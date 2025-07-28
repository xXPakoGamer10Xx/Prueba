<?php

namespace App\Livewire\Odontologia;

use Livewire\Component;
use App\Models\Odontologia\Pedido;
use Illuminate\Support\Facades\Auth; // To get the authenticated user's role
use Carbon\Carbon; // To get the current date

class AddNewPeticionModal extends Component
{
    public $id_insumo_almacen_fk;
    public $cantidad_solicitada;
    public $message = '';
    public $messageType = '';

    // Listeners for events from other components
    protected $listeners = [
        'openPeticionModal' => 'setInsumoAlmacenId', // Custom event to receive the insumo ID
        'hidden.bs.modal' => 'resetForm' // Listen to Bootstrap modal's hidden event
    ];

    protected function rules()
    {
        return [
            'id_insumo_almacen_fk' => 'required|exists:almacen,id_insumo_almacen',
            'cantidad_solicitada' => 'required|integer|min:1',
        ];
    }

    protected $messages = [
        'id_insumo_almacen_fk.required' => 'El ID del insumo es obligatorio.',
        'id_insumo_almacen_fk.exists' => 'El insumo seleccionado no es válido.',
        'cantidad_solicitada.required' => 'La cantidad solicitada es obligatoria.',
        'cantidad_solicitada.integer' => 'La cantidad solicitada debe ser un número entero.',
        'cantidad_solicitada.min' => 'La cantidad solicitada debe ser al menos 1.',
    ];

    // Esta es la única declaración de la función setInsumoAlmacenId
    public function setInsumoAlmacenId($id)
    {
        $this->id_insumo_almacen_fk = $id;
        $this->resetMessage(); // Clear any previous messages when a new request is initiated
        $this->cantidad_solicitada = null; // Clear previous quantity
    }

    public function savePeticion()
    {
        $this->resetMessage();
        $this->validate();

        // Ensure the user has the correct role
        if (Auth::check() && Auth::user()->rol === 'odontologia_consultorio') {
            try {
                Pedido::create([
                    'id_insumo_almacen_fk' => $this->id_insumo_almacen_fk,
                    'cantidad_solicitada' => $this->cantidad_solicitada,
                    'cantidad_autorizada' => null, // As requested, insert NULL
                    'estado_pedido' => 'Pendiente', // As requested, set to 'Pendiente'
                    'fecha_pedido' => Carbon::now()->toDateString(), // Current date
                    'fecha_entrega' => null, // As requested, insert NULL
                ]);

                $this->message = 'Petición registrada exitosamente.';
                $this->messageType = 'success';
            } catch (\Illuminate\Validation\ValidationException $e) {
                // Re-throw validation exceptions for Livewire to handle
                throw $e;
            } catch (\Exception $e) {
                $this->message = 'Error al registrar la petición: ' . $e->getMessage();
                $this->messageType = 'danger';
            }
        } else {
            $this->message = 'No tienes permiso para realizar esta acción.';
            $this->messageType = 'danger';
        }

        session()->flash($this->messageType, $this->message);
        return redirect(request()->header('Referer'));
    }

    public function resetForm()
    {
        $this->reset(['id_insumo_almacen_fk', 'cantidad_solicitada']);
        $this->resetValidation();
        $this->resetMessage();
    }

    public function resetMessage()
    {
        $this->message = '';
        $this->messageType = '';
    }

    public function render()
    {
        return view('livewire.odontologia.add-new-peticion-modal');
    }
}