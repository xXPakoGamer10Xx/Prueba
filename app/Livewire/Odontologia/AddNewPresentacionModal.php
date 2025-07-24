<?php

namespace App\Livewire\Odontologia;

use Livewire\Component;
use App\Models\Odontologia\Presentacion; // Import the Presentacion model

class AddNewPresentacionModal extends Component
{
    public $descripcion; // Property to bind to the form input

    public $message = ''; // For success/error messages
    public $messageType = ''; // 'success' or 'danger'

    protected $rules = [
        'descripcion' => 'required|string|max:20|unique:presentaciones,descripcion',
    ];

    protected $messages = [
        'descripcion.required' => 'La descripción de la presentación es obligatoria.',
        'descripcion.max' => 'La descripción no puede exceder los 20 caracteres.',
        'descripcion.unique' => 'Esta presentación ya existe. Por favor, ingrese una diferente.',
    ];

    /**
     * Save the new laboratory to the database.
     */
    public function saveNewPresentacion()
    {
        $this->resetMessage();
        $this->validate();

        try {
            Presentacion::create([
                'descripcion' => $this->descripcion,
            ]);

            $this->message = 'Nueva presentación registrada exitosamente.';
            $this->messageType = 'success';

            $this->resetForm();
        
            $this->dispatch('close-modal', 'modalAgregarPresentacion');
            $this->dispatch('presentacionAdded');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->message = 'Error al registrar la nuevo presentacion: ' . $e->getMessage();
            $this->messageType = 'danger';
        }
    }

    /**
     * Reset the form fields and messages.
     */
    protected function resetForm()
    {
        $this->reset(['descripcion']);
        $this->resetMessage();
    }

    /**
     * Reset only the status messages.
     */
    public function resetMessage()
    {
        $this->message = '';
        $this->messageType = '';
    }

    /**
     * Livewire lifecycle hook to listen for modal close event and reset form.
     */
    public function hydrate()
    {
        $this->listeners = [
            'hidden.bs.modal' => 'resetForm'
        ];
    }

    public function render()
    {
        return view('livewire.odontologia.add-new-presentacion-modal');
    }
}