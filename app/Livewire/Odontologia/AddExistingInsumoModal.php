<?php

namespace App\Livewire\Odontologia;

use App\Models\Odontologia\Almacen;
use Livewire\Component;
use App\Models\Odontologia\Insumo;
use App\Models\Odontologia\Consultorio;
use ParaTest\JUnit\MessageType;

class AddExistingInsumoModal extends Component
{
    public $formulario; // Propiedad que recibirá 'consultorio' o 'almacen'
    public $insumos; // Para almacenar la lista de insumos para el select
    public $selectedInsumoId; // Para el ID del insumo seleccionado
    public $cantidad; // Para la cantidad a agregar

    // Propiedad para el mensaje de éxito/error
    public $message = '';
    public $messageType = ''; // 'success' o 'danger'

    protected $rules = [
        'selectedInsumoId' => 'required|exists:insumos,id_insumo',
        'cantidad' => 'required|integer|min:1',
    ];

    protected $messages = [
        'selectedInsumoId.required' => 'Debe seleccionar un insumo.',
        'selectedInsumoId.exists' => 'El insumo seleccionado no es válido.',
        'cantidad.required' => 'La cantidad es obligatoria.',
        'cantidad.integer' => 'La cantidad debe ser un número entero.',
        'cantidad.min' => 'La cantidad debe ser al menos 1.',
    ];

    // Recibe el valor de 'formulario' al montar el componente
    public function mount($formulario = 'consultorio') // Valor por defecto si no se pasa nada
    {
        $this->formulario = $formulario; // Asigna el valor recibido a la propiedad
        // Carga todos los insumos disponibles para el select
        $this->insumos = Insumo::orderBy('id_insumo', 'desc')->get();
    }

    public function addInsumoToConsultorio()
    {
        $this->resetMessage(); // Limpiar mensajes anteriores
        $this->validate();

        try {
            // Buscar si el insumo ya existe en la tabla correspondiente (consultorio o almacen)
            if ($this->formulario == 'consultorio') {
                $targetModel = Consultorio::class;
                $this->message = 'El insumo existente ha sido agregado exitosamente al consultorio.';
            } else {
                $targetModel = Almacen::class;
                $this->message = 'El insumo existente ha sido agregado exitosamente a almacén.';
            }

            $existingInsumo = $targetModel::where('id_insumo_fk', $this->selectedInsumoId)->first();

            if ($existingInsumo) {
                // Si existe, actualizar la cantidad
                $existingInsumo->cantidad += $this->cantidad;
                $existingInsumo->save();
            } else {
                // Si no existe, crear un nuevo registro
                $targetModel::create([
                    'id_insumo_fk' => $this->selectedInsumoId,
                    'cantidad' => $this->cantidad,
                ]);
            }

            $this->messageType = 'success';

        } catch (\Exception $e) {
            $this->message = 'Error al agregar el insumo: ' . $e->getMessage();
            $this->messageType = 'danger';
        }

        session()->flash($this->messageType, $this->message);
        return redirect(request()->header('Referer'));
    }

    // Método para resetear los mensajes de validación y de estado
    public function resetMessage()
    {
        $this->message = '';
        $this->messageType = '';
    }

    public function render()
    {
        return view('livewire.odontologia.add-existing-insumo-modal');
    }
}