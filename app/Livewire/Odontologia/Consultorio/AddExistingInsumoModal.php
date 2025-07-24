<?php

namespace App\Livewire\Odontologia\Consultorio;

use App\Models\Odontologia\Almacen;
use Livewire\Component;
use App\Models\Odontologia\Insumo;
use App\Models\Odontologia\Consultorio;

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
            } else {
                $targetModel = Almacen::class;
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
                    // Otros campos que Consultorio o Almacen necesiten, si los hay y son obligatorios
                ]);
            }

            $this->message = 'Insumo agregado/actualizado exitosamente.';
            $this->messageType = 'success';

            // Limpiar los campos del formulario
            $this->reset(['selectedInsumoId', 'cantidad']);

            // Cerrar la modal
            $this->dispatch('close-modal', 'modalAgregarInsumo');

            // Opcional: Despachar un evento para que InsumosTable se refresque si está escuchando
            $this->dispatch('insumoAdded'); // Para que InsumosTable recargue los datos
        } catch (\Exception $e) {
            $this->message = 'Error al agregar el insumo: ' . $e->getMessage();
            $this->messageType = 'danger';
            // Para mostrar el mensaje de error dentro de la modal, no la cierres
        }
    }

    // Método para resetear los mensajes de validación y de estado
    public function resetMessage()
    {
        $this->message = '';
        $this->messageType = '';
    }

    public function render()
    {
        return view('livewire.odontologia.consultorio.add-existing-insumo-modal');
    }
}