<?php

namespace App\Http\Controllers\Servicios; // <-- RUTA CORREGIDA

use App\Http\Controllers\Controller; // <-- RUTA AÑADIDA
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MantenimientoController extends Controller
{
    /**
     * Muestra el formulario para crear un nuevo reporte de mantenimiento.
     */
    public function create()
    {
        // Obtener equipos del inventario que no estén en proceso de baja
        $equipos_inventario = DB::table('inventarios as i')
            ->join('equipos as e', 'i.id_equipo_fk', '=', 'e.id_equipo')
            ->where('i.status', '!=', 'proceso de baja')
            ->select('i.id_inventario', 'i.num_serie', 'e.nombre')
            ->orderBy('e.nombre')
            ->get();

        // Obtener la lista de encargados de mantenimiento
        $encargados = DB::table('encargados_mantenimiento')
            ->select('id_encargado_man', 'nombre', 'apellidos')
            ->orderBy('nombre')
            ->get();

        return view('servicios.mantenimiento', [
            'equipos_inventario' => $equipos_inventario,
            'encargados' => $encargados,
        ]);
    }

    /**
     * Almacena un nuevo reporte de mantenimiento en la base de datos.
     */
    public function store(Request $request)
    {
        // Validación de los datos del formulario
        $request->validate([
            'id_inventario' => 'required|integer|exists:inventarios,id_inventario',
            'id_encargado_man' => 'required|integer|exists:encargados_mantenimiento,id_encargado_man',
            'fecha' => 'required|date',
            'tipo' => 'required|in:preventivo,correctivo',
            'refacciones_material' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        // Insertar el nuevo reporte de mantenimiento
        DB::table('mantenimientos')->insert([
            'id_inventario' => $request->id_inventario,
            'id_encargado_man' => $request->id_encargado_man,
            'fecha' => $request->fecha,
            'tipo' => $request->tipo,
            'refacciones_material' => $request->refacciones_material,
            'observaciones' => $request->observaciones,
        ]);

        // Redirigir a la vista de reportes con un mensaje de éxito
        return redirect()->route('servicios.reportes')->with('success', 'Reporte de mantenimiento guardado exitosamente.');
    }

    /**
     * Muestra la lista de reportes de mantenimiento.
     */
    public function index(Request $request)
    {
        $search_query = $request->input('search');

        $reportes = DB::table('mantenimientos as m')
            ->join('encargados_mantenimiento as em', 'm.id_encargado_man', '=', 'em.id_encargado_man')
            ->leftJoin('inventarios as i', 'm.id_inventario', '=', 'i.id_inventario')
            ->leftJoin('equipos as e', 'i.id_equipo_fk', '=', 'e.id_equipo')
            ->select(
                'm.id_mantenimiento', 'm.fecha', 'm.tipo', 'm.refacciones_material', 'm.observaciones',
                'i.num_serie', 'e.nombre AS nombre_equipo',
                DB::raw("CONCAT(em.nombre, ' ', em.apellidos) AS encargado"), 'em.cargo'
            )
            ->when($search_query, function ($query, $search) {
                return $query->where('e.nombre', 'like', "%{$search}%")
                    ->orWhere(DB::raw("CONCAT(em.nombre, ' ', em.apellidos)"), 'like', "%{$search}%")
                    ->orWhere('m.tipo', 'like', "%{$search}%");
            })
            ->orderBy('m.fecha', 'desc')
            ->orderBy('m.id_mantenimiento', 'desc')
            ->paginate(10);

        return view('servicios.reportes', compact('reportes', 'search_query'));
    }
}