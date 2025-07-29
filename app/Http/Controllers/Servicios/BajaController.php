<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Para registrar errores

class BajaController extends Controller
{
    /**
     * Muestra el formulario para crear un nuevo reporte de baja.
     */
    public function create()
    {
        $equipos_inventario = DB::table('inventarios as i')
            ->join('equipos as e', 'i.id_equipo_fk', '=', 'e.id_equipo')
            ->select('i.id_inventario', 'i.num_serie', 'e.nombre as nombre_equipo')
            ->orderBy('e.nombre')
            ->get();

        $mantenimientos = DB::table('mantenimientos')
            ->select('id_mantenimiento', 'fecha', 'tipo', 'id_inventario')
            ->orderBy('fecha', 'desc')
            ->get();

        return view('servicios.bajas', [
            'equipos_inventario' => $equipos_inventario,
            'mantenimientos' => $mantenimientos,
        ]);
    }

    /**
     * Almacena un nuevo reporte de baja en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_inventario' => 'required|integer|exists:inventarios,id_inventario',
            'id_mantenimiento' => 'nullable|integer|exists:mantenimientos,id_mantenimiento',
            'estado' => 'required|in:en proceso,baja completa,cancelada',
            'motivo' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // Insertar en la tabla de procesos_baja
            DB::table('procesos_baja')->insert([
                'id_inventario_fk' => $request->id_inventario,
                'id_mantenimiento_fk' => $request->id_mantenimiento,
                'motivo' => $request->motivo,
                'estado' => $request->estado,
            ]);

            // Si el estado es "baja completa", actualizamos el estado en la tabla de inventarios
            if ($request->estado === 'baja completa') {
                DB::table('inventarios')
                    ->where('id_inventario', $request->id_inventario)
                    ->update(['status' => 'baja']); // Asegúrate que 'baja' sea un valor válido en tu ENUM de inventarios.
            }

            DB::commit();
            return redirect()->route('servicios.bajas.historial')->with('success', 'Baja registrada y estado de inventario actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar baja: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al procesar la solicitud. Inténtalo de nuevo.')->withInput();
        }
    }

    /**
     * Muestra el historial de bajas.
     */
    public function index(Request $request)
    {
        $search_query = $request->input('search');

        $bajas = DB::table('procesos_baja as pb')
            ->leftJoin('inventarios as i', 'pb.id_inventario_fk', '=', 'i.id_inventario')
            ->leftJoin('equipos as e', 'i.id_equipo_fk', '=', 'e.id_equipo')
            ->select(
                'pb.id_proceso_baja', 'pb.motivo', 'pb.estado',
                'i.num_serie', 'e.nombre as nombre_equipo'
            )
            ->when($search_query, function ($query, $search) {
                return $query->where('e.nombre', 'like', "%{$search}%")
                             ->orWhere('i.num_serie', 'like', "%{$search}%")
                             ->orWhere('pb.motivo', 'like', "%{$search}%")
                             ->orWhere('pb.estado', 'like', "%{$search}%");
            })
            ->orderBy('pb.id_proceso_baja', 'desc')
            ->paginate(10);

        return view('servicios.historial_bajas', compact('bajas', 'search_query'));
    }
}