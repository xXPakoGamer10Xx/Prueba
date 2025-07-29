<?php

namespace App\Http\Controllers\Servicios; // <-- RUTA CORREGIDA

use App\Http\Controllers\Controller; // <-- RUTA AÑADIDA
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EncargadoMantenimientoController extends Controller
{
    /**
     * Almacena un nuevo encargado de mantenimiento.
     */
    public function store(Request $request)
    {
        // Validación de los datos del modal
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'cargo' => 'required|string|max:100',
            'contacto' => 'nullable|string|max:100',
        ]);

        // Insertar el nuevo encargado
        DB::table('encargados_mantenimiento')->insert([
            'nombre' => $request->nombre,
            'apellidos' => $request->apellidos,
            'cargo' => $request->cargo,
            'contacto' => $request->contacto,
        ]);

        // Redirigir de vuelta al formulario de mantenimiento con un mensaje de éxito
        return redirect()->route('servicios.mantenimiento')->with('success', 'Encargado registrado exitosamente.');
    }
}