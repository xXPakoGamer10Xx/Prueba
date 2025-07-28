<?php

namespace App\Livewire\Odontologia;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Odontologia\Pedido;
use App\Models\Odontologia\Insumo;

class PeticionesTable extends Component
{
    use WithPagination;

    public $search = ''; // Propiedad para el campo de búsqueda
    protected $paginationTheme = 'bootstrap'; // Define el tema de paginación de Bootstrap

    // Método que se ejecuta cuando cambia el valor de 'search' para resetear la paginación
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $pedidos = Pedido::query()
            ->with(['almacenItem.insumo.laboratorio', 'almacenItem.insumo.presentacion'])
            ->when($this->search, function ($query) {
                $query->where('cantidad_solicitada', 'like', '%' . $this->search . '%')
                      ->orWhere('cantidad_autorizada', 'like', '%' . $this->search . '%')
                      ->orWhere('estado_pedido', 'like', '%' . $this->search . '%')
                      ->orWhere('fecha_pedido', 'like', '%' . $this->search . '%')
                      ->orWhere('fecha_entrega', 'like', '%' . $this->search . '%')
                      ->orWhereHas('almacenItem.insumo', function ($q) {
                          $q->where('clave', 'like', '%' . $this->search . '%')
                            ->orWhere('descripcion', 'like', '%' . $this->search . '%');
                      });
            })
            ->orderBy('fecha_pedido', 'desc')
            ->paginate(10);

        return view('livewire.odontologia.peticiones-table', [
            'pedidos' => $pedidos,
        ]);
    }
}
