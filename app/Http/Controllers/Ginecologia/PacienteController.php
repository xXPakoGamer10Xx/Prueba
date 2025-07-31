<?php

namespace App\Http\Controllers\Ginecologia;

use App\Http\Controllers\Controller;
use App\Models\Ginecologia\Paciente;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    /**
     * Muestra una lista de los pacientes.
     */
    public function index(Request $request)
    {
        $searchTerm = $request->input('search');
        $query = Paciente::query();

        if ($searchTerm) {
            // Busca en nombre y apellidos
            $query->where(function($q) use ($searchTerm) {
                $q->where('nombre_paciente', 'like', '%' . $searchTerm . '%')
                  ->orWhere('apellido1_paciente', 'like', '%' . $searchTerm . '%')
                  ->orWhere('apellido2_paciente', 'like', '%' . $searchTerm . '%');
            });
        }
        
        $pacientes = $query->orderBy('apellido1_paciente')->paginate(10);
        return view('ginecologia.expediente', compact('pacientes', 'searchTerm'));
    }

    /**
     * Almacena un nuevo paciente.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_paciente' => 'required|string|max:10|unique:pacientes,id_paciente',
            'nombre_paciente' => 'required|string|max:40',
            'apellido1_paciente' => 'nullable|string|max:30',
            'apellido2_paciente' => 'nullable|string|max:30',
            'fecha_nac' => 'nullable|date',
            'genero_paciente' => 'required|string|max:20',
        ]);

        Paciente::create($request->all());

        return redirect()->route('expediente.index')->with('success', 'Paciente registrado exitosamente.');
    }

    /**
     * Actualiza un paciente específico.
     */
    public function update(Request $request, Paciente $expediente)
    {
        $request->validate([
            'nombre_paciente' => 'required|string|max:40',
            'apellido1_paciente' => 'nullable|string|max:30',
            'apellido2_paciente' => 'nullable|string|max:30',
            'fecha_nac' => 'nullable|date',
            'genero_paciente' => 'required|string|max:20',
        ]);

        $expediente->update($request->all());

        return redirect()->route('expediente.index')->with('success', 'Paciente actualizado exitosamente.');
    }

    /**
     * Elimina un paciente.
     */
    public function destroy(Paciente $expediente)
    {
        $expediente->delete();
        return redirect()->route('expediente.index')->with('success', 'Paciente eliminado exitosamente.');
    }
}