<?php

namespace App\Http\Controllers\Ginecologia;

use App\Http\Controllers\Controller;
use App\Models\Ginecologia\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    /**
     * Muestra una lista de los materiales, opcionalmente filtrada por búsqueda.
     */
    public function index(Request $request)
    {
        // CAMBIO APLICADO AQUÍ
        $searchTerm = $request->input('search');
        $query = Material::query();

        if ($searchTerm) {
            $query->where('nombre_material', 'like', '%' . $searchTerm . '%');
        }

        $materiales = $query->paginate(10);
        
        return view('ginecologia.material', compact('materiales', 'searchTerm'));
    }

    /**
     * Almacena un nuevo material en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_material' => 'required|string|max:10|unique:materiales,id_material',
            'nombre' => 'required|string|max:100',
            'cantidad' => 'required|integer|min:0',
        ]);

        Material::create([
            'id_material'       => $request->id_material,
            'nombre_material'   => $request->nombre,
            'cantidad_material' => $request->cantidad,
        ]);

        return redirect()->route('material.index')->with('success', 'Material agregado exitosamente.');
    }

    /**
     * Actualiza un material específico.
     */
    public function update(Request $request, Material $material)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'cantidad' => 'required|integer|min:0',
        ]);

        $material->update([
            'nombre_material' => $request->nombre,
            'cantidad_material' => $request->cantidad,
        ]);

        return redirect()->route('material.index')->with('success', 'Material actualizado exitosamente.');
    }

    /**
     * Elimina un material específico.
     */
    public function destroy(Material $material)
    {
        $material->delete();
        return redirect()->route('material.index')->with('success', 'Material eliminado exitosamente.');
    }
}