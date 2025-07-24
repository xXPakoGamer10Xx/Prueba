<?php

namespace App\Livewire\Odontologia;

use Livewire\Component;
use App\Models\Odontologia\Insumo;
use App\Models\Odontologia\Consultorio;
use App\Models\Odontologia\Laboratorio;
use App\Models\Odontologia\Presentacion;

class AddNewInsumoModal extends Component
{
    // Propiedades para los campos del formulario
    public $clave;
    public $descripcion;
    public $id_laboratorio;
    public $contenido;
    public $id_presentacion;
    public $caducidad;
    public $cantidad;

    // Propiedades para las listas desplegables (selects)
    public $laboratorios;
    public $presentaciones;

    // Propiedades para mensajes de estado
    public $message = '';
    public $messageType = ''; // 'success' o 'danger'

    protected $rules = [
        'clave' => 'required|string|max:255|unique:insumos,clave', // Clave debe ser única en la tabla insumos
        'descripcion' => 'required|string|max:255',
        'id_laboratorio' => 'required|exists:laboratorios,id_laboratorio',
        'contenido' => 'required|string|max:255',
        'id_presentacion' => 'required|exists:presentaciones,id_presentacion',
        'caducidad' => 'nullable|date', // Caducidad puede ser nula
        'cantidad' => 'required|integer|min:0', // La cantidad inicial puede ser 0
    ];

    protected $messages = [
        'clave.required' => 'La clave es obligatoria.',
        'clave.unique' => 'Esta clave ya existe. Por favor, ingrese una diferente.',
        'descripcion.required' => 'La descripción es obligatoria.',
        'id_laboratorio.required' => 'Debe seleccionar un laboratorio.',
        'id_laboratorio.exists' => 'El laboratorio seleccionado no es válido.',
        'contenido.required' => 'El contenido es obligatorio.',
        'id_presentacion.required' => 'Debe seleccionar una presentación.',
        'id_presentacion.exists' => 'La presentación seleccionada no es válida.',
        'caducidad.date' => 'La caducidad debe ser una fecha válida.',
        'cantidad.required' => 'La cantidad es obligatoria.',
        'cantidad.integer' => 'La cantidad debe ser un número entero.',
        'cantidad.min' => 'La cantidad debe ser al menos 0.',
    ];

    public function mount()
    {
        // Cargar laboratorios y presentaciones al inicializar el componente
        $this->laboratorios = Laboratorio::orderBy('descripcion')->get();
        $this->presentaciones = Presentacion::orderBy('descripcion')->get();

        // Valores por defecto para los campos
        $this->caducidad = now()->format('Y-m-d'); // Fecha actual por defecto
        $this->cantidad = 0; // Cantidad por defecto
    }

    public function saveNewInsumo()
    {
        $this->resetMessage();
        $this->validate();

        try {
            // Asegurarse de que caducidad sea NULL si el campo está vacío (cadena vacía)
            // Esto es una comprobación defensiva, Eloquent a menudo lo maneja con el casting.
            $caducidadParaGuardar = empty($this->caducidad) ? null : $this->caducidad;

            $insumo = Insumo::create([
                'clave' => $this->clave,
                'descripcion' => $this->descripcion,
                'id_laboratorio' => $this->id_laboratorio,
                'contenido' => $this->contenido,
                'id_presentacion' => $this->id_presentacion,
                'caducidad' => $caducidadParaGuardar, // Usa el valor que puede ser NULL
            ]);

            // ... (el resto de tu lógica para agregar al consultorio, mensajes, etc.) ...
            // 2. Agregar el insumo recién creado al 'consultorio'
            Consultorio::create([
                'id_insumo_fk' => $insumo->id_insumo,
                'cantidad' => $this->cantidad,
            ]);

            $this->message = 'Nuevo insumo registrado y agregado al consultorio exitosamente.';
            $this->messageType = 'success';

            // Limpiar los campos del formulario después del éxito
            $this->resetForm();

            // Despachar evento para cerrar la modal
            $this->dispatch('close-modal', 'modalAgregarNuevoInsumo');
            // Despachar evento para que la tabla de insumos se refresque
            $this->dispatch('insumoAdded');

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->message = 'Error al registrar el nuevo insumo: ' . $e->getMessage();
            $this->messageType = 'danger';
        }
    }

    // Método para limpiar los campos del formulario y los mensajes
    protected function resetForm()
    {
        $this->reset([
            'clave', 'descripcion', 'id_laboratorio', 'contenido',
            'id_presentacion', 'caducidad', 'cantidad'
        ]);
        $this->caducidad = now()->format('Y-m-d'); // Restablecer a fecha actual
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
        return view('livewire.odontologia.add-new-insumo-modal');
    }
}