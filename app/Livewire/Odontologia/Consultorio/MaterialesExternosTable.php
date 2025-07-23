<?php

namespace App\Livewire\Odontologia\Consultorio;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Odontologia\MaterialesExternos;

class MaterialesExternosTable extends Component
{
    use WithPagination;

    public $search = ''; // Propiedad para la búsqueda, si se desea implementar
    public $materialToDeleteId; // Propiedad para almacenar el ID del material a eliminar

    protected $listeners = ['materialAdded' => '$refresh'];

    // Opcional: Para resetear la paginación cuando cambia la búsqueda
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Establece el ID del material a eliminar y muestra la modal de confirmación.
     *
     * @param int $id El ID del material externo a eliminar.
     * @return void
     */
    public function confirmDelete($id)
    {
        $this->materialToDeleteId = $id;
        $this->dispatch('open-modal', 'deleteMaterialModal'); // Despacha un evento para 
    }

    /**
     * Elimina el material externo de la base de datos.
     *
     * @return void
     */
    public function deleteMaterial()
    {
        if ($this->materialToDeleteId) {
            MaterialesExternos::find($this->materialToDeleteId)->delete();
            $this->materialToDeleteId = null; // Limpiar el ID después de la eliminación
            $this->dispatch('close-modal', 'deleteMaterialModal'); // Despacha un evento para cerrar la modal
            session()->flash('message', 'Material externo eliminado exitosamente.'); // Mensaje de éxito
            $this->dispatch('$refresh'); // Recargar la tabla
        }
    }


    public function render()
    {
        // Obtener los materiales externos, aplicar búsqueda si existe y paginar
        $materialesExternos = MaterialesExternos::query()
            ->when($this->search, function ($query) {
                // Buscar por descripcion o cualquier otro campo relevante
                $query->where('descripcion', 'like', '%' . $this->search . '%');
            })
            ->paginate(10); // Paginar 10 resultados por página

        return view('livewire.odontologia.consultorio.materiales-externos-table', [
            'materialesExternos' => $materialesExternos,
        ]);
    }
}