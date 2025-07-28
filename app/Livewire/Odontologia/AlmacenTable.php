<?php

namespace App\Livewire\Odontologia;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Odontologia\Almacen;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AlmacenTable extends Component
{
    use WithPagination;

    public $search = '';
    public $itemToDeleteId;
    public $message = '';
    public $messageType = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updateCantidad($id_almacen, $newCantidad)
    {
        $rules = [
            'cantidad' => 'required|integer|min:0',
        ];
        $messages = [
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad no puede ser negativa.',
        ];

        $validator = Validator::make([
            'cantidad' => $newCantidad
        ], $rules, $messages);

        try {
            $validator->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('error', 'Error de validación: ' . implode(', ', \Illuminate\Support\Arr::flatten($e->errors())));
            throw $e;
        }

        try {
            $almacenEntry = Almacen::find($id_almacen);

            if ($almacenEntry) {
                $almacenEntry->cantidad = $newCantidad;
                $almacenEntry->save();
                $this->message = 'Cantidad actualizada exitosamente.';
                $this->messageType = 'success';
            }
        } catch (\Exception $e) {
            $this->message = 'Error al actualizar la cantidad.';
            $this->messageType = 'error';
        }

        session()->flash($this->messageType, $this->message);
        return redirect(request()->header('Referer'));
    }

    public function confirmDelete($id)
    {
        $this->itemToDeleteId = $id;
        $this->dispatch('open-modal', 'modalEliminarInsumo');
    }

    public function deleteInsumo()
    {
        try {
            if ($this->itemToDeleteId) {
                Almacen::find($this->itemToDeleteId)->delete();
                $this->itemToDeleteId = null;
                $this->message = 'Insumo eliminado exitosamente.';
                $this->messageType = 'success';
            }
        } catch (\Exception) {
            $this->message = 'No se pudo eliminar el registro ya que está siendo utilizado en otras tablas.';
            $this->messageType = 'error';
        }

        session()->flash($this->messageType, $this->message);
        return redirect(request()->header('Referer'));
    }

    // Corrected method to open the peticion modal and pass the insumo ID
    public function openPeticionModal($id_insumo_almacen)
    {
        $this->dispatch('open-modal', 'modalPedir'); // Open the modal
        // Pass the ID directly as an argument, not a named parameter
        $this->dispatch('openPeticionModal', $id_insumo_almacen);
    }

    public function render()
    {
        $almacenItems = Almacen::query()
            ->with(['insumo', 'insumo.laboratorio', 'insumo.presentacion'])
            ->when($this->search, function ($query) {
                $query->whereHas('insumo', function ($q) {
                    $q->where('descripcion', 'like', '%' . $this->search . '%')
                      ->orWhere('clave', 'like', '%' . $this->search . '%');
                });
            })
            ->paginate(10);

        return view('livewire.odontologia.almacen-table', [
            'almacenItems' => $almacenItems,
        ]);
    }
}