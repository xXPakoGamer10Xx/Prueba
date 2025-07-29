<?php

namespace App\Livewire\Odontologia;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Odontologia\Pedido;
use App\Models\Odontologia\Insumo;
use Illuminate\Support\Facades\Auth;

class PeticionesTable extends Component
{
    use WithPagination;

    public $search = ''; // Propiedad para el campo de búsqueda
    public $pedidoToCancelId; // Propiedad para almacenar el ID del pedido a cancelar
    public $message = '';
    public $messageType = '';
    protected $paginationTheme = 'bootstrap'; // Define el tema de paginación de Bootstrap

    // Método que se ejecuta cuando cambia el valor de 'search' para resetear la paginación
    public function updatingSearch()
    {
        $this->resetPage();
    }

/**
     * Confirma la cancelación de un pedido y establece el ID.
     *
     * @param int $id
     * @return void
     */
    public function confirmCancel($id)
    {
        $this->pedidoToCancelId = $id;
        // Dispatch an event to open the confirmation modal (assuming a generic modal component handles this)
        $this->dispatch('open-modal', 'modalCancelarPedido');
    }

    /**
     * Cancela el pedido en la base de datos.
     *
     * @return void
     */
    public function cancelPedido()
    {
        if ($this->pedidoToCancelId) {
            $pedido = Pedido::find($this->pedidoToCancelId);
            if ($pedido) {
                $pedido->estado_pedido = 'Cancelado';
                $pedido->save();
                $this->pedidoToCancelId = null;
                $this->message = 'Pedido cancelado exitosamente.';
                $this->messageType = 'success';
            }
        }
        
        session()->flash($this->messageType, $this->message);
        return redirect(request()->header('Referer'));
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
