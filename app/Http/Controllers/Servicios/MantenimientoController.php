<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Servicios\Mantenimiento; // Importar el modelo Mantenimiento
use App\Models\Servicios\Inventario; // Importar el modelo Inventario
use App\Models\Servicios\EncargadoMantenimiento; // Importar el modelo EncargadoMantenimiento
use Illuminate\Support\Facades\Log; // Para registrar errores

class MantenimientoController extends Controller
{
    /**
     * Almacena un nuevo reporte de mantenimiento en la base de datos.
     * Realiza validación y maneja la creación del registro.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP entrante.
     * @return \Illuminate\Http\RedirectResponse Redirecciona a la página de reportes con un mensaje flash.
     */
    public function store(Request $request)
    {
        // Validación de los datos del formulario.
        // IMPORTANTE: Las reglas de validación deben usar los nombres de columna de la DB
        // que son 'id_inventario_fk' y 'id_encargado_man_fk'.
        $validatedData = $request->validate([
            'id_inventario_fk' => 'required|integer|exists:inventarios,id_inventario', // 'exists' verifica que el ID exista
            'id_encargado_man_fk' => 'required|integer|exists:encargados_mantenimiento,id_encargado_man', // 'exists' verifica que el ID exista
            'fecha' => 'required|date',
            'tipo' => 'required|in:preventivo,correctivo',
            'refacciones_material' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        try {
            // Crea el registro de Mantenimiento directamente con los datos validados.
            // Los nombres de las claves foráneas ya son correctos en $validatedData.
            Mantenimiento::create($validatedData);

            // Redirige a la ruta 'servicios.reportes' con un mensaje de éxito.
            return redirect()->route('servicios.reportes')->with('success', 'Reporte de mantenimiento guardado exitosamente.');

        } catch (\Exception $e) {
            // En caso de error, registrarlo y redirigir de vuelta con un mensaje de error.
            Log::error('Error al guardar mantenimiento: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al guardar el reporte de mantenimiento.')->withInput();
        }
    }

    /**
     * Muestra una lista de reportes de mantenimiento, con funcionalidad de búsqueda y paginación.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP entrante.
     * @return \Illuminate\View\View La vista con los reportes paginados y datos relacionados.
     */
    public function index(Request $request)
    {
        $search_query = $request->input('search'); // Obtiene el término de búsqueda

        // Obtener los reportes de mantenimiento con sus relaciones (inventario.equipo y encargadoMantenimiento)
        $reportes = Mantenimiento::with(['inventario.equipo', 'encargadoMantenimiento'])
            // Aplicar filtro de búsqueda si $search_query no está vacío
            ->when($search_query, function ($query, $search) {
                $query->where('tipo', 'like', "%{$search}%")
                    // Buscar por nombre de equipo relacionado
                    ->orWhereHas('inventario.equipo', function ($q) use ($search) {
                        $q->where('nombre', 'like', "%{$search}%");
                    })
                    // Buscar por nombre o apellido del encargado de mantenimiento
                    ->orWhereHas('encargadoMantenimiento', function ($q) use ($search) {
                        $q->where('nombre', 'like', "%{$search}%")
                            ->orWhere('apellidos', 'like', "%{$search}%");
                    });
            })
            ->orderBy('fecha', 'desc') // Ordenar por fecha de forma descendente
            ->paginate(10); // Paginar los resultados

        // Obtener equipos del inventario que no estén en 'proceso de baja' para el select del formulario (si se usa en la vista)
        $equipos_inventario = Inventario::with('equipo')
            ->where('status', '!=', 'proceso de baja')
            ->get();

        // Obtener todos los encargados de mantenimiento (para el select del formulario, si se usa en la vista)
        $encargados = EncargadoMantenimiento::orderBy('nombre')->get();

        // Renderiza la vista 'servicios.reportes' y le pasa los datos.
        return view('servicios.reportes', compact('reportes', 'search_query', 'equipos_inventario', 'encargados'));
    }

    // Si necesitaras métodos para editar, actualizar o eliminar mantenimientos,
    // se añadirían aquí, siguiendo patrones similares a otros controladores.
    // public function edit(Mantenimiento $mantenimiento) { ... }
    // public function update(Request $request, Mantenimiento $mantenimiento) { ... }
    // public function destroy(Mantenimiento $mantenimiento) { ... }
}
