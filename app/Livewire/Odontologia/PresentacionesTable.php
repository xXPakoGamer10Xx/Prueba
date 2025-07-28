<?php

namespace App\Livewire\Odontologia;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Odontologia\Presentacion;

class PresentacionesTable extends Component
{
    use WithPagination;

    public $search = ''; // Propiedad para búsqueda, si se desea implementar
    public $itemToBeDeleted;
    public $message = '';
    public $messageType = '';

    // Opcional: Para resetear la paginación cuando cambia la búsqueda
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->itemToBeDeleted = $id;
        // Despacha un evento para abrir la modal de confirmación de eliminación
        $this->dispatch('open-modal', 'modalEliminarPresentacion');
    }

    /**
     * Elimina el insumo de la base de datos.
     *
     * @return void
     */
    public function deletePresentacion()
    {
        try {
            if ($this->itemToBeDeleted) {
                Presentacion::find($this->itemToBeDeleted)->delete();
                $this->itemToBeDeleted = null; // Limpia el ID después de la eliminación
                $this->message = 'Presentación eliminada exitosamente.';
                $this->messageType = 'success';
            }
        } catch(\Exception) {
            $this->message = 'No se pudo eliminar el registro ya que está siendo utilizado en otras tablas.';
            $this->messageType = 'error';
        }

        session()->flash($this->messageType, $this->message);
        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        // Obtener los presentaciones, aplicar búsqueda si existe y paginar
        $presentaciones = Presentacion::query()
            ->when($this->search, function ($query) {
                // Buscar por descripción de la presentacion
                $query->where('descripcion', 'like', '%' . $this->search . '%');
            })
            ->paginate(10); // Paginar 10 resultados por página

        return view('livewire.odontologia.presentaciones-table', [
            'presentaciones' => $presentaciones,
        ]);
    }
}