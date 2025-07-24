<?php

namespace App\Livewire\Odontologia\Almacen;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Odontologia\Almacen;

class AlmacenTable extends Component
{
    use WithPagination;

    public $search = ''; // Propiedad para la búsqueda, si se desea implementar
    public $itemToDeleteId; // Propiedad para almacenar el ID del ítem a eliminar

    // Opcional: Para resetear la paginación cuando cambia la búsqueda
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Establece el ID del ítem a eliminar y muestra la modal de confirmación.
     *
     * @param int $id El ID del ítem de almacén a eliminar.
     * @return void
     */
    public function confirmDelete($id)
    {
        $this->itemToDeleteId = $id;
        // Asumiendo que tienes una modal de confirmación similar a las otras
        $this->dispatch('open-modal', 'deleteAlmacenItemModal');
    }

    /**
     * Elimina el ítem de almacén de la base de datos.
     *
     * @return void
     */
    public function deleteItem()
    {
        if ($this->itemToDeleteId) {
            Almacen::find($this->itemToDeleteId)->delete();
            $this->itemToDeleteId = null;
            $this->dispatch('close-modal', 'deleteAlmacenItemModal');
            session()->flash('message', 'Ítem de almacén eliminado exitosamente.');
            $this->dispatch('$refresh'); // Recargar la tabla
        }
    }

    public function render()
    {
        // Obtener los ítems del almacén con sus relaciones, aplicar búsqueda y paginar
        $almacenItems = Almacen::query()
            ->with(['insumo', 'insumo.laboratorio', 'insumo.presentacion']) // Cargar relaciones necesarias
            ->when($this->search, function ($query) {
                // Buscar por descripción del insumo o clave
                $query->whereHas('insumo', function ($q) {
                    $q->where('descripcion', 'like', '%' . $this->search . '%')
                      ->orWhere('clave', 'like', '%' . $this->search . '%');
                });
            })
            ->paginate(10); // Paginar 10 resultados por página

        return view('livewire.odontologia.almacen.almacen-table', [
            'almacenItems' => $almacenItems,
        ]);
    }
}