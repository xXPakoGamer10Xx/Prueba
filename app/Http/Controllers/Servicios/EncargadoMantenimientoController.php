<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Servicios\EncargadoMantenimiento; // Se importa el Modelo

class EncargadoMantenimientoController extends Controller
{
    /**
     * Almacena un nuevo encargado de mantenimiento.
     */
    public function store(Request $request)
    {
        // Validación de los datos del modal
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'cargo' => 'required|string|max:100',
            'contacto' => 'nullable|string|max:100',
        ]);

        // Se usa el método create() del Modelo Eloquent, que es más seguro
        EncargadoMantenimiento::create($validatedData);

        // Redirige a la página de reportes, que es donde se abrió el modal
        return redirect()->route('servicios.reportes')->with('success', 'Encargado registrado exitosamente.');
    }
}
