<?php

namespace App\Livewire\Odontologia\Consultorio;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Odontologia\Almacen;

class AlmacenTable extends Component
{
    use WithPagination;

    public $search = ''; // Propiedad para la búsqueda, si se desea implementar
    public $itemToDeleteId; // Propiedad para almacenar el ID del ítem a eliminar

    protected $listeners = ['insumoAdded' => '$refresh'];

    // Opcional: Para resetear la paginación cuando cambia la búsqueda
    public function updatingSearch()
    {
        $this->resetPage();
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

        return view('livewire.odontologia.consultorio.almacen-table', [
            'almacenItems' => $almacenItems,
        ]);
    }
}