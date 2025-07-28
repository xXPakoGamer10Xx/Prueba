<?php

namespace App\Livewire\Odontologia;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Odontologia\Presentacion;

class PresentacionesTable extends Component
{
    use WithPagination;

    public $search = ''; // Propiedad para búsqueda, si se desea implementar
    public $presentacionToDeleteId;
    protected $listeners = ['insumoAdded' => '$refresh'];

    // Opcional: Para resetear la paginación cuando cambia la búsqueda
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->presentacionToDeleteId = $id;
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
        if ($this->presentacionToDeleteId) {
            Presentacion::find($this->presentacionToDeleteId)->delete();
            $this->presentacionToDeleteId = null; // Limpia el ID después de la eliminación
            $this->dispatch('close-modal', 'modalEliminarPresentacion'); // Cierra la modal
            session()->flash('message', 'Presentación eliminada exitosamente.'); // Mensaje de éxito
            $this->dispatch('insumoAdded'); // Despacha un evento para refrescar la tabla
        }
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