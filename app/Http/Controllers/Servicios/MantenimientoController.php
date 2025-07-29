<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Servicios\Mantenimiento;
use App\Models\Servicios\Inventario;
use App\Models\Servicios\EncargadoMantenimiento;

class MantenimientoController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            // CAMBIO AQUÍ: Volver a 'id_inventario' y 'id_encargado_man'
            'id_inventario' => 'required|integer|exists:inventarios,id_inventario',
            'id_encargado_man' => 'required|integer|exists:encargados_mantenimiento,id_encargado_man',
            'fecha' => 'required|date',
            'tipo' => 'required|in:preventivo,correctivo',
            'refacciones_material' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        Mantenimiento::create($validatedData);

        return redirect()->route('servicios.reportes')->with('success', 'Reporte de mantenimiento guardado exitosamente.');
    }

    public function index(Request $request)
    {
        // ... (Tu código index() se mantiene igual) ...
        $search_query = $request->input('search');

        $reportes = Mantenimiento::with(['Inventario.equipo', 'EncargadoMantenimiento'])
            ->when($search_query, function ($query, $search) {
                $query->where('tipo', 'like', "%{$search}%")
                    ->orWhereHas('inventario.equipo', function ($q) use ($search) {
                        $q->where('nombre', 'like', "%{$search}%");
                    })
                    ->orWhereHas('EncargadoMantenimiento', function ($q) use ($search) {
                        $q->where('nombre', 'like', "%{$search}%")
                            ->orWhere('apellidos', 'like', "%{$search}%");
                    });
            })
            ->orderBy('fecha', 'desc')
            ->paginate(10);

        $equipos_inventario = Inventario::with('equipo')
            ->where('status', '!=', 'proceso de baja')
            ->get();

        $encargados = EncargadoMantenimiento::orderBy('nombre')->get();

        return view('servicios.reportes', compact('reportes', 'search_query', 'equipos_inventario', 'encargados'));
    }
}
