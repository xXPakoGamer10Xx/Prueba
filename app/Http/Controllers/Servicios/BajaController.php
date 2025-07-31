<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Servicios\ProcesoBaja;
use App\Models\Servicios\Inventario;
use App\Models\Servicios\Mantenimiento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BajaController extends Controller
{
    /**
     * Almacena un nuevo proceso de baja en la base de datos.
     * Realiza validación, maneja transacciones y actualiza el estado del inventario.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Valida los datos de la solicitud.
        // Se utilizan los nombres de columna de la base de datos (con _fk) en las reglas de validación.
        $validatedData = $request->validate([
            'id_inventario_fk' => 'required|integer|exists:inventarios,id_inventario', // 'exists' verifica que el ID exista en la tabla 'inventarios'
            'id_mantenimiento_fk' => 'nullable|integer|exists:mantenimientos,id_mantenimiento', // Puede ser nulo
            'estado' => 'required|in:en proceso,baja completa,cancelado',
            'motivo' => 'required|string',
            'fecha_baja' => 'required|date', // Asegurarse de que fecha_baja sea requerida
        ]);

        // Iniciar una transacción de base de datos para asegurar la atomicidad.
        // Si algo falla dentro del try, se hará un rollback.
        DB::beginTransaction();
        try {
            // Crear el registro de ProcesoBaja directamente con los datos validados.
            // Los nombres de las claves foráneas ya son correctos en $validatedData.
            ProcesoBaja::create($validatedData);

            // Actualizar el estado del inventario asociado si la baja es 'baja completa'.
            if ($request->estado === 'baja completa') {
                $inventario = Inventario::find($request->id_inventario_fk); // Usar id_inventario_fk
                if ($inventario) { // Asegurarse de que el inventario exista
                    $inventario->status = 'baja'; // Cambiar el estado del inventario a 'baja'
                    $inventario->save(); // Guardar los cambios en el inventario
                }
            } elseif ($request->estado === 'en proceso') {
                $inventario = Inventario::find($request->id_inventario_fk);
                if ($inventario) {
                    $inventario->status = 'proceso de baja';
                    $inventario->save();
                }
            } elseif ($request->estado === 'cancelado') {
                $inventario = Inventario::find($request->id_inventario_fk);
                if ($inventario && ($inventario->status === 'proceso de baja' || $inventario->status === 'baja')) {
                    $inventario->status = 'funcionando'; // Revertir a funcionando si se cancela
                    $inventario->save();
                }
            }


            DB::commit(); // Confirmar la transacción si todo fue exitoso.
            // Redirigir a la ruta de historial de bajas con un mensaje de éxito.
            return redirect()->route('servicios.bajas.historial')->with('success', 'Baja registrada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack(); // Revertir la transacción en caso de error.
            Log::error('Error al registrar baja: ' . $e->getMessage()); // Registrar el error para depuración.
            // Redirigir de vuelta con un mensaje de error y los datos de entrada para que el usuario no los pierda.
            return redirect()->back()->with('error', 'Ocurrió un error al procesar la solicitud. Por favor, inténtalo de nuevo.')->withInput();
        }
    }

    /**
     * Muestra el historial de procesos de baja, con funcionalidad de búsqueda y paginación.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search_query = $request->input('search'); // Obtiene el término de búsqueda

        // Obtener los procesos de baja con sus relaciones (inventario y equipo)
        $bajas = ProcesoBaja::with('inventario.equipo') // Carga eager loading las relaciones anidadas
            ->when($search_query, function ($query, $search) {
                // Aplica el filtro de búsqueda si existe un término
                $query->where('motivo', 'like', "%{$search}%")
                      ->orWhere('estado', 'like', "%{$search}%")
                      // Buscar por nombre de equipo relacionado
                      ->orWhereHas('inventario.equipo', function ($q) use ($search) {
                          $q->where('nombre', 'like', "%{$search}%");
                      })
                      // Buscar por número de serie del inventario
                      ->orWhereHas('inventario', function ($q) use ($search) {
                          $q->where('num_serie', 'like', "%{$search}%");
                      });
            })
            ->orderBy('id_proceso_baja', 'desc') // Ordenar por ID de baja de forma descendente
            ->paginate(10); // Paginar los resultados

        // Obtener todos los equipos de inventario (para selects o listas en la vista)
        $equipos_inventario = Inventario::with('equipo')->get();
        // Obtener todos los mantenimientos (para selects o listas en la vista)
        $mantenimientos = Mantenimiento::orderBy('fecha', 'desc')->get();

        // Renderiza la vista 'servicios.historial_bajas' y le pasa los datos.
        return view('servicios.historial_bajas', compact('bajas', 'search_query', 'equipos_inventario', 'mantenimientos'));
    }
}
