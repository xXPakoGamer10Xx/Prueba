<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// --- LÍNEAS CORREGIDAS: Se añade 'Servicios' a la ruta de los modelos ---
use App\Models\Servicios\ProcesoBaja;
use App\Models\Servicios\Inventario;
use App\Models\Servicios\Mantenimiento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BajaController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_inventario' => 'required|integer|exists:inventarios,id_inventario',
            'id_mantenimiento' => 'nullable|integer|exists:mantenimientos,id_mantenimiento',
            'estado' => 'required|in:en proceso,baja completa,cancelada',
            'motivo' => 'required|string',
        ]);
        
        $validatedData['id_inventario_fk'] = $validatedData['id_inventario'];
        unset($validatedData['id_inventario']);
        
        $validatedData['id_mantenimiento_fk'] = $validatedData['id_mantenimiento'];
        unset($validatedData['id_mantenimiento']);

        DB::beginTransaction();
        try {
            ProcesoBaja::create($validatedData);

            if ($request->estado === 'baja completa') {
                $inventario = Inventario::find($request->id_inventario);
                $inventario->status = 'baja';
                $inventario->save();
            }

            DB::commit();
            return redirect()->route('servicios.bajas.historial')->with('success', 'Baja registrada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar baja: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al procesar la solicitud.')->withInput();
        }
    }

    public function index(Request $request)
    {
        $search_query = $request->input('search');

        $bajas = ProcesoBaja::with('inventario.equipo')
            ->when($search_query, function ($query, $search) {
                $query->where('motivo', 'like', "%{$search}%")
                    ->orWhere('estado', 'like', "%{$search}%")
                    ->orWhereHas('inventario.equipo', function ($q) use ($search) {
                        $q->where('nombre', 'like', "%{$search}%");
                    })
                    ->orWhereHas('inventario', function ($q) use ($search) {
                        $q->where('num_serie', 'like', "%{$search}%");
                    });
            })
            ->orderBy('id_proceso_baja', 'desc')
            ->paginate(10);

        $equipos_inventario = Inventario::with('equipo')->get();
        $mantenimientos = Mantenimiento::orderBy('fecha', 'desc')->get();

        return view('servicios.historial_bajas', compact('bajas', 'search_query', 'equipos_inventario', 'mantenimientos'));
    }
}