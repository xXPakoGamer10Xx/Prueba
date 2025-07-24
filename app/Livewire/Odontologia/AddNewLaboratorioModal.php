<?php

namespace App\Livewire\Odontologia;

use Livewire\Component;
use App\Models\Odontologia\Laboratorio; // Import the Laboratorio model

class AddNewLaboratorioModal extends Component
{
    public $descripcion; // Property to bind to the form input

    public $message = ''; // For success/error messages
    public $messageType = ''; // 'success' or 'danger'

    protected $rules = [
        'descripcion' => 'required|string|max:50|unique:laboratorios,descripcion',
    ];

    protected $messages = [
        'descripcion.required' => 'La descripción del laboratorio es obligatoria.',
        'descripcion.max' => 'La descripción no puede exceder los 50 caracteres.',
        'descripcion.unique' => 'Este laboratorio ya existe. Por favor, ingrese uno diferente.',
    ];

    /**
     * Save the new laboratory to the database.
     */
    public function saveNewLaboratorio()
    {
        $this->resetMessage(); // Clear previous messages
        $this->validate(); // Run validation rules

        try {
            // Create a new Laboratorio record
            Laboratorio::create([
                'descripcion' => $this->descripcion,
            ]);

            $this->message = 'Nuevo laboratorio registrado exitosamente.';
            $this->messageType = 'success';

            $this->resetForm(); // Clear form fields

            // Dispatch events to close the modal and refresh the table
            $this->dispatch('close-modal', 'modalAgregarLaboratorio');
            $this->dispatch('insumoAdded'); // Event to refresh the LaboratoriosTable
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions so Livewire can handle displaying errors
            throw $e;
        } catch (\Exception $e) {
            // Catch any other exceptions and set an error message
            $this->message = 'Error al registrar el nuevo laboratorio: ' . $e->getMessage();
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
        return view('livewire.odontologia.add-new-laboratorio-modal');
    }
}