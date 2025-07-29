<?php

namespace App\Livewire\Odontologia;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Odontologia\Pedido;
use App\Models\Odontologia\Insumo;
use App\Models\Odontologia\Almacen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PeticionesTable extends Component
{
    use WithPagination;

    public $search = ''; // Propiedad para el campo de búsqueda
    public $peticionToConfirmId;
    public $peticionToCancelId;
    public $peticionToDeleteId;
    public $cantidad;
    public $almacenCantidadDisponible;
    public $message = '';
    public $messageType = '';
    protected $paginationTheme = 'bootstrap'; // Define el tema de paginación de Bootstrap

    // Método que se ejecuta cuando cambia el valor de 'search' para resetear la paginación
    public function updatingSearch()
    {
        $this->resetPage();
    }

/**
     * Confirma la autorización de un pedido y establece el ID.
     * Recupera la cantidad disponible del almacén para el insumo.
     *
     * @param int $id
     * @return void
     */
    public function confirmPedidoModal($id)
    {
        $this->peticionToConfirmId = $id;
        $pedido = Pedido::with('almacenItem')->find($id); // Cargar la relación almacenItem
        
        if ($pedido && $pedido->almacenItem) {
            $this->almacenCantidadDisponible = $pedido->almacenItem->cantidad;
            $this->cantidad = $pedido->cantidad_solicitada; // Pre-llenar con la cantidad solicitada
            $this->message = ''; // Limpiar mensajes previos al abrir el modal
            $this->messageType = '';
            $this->dispatch('open-modal', 'modalConfirmarPedido');
        } else {
            session()->flash('error', 'Pedido o insumo de almacén no encontrado.');
            return redirect(request()->header('Referer'));
        }
    }

    /**
     * Confirma el pedido, actualiza la cantidad autorizada y la del almacén.
     *
     * @return void
     */
    public function confirmPedido()
    {
        // Reglas de validación
        $rules = [
            'cantidad' => [
                'required',
                'integer',
                'min:1',
                // Validar que la cantidad no exceda la disponible en el almacén
                function ($attribute, $value, $fail) {
                    $pedido = Pedido::with('almacenItem')->find($this->peticionToConfirmId);
                    if ($pedido && $pedido->almacenItem) {
                        if ($value > $pedido->almacenItem->cantidad) {
                            $fail('La cantidad autorizada no puede ser mayor que la cantidad disponible en almacén (' . $pedido->almacenItem->cantidad . ').');
                        }
                    } else {
                        $fail('No se pudo encontrar el insumo en almacén para validar la cantidad.');
                    }
                },
            ],
        ];

        // Mensajes de validación personalizados
        $messages = [
            'cantidad.required' => 'La cantidad autorizada es obligatoria.',
            'cantidad.integer' => 'La cantidad autorizada debe ser un número entero.',
            'cantidad.min' => 'La cantidad autorizada debe ser al menos 1.',
        ];

        try {
            $this->validate($rules, $messages);

            if ($this->peticionToConfirmId) {
                $pedido = Pedido::with('almacenItem')->find($this->peticionToConfirmId);

                if ($pedido) {
                    // Restar la cantidad autorizada del almacén
                    $almacenItem = $pedido->almacenItem;
                    $almacenItem->cantidad -= $this->cantidad;
                    $almacenItem->save();

                    // Actualizar el pedido
                    $pedido->cantidad_autorizada = $this->cantidad;
                    $pedido->estado_pedido = 'Entregado';
                    $pedido->fecha_entrega = now()->toDateString();
                    $pedido->save();

                    $this->peticionToConfirmId = null;
                    $this->almacenCantidadDisponible = null; // Limpiar la cantidad disponible
                    $this->message = 'Pedido autorizado y almacén actualizado exitosamente.';
                    $this->messageType = 'success';
                }
            }
        } catch (ValidationException $e) {
            $this->message = 'Error de validación: ' . $e->getMessage();
            $this->messageType = 'error';
            throw $e;
        } catch (\Exception $e) {
            $this->message = 'Error al confirmar el pedido: ' . $e->getMessage();
            $this->messageType = 'error';
        }
        
        session()->flash($this->messageType, $this->message);
        return redirect(request()->header('Referer'));
    }

    /**
     * Confirma la cancelación de un pedido y establece el ID.
     *
     * @param int $id
     * @return void
     */
    public function confirmCancel($id)
    {
        $this->peticionToCancelId = $id;
        $this->dispatch('open-modal', 'modalCancelarPedido');
    }

    /**
     * Cancela el pedido en la base de datos.
     *
     * @return void
     */
    public function cancelPedido()
    {
        if ($this->peticionToCancelId) {
            $pedido = Pedido::find($this->peticionToCancelId);
            if ($pedido) {
                $pedido->estado_pedido = 'Cancelado';
                $pedido->save();
                $this->peticionToCancelId = null;
                $this->message = 'Pedido cancelado exitosamente.';
                $this->messageType = 'success';
            }
        }
        
        session()->flash($this->messageType, $this->message);
        return redirect(request()->header('Referer'));
    }

    /**
     * Confirma la eliminación de un registro y establece el ID.
     *
     * @param int $id
     * @return void
     */
    public function confirmDelete($id)
    {
        $this->peticionToDeleteId = $id;
        $this->dispatch('open-modal', 'modalEliminarRegistro');
    }

    /**
     * Elimina el registro de las peticiones.
     *
     * @return void
     */
    public function deleteRegistro()
    {
        if ($this->peticionToDeleteId) {
            Pedido::find($this->peticionToDeleteId)->delete();
            $this->peticionToDeleteId = null; // Limpia el ID después de la eliminación
            session()->flash('success', 'Registro eliminado exitosamente.'); // Opcional: mensaje de éxito
            return redirect(request()->header('Referer'));
        }
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
