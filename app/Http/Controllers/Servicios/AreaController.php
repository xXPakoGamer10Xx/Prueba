<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Servicios\Area;
use App\Models\Servicios\EncargadoArea;

class AreaController extends Controller
{
    /**
     * Muestra una lista de áreas y encargados de área, con funcionalidad de búsqueda y paginación.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // --- Lógica para Áreas (búsqueda y paginación) ---
        $searchAreas = $request->input('search_a'); // Obtiene el término de búsqueda para áreas

        $areas = Area::with('encargado') // Carga eager loading la relación 'encargado' para evitar N+1 queries
            ->when($searchAreas, function ($query, $search) {
                // Aplica el filtro de búsqueda si existe un término
                return $query->where('nombre', 'like', "%{$search}%")
                             ->orWhereHas('encargado', function ($q) use ($search) {
                                 // Busca en el nombre o apellidos del encargado relacionado
                                 $q->where('nombre', 'like', "%{$search}%")
                                   ->orWhere('apellidos', 'like', "%{$search}%");
                             });
            })
            ->orderBy('nombre') // Ordena los resultados por el nombre del área
            ->paginate(5, ['*'], 'pagina_a'); // Pagina los resultados, usando 'pagina_a' para evitar conflictos con otra paginación

        // --- Lógica para Encargados (búsqueda y paginación) ---
        $searchEncargados = $request->input('search_e'); // Obtiene el término de búsqueda para encargados

        $encargados = EncargadoArea::query()
            ->when($searchEncargados, function ($query, $search) {
                // Aplica el filtro de búsqueda si existe un término
                return $query->where('nombre', 'like', "%{$search}%")
                             ->orWhere('apellidos', 'like', "%{$search}%")
                             ->orWhere('cargo', 'like', "%{$search}%");
            })
            ->orderBy('apellidos') // Ordena los resultados por los apellidos del encargado
            ->paginate(5, ['*'], 'pagina_e'); // Pagina los resultados, usando 'pagina_e' para evitar conflictos

        // Obtiene una lista completa de encargados para usar en un select (ej. para asignar un encargado a un área)
        $listaEncargados = EncargadoArea::orderBy('nombre')->get();

        // Renderiza la vista y le pasa todos los datos necesarios
        return view('servicios.areas.index', compact('areas', 'encargados', 'listaEncargados', 'searchAreas', 'searchEncargados'));
    }

    // --- MÉTODOS CRUD PARA ÁREAS ---

    /**
     * Almacena un nuevo registro de área en la base de datos.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeArea(Request $request)
    {
        // Valida los datos de la solicitud.
        $request->validate([
            'nombre' => 'required|string|max:100',
            'id_encargado_area_fk' => 'required|exists:encargados_area,id_encargado_area'
        ]);

        // Crea un nuevo registro de Área con los datos validados.
        Area::create($request->all());

        // Redirige de vuelta al índice con un mensaje de éxito.
        return redirect()->route('servicios.areas.index')->with('mensaje', 'Área agregada correctamente.')->with('mensaje_tipo', 'success');
    }

    /**
     * Actualiza un registro de área existente en la base de datos.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Servicios\Area $area Inyección de modelo para el área a actualizar.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateArea(Request $request, Area $area)
    {
        // Valida los datos de la solicitud.
        $request->validate([
            'nombre' => 'required|string|max:100',
            'id_encargado_area_fk' => 'required|exists:encargados_area,id_encargado_area'
        ]);

        // Actualiza el registro del área con los datos validados.
        $area->update($request->all());

        // Redirige de vuelta al índice con un mensaje de éxito.
        return redirect()->route('servicios.areas.index')->with('mensaje', 'Área actualizada correctamente.')->with('mensaje_tipo', 'warning');
    }

    /**
     * Elimina un registro de área de la base de datos.
     *
     * @param \App\Models\Servicios\Area $area Inyección de modelo para el área a eliminar.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyArea(Area $area)
    {
        // Considerar añadir una verificación para ver si el área tiene inventarios asociados
        // antes de eliminarla, para mantener la integridad referencial.
        // Ejemplo:
        // if ($area->inventarios()->count() > 0) {
        //     return redirect()->route('servicios.areas.index')->with('mensaje', 'No se puede eliminar. El área tiene equipos en inventario asignados.')->with('mensaje_tipo', 'danger');
        // }

        $area->delete(); // Elimina el registro del área.

        // Redirige de vuelta al índice con un mensaje de éxito.
        return redirect()->route('servicios.areas.index')->with('mensaje', 'Área eliminada correctamente.')->with('mensaje_tipo', 'danger');
    }

    // --- MÉTODOS CRUD PARA ENCARGADOS ---

    /**
     * Almacena un nuevo registro de encargado de área en la base de datos.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeEncargado(Request $request)
    {
        // Valida los datos de la solicitud.
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'cargo' => 'required|string|max:100',
            'contacto' => 'nullable|string|max:100', // Añadido 'contacto' y 'nullable' según el modelo
        ]);

        // Crea un nuevo registro de EncargadoArea con los datos validados.
        EncargadoArea::create($request->all());

        // Redirige de vuelta al índice con un mensaje de éxito, anclando a la sección de encargados.
        return redirect()->route('servicios.areas.index', ['#lista-encargados'])->with('mensaje', 'Encargado agregado correctamente.')->with('mensaje_tipo', 'success');
    }

    /**
     * Actualiza un registro de encargado de área existente en la base de datos.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Servicios\EncargadoArea $encargado Inyección de modelo para el encargado a actualizar.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateEncargado(Request $request, EncargadoArea $encargado)
    {
        // Valida los datos de la solicitud.
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'cargo' => 'required|string|max:100',
            'contacto' => 'nullable|string|max:100', // Añadido 'contacto' y 'nullable' según el modelo
        ]);

        // Actualiza el registro del encargado con los datos validados.
        $encargado->update($request->all());

        // Redirige de vuelta al índice con un mensaje de éxito, anclando a la sección de encargados.
        return redirect()->route('servicios.areas.index', ['#lista-encargados'])->with('mensaje', 'Encargado actualizado correctamente.')->with('mensaje_tipo', 'warning');
    }

    /**
     * Elimina un registro de encargado de área de la base de datos.
     * Realiza una verificación para evitar eliminar encargados con áreas asignadas.
     *
     * @param \App\Models\Servicios\EncargadoArea $encargado Inyección de modelo para el encargado a eliminar.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyEncargado(EncargadoArea $encargado)
    {
        // Verifica si el encargado tiene áreas asignadas antes de intentar eliminarlo.
        if ($encargado->areas()->count() > 0) {
            // Si tiene áreas, redirige con un mensaje de error.
            return redirect()->route('servicios.areas.index', ['#lista-encargados'])->with('mensaje', 'Este encargado tiene áreas asignadas y no se puede eliminar.')->with('mensaje_tipo', 'danger');
        }

        $encargado->delete(); // Elimina el registro del encargado.

        // Redirige de vuelta al índice con un mensaje de éxito.
        return redirect()->route('servicios.areas.index', ['#lista-encargados'])->with('mensaje', 'Encargado eliminado correctamente.')->with('mensaje_tipo', 'danger');
    }
}
