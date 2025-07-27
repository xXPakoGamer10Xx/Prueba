<?php

namespace App\Livewire\Servicios;

use App\Models\servicios\Garantia;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Componente de Livewire para gestionar la tabla de Garantías.
 */
class GestionarGarantias extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // Propiedades para el modal
    public $isModalOpen = false;
    public $isEditMode = false;
    public $garantiaId;
    public $status;
    public $empresa;
    public $contacto;
    public $fecha_garantia;

    protected $rules = [
        'status' => 'required|in:activa,terminada',
        'empresa' => 'nullable|string|max:100',
        'contacto' => 'nullable|string|max:100',
        'fecha_garantia' => 'nullable|date',
    ];

    protected $messages = [
        'status.required' => 'El estado es obligatorio.',
        'status.in' => 'El estado debe ser: activa o terminada.',
        'empresa.max' => 'El nombre de la empresa no puede exceder 100 caracteres.',
        'contacto.max' => 'El contacto no puede exceder 100 caracteres.',
        'fecha_garantia.date' => 'La fecha de garantía debe ser una fecha válida.',
    ];

    public function create()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $garantia = Garantia::findOrFail($id);
        $this->garantiaId = $id;
        $this->status = $garantia->status;
        $this->empresa = $garantia->empresa;
        $this->contacto = $garantia->contacto;
        $this->fecha_garantia = $garantia->fecha_garantia ? $garantia->fecha_garantia->format('Y-m-d') : '';
        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate();

        if ($this->isEditMode) {
            Garantia::where('id_garantia', $this->garantiaId)->update([
                'status' => $this->status,
                'empresa' => $this->empresa,
                'contacto' => $this->contacto,
                'fecha_garantia' => $this->fecha_garantia,
            ]);
            session()->flash('message', 'Garantía actualizada exitosamente.');
        } else {
            Garantia::create([
                'status' => $this->status,
                'empresa' => $this->empresa,
                'contacto' => $this->contacto,
                'fecha_garantia' => $this->fecha_garantia,
            ]);
            session()->flash('message', 'Garantía creada exitosamente.');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        Garantia::find($id)->delete();
        session()->flash('message', 'Garantía eliminada exitosamente.');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['garantiaId', 'status', 'empresa', 'contacto', 'fecha_garantia']);
    }

    public function render()
    {
        return view('livewire.servicios.gestionar-garantias', [
            'garantias' => Garantia::paginate(10),
        ]);
    }
}