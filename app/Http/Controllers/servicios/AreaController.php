<?php


namespace App\Http\Controllers\servicios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\servicios\Area;
use App\Models\servicios\EncargadoArea;

class AreaController extends Controller
{
    public function index(Request $request)
    {
        // Lógica para Áreas (búsqueda y paginación)
        $searchAreas = $request->input('search_a');
        $areas = Area::with('encargado')
            ->when($searchAreas, function ($query, $search) {
                return $query->where('nombre', 'like', "%{$search}%")
                            ->orWhereHas('encargado', function ($q) use ($search) {
                                $q->where('nombre', 'like', "%{$search}%")
                                ->orWhere('apellidos', 'like', "%{$search}%");
                            });
            })
            ->orderBy('nombre')
            ->paginate(5, ['*'], 'pagina_a');

        // Lógica para Encargados (búsqueda y paginación)
        $searchEncargados = $request->input('search_e');
        $encargados = EncargadoArea::query()
            ->when($searchEncargados, function ($query, $search) {
                return $query->where('nombre', 'like', "%{$search}%")
                            ->orWhere('apellidos', 'like', "%{$search}%")
                            ->orWhere('cargo', 'like', "%{$search}%");
            })
            ->orderBy('apellidos')
            ->paginate(5, ['*'], 'pagina_e');

        $listaEncargados = EncargadoArea::orderBy('nombre')->get();
        
        // Renderiza la vista y le pasa los datos
        return view('servicios.areas.index', compact('areas', 'encargados', 'listaEncargados', 'searchAreas', 'searchEncargados'));
    }

    // --- MÉTODOS PARA ÁREAS ---
    public function storeArea(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:100', 'id_encargado_area_fk' => 'required|exists:encargados_area,id_encargado_area']);
        Area::create($request->all());
        return redirect()->route('servicios.areas.index')->with('mensaje', 'Área agregada correctamente.')->with('mensaje_tipo', 'success');
    }

    public function updateArea(Request $request, Area $area)
    {
        $request->validate(['nombre' => 'required|string|max:100', 'id_encargado_area_fk' => 'required|exists:encargados_area,id_encargado_area']);
        $area->update($request->all());
        return redirect()->route('servicios.areas.index')->with('mensaje', 'Área actualizada correctamente.')->with('mensaje_tipo', 'warning');
    }

    public function destroyArea(Area $area)
    {
        $area->delete();
        return redirect()->route('servicios.areas.index')->with('mensaje', 'Área eliminada correctamente.')->with('mensaje_tipo', 'danger');
    }

    // --- MÉTODOS PARA ENCARGADOS ---
    public function storeEncargado(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:100', 'apellidos' => 'required|string|max:100', 'cargo' => 'required|string|max:100']);
        EncargadoArea::create($request->all());
        return redirect()->route('servicios.areas.index', ['#lista-encargados'])->with('mensaje', 'Encargado agregado.')->with('mensaje_tipo', 'success');
    }
    
    public function updateEncargado(Request $request, EncargadoArea $encargado)
    {
        $request->validate(['nombre' => 'required|string|max:100', 'apellidos' => 'required|string|max:100', 'cargo' => 'required|string|max:100']);
        $encargado->update($request->all());
        return redirect()->route('servicios.areas.index', ['#lista-encargados'])->with('mensaje', 'Encargado actualizado.')->with('mensaje_tipo', 'warning');
    }

    public function destroyEncargado(EncargadoArea $encargado)
    {
        if ($encargado->areas()->count() > 0) {
            return redirect()->route('servicios.areas.index', ['#lista-encargados'])->with('mensaje', 'Este encargado tiene áreas asignadas y no se puede eliminar.')->with('mensaje_tipo', 'danger');
        }
        $encargado->delete();
        return redirect()->route('servicios.areas.index', ['#lista-encargados'])->with('mensaje', 'Encargado eliminado.')->with('mensaje_tipo', 'danger');
    }
}