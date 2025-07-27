<?php

namespace App\Livewire\Servicios;

use App\Models\servicios\Equipo;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Componente de Livewire para gestionar la tabla de Equipos.
 */
class GestionarEquipos extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // Propiedades para el modal
    public $isModalOpen = false;
    public $isEditMode = false;
    public $equipoId;
    public $nombre;
    public $marca;
    public $modelo;
    public $cantidad;
    public $frecuencia_mantenimiento;

    protected $rules = [
        'nombre' => 'required|string|max:100',
        'marca' => 'nullable|string|max:50',
        'modelo' => 'nullable|string|max:50',
        'cantidad' => 'required|integer|min:1',
        'frecuencia_mantenimiento' => 'required|integer|min:1',
    ];

    protected $messages = [
        'nombre.required' => 'El nombre del equipo es obligatorio.',
        'nombre.max' => 'El nombre no puede exceder 100 caracteres.',
        'marca.max' => 'La marca no puede exceder 50 caracteres.',
        'modelo.max' => 'El modelo no puede exceder 50 caracteres.',
        'cantidad.required' => 'La cantidad es obligatoria.',
        'cantidad.integer' => 'La cantidad debe ser un número entero.',
        'cantidad.min' => 'La cantidad debe ser al menos 1.',
        'frecuencia_mantenimiento.required' => 'La frecuencia de mantenimiento es obligatoria.',
        'frecuencia_mantenimiento.integer' => 'La frecuencia debe ser un número entero.',
        'frecuencia_mantenimiento.min' => 'La frecuencia debe ser al menos 1 mes.',
    ];

    public function create()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $equipo = Equipo::findOrFail($id);
        $this->equipoId = $id;
        $this->nombre = $equipo->nombre;
        $this->marca = $equipo->marca;
        $this->modelo = $equipo->modelo;
        $this->cantidad = $equipo->cantidad;
        $this->frecuencia_mantenimiento = $equipo->frecuencia_mantenimiento;
        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate();

        if ($this->isEditMode) {
            Equipo::where('id_equipo', $this->equipoId)->update([
                'nombre' => $this->nombre,
                'marca' => $this->marca,
                'modelo' => $this->modelo,
                'cantidad' => $this->cantidad,
                'frecuencia_mantenimiento' => $this->frecuencia_mantenimiento,
            ]);
            session()->flash('message', 'Equipo actualizado exitosamente.');
        } else {
            Equipo::create([
                'nombre' => $this->nombre,
                'marca' => $this->marca,
                'modelo' => $this->modelo,
                'cantidad' => $this->cantidad,
                'frecuencia_mantenimiento' => $this->frecuencia_mantenimiento,
            ]);
            session()->flash('message', 'Equipo creado exitosamente.');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        Equipo::find($id)->delete();
        session()->flash('message', 'Equipo eliminado exitosamente.');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['equipoId', 'nombre', 'marca', 'modelo', 'cantidad', 'frecuencia_mantenimiento']);
    }

    public function render()
    {
        return view('livewire.servicios.gestionar-equipos', [
            'equipos' => Equipo::paginate(10),
        ]);
    }
}