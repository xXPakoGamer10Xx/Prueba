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
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            // CAMBIO CRÍTICO AQUÍ: Las reglas de validación deben usar los nombres de columna de la DB
            'id_inventario_fk' => 'required|integer|exists:inventarios,id_inventario', // 'exists' usa la clave primaria de la tabla referenciada
            'id_mantenimiento_fk' => 'nullable|integer|exists:mantenimientos,id_mantenimiento',
            'estado' => 'required|in:en proceso,baja completa,cancelada',
            'motivo' => 'required|string',
        ]);

        // ELIMINAR ESTAS LÍNEAS: Ya no son necesarias si la validación usa los nombres correctos
        // $validatedData['id_inventario_fk'] = $validatedData['id_inventario'];
        // unset($validatedData['id_inventario']);
        // $validatedData['id_mantenimiento_fk'] = $validatedData['id_mantenimiento'];
        // unset($validatedData['id_mantenimiento']);

        DB::beginTransaction();
        try {
            // Se crea el registro directamente con los datos validados, que ya tienen las claves _fk
            ProcesoBaja::create($validatedData);

            // IMPORTANTE: Aquí también debes usar $request->id_inventario_fk
            if ($request->estado === 'baja completa') {
                $inventario = Inventario::find($request->id_inventario_fk); // CAMBIO AQUÍ
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

        $bajas = ProcesoBaja::with('inventario.equipo') // La relación inventario() en ProcesoBaja ya usa id_inventario_fk
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
