<?php

namespace App\Livewire\Odontologia;

use Livewire\Component;
use App\Models\Odontologia\MaterialesExternos; // Usamos el modelo de Materiales Externos

class AddNewMaterialModal extends Component
{
    // Propiedades para los campos del formulario
    public $descripcion;
    public $cantidad;

    // Propiedades para mensajes de estado
    public $message = '';
    public $messageType = ''; // 'success' o 'danger'

    protected $rules = [
        'descripcion' => 'required|string|max:255',
        'cantidad' => 'required|integer|min:0', // La cantidad inicial puede ser 0
    ];

    protected $messages = [
        'descripcion.required' => 'La descripción es obligatoria.',
        'cantidad.required' => 'La cantidad es obligatoria.',
        'cantidad.integer' => 'La cantidad debe ser un número entero.',
        'cantidad.min' => 'La cantidad debe ser al menos 0.',
    ];

    public function mount()
    {
        // Valores por defecto para los campos
        $this->cantidad = 0; // Cantidad por defecto
    }

    public function saveNewMaterial()
    {
        $this->resetMessage();
        $this->validate();

        try {
            MaterialesExternos::create([
                'descripcion' => $this->descripcion,
                'cantidad' => $this->cantidad,
            ]);

            $this->message = 'Nuevo material registrado exitosamente.';
            $this->messageType = 'success';
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->message = 'Error al registrar el nuevo material: ' . $e->getMessage();
            $this->messageType = 'danger';
        }

        session()->flash($this->messageType, $this->message);
        return redirect(request()->header('Referer'));
    }

    // Método para limpiar los campos del formulario y los mensajes
    protected function resetForm()
    {
        $this->reset([
            'descripcion', 'cantidad'
        ]);
        $this->cantidad = 0; // Restablecer cantidad
        $this->resetMessage();
    }

    // Método para resetear solo los mensajes de estado
    public function resetMessage()
    {
        $this->message = '';
        $this->messageType = '';
    }

    // Escuchar el evento 'hidden.bs.modal' para limpiar el formulario cuando la modal se cierra
    // Esto es útil si el usuario cierra la modal manualmente sin enviar el formulario
    public function hydrate()
    {
        $this->listeners = [
            'hidden.bs.modal' => 'resetForm'
        ];
    }

    public function render()
    {
        return view('livewire.odontologia.add-new-material-modal');
    }
}