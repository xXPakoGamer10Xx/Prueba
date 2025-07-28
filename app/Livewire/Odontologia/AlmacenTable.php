<?php

namespace App\Livewire\Odontologia;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Odontologia\Almacen;
use Illuminate\Support\Facades\Validator; // Importar la fachada Validator

class AlmacenTable extends Component
{
    use WithPagination;

    public $search = ''; // Propiedad para la búsqueda, si se desea implementar
    public $itemToDeleteId; // Propiedad para almacenar el ID del ítem a eliminar

    protected $listeners = ['insumoAdded' => '$refresh'];

    // Opcional: Para resetear la paginación cuando cambia la búsqueda
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Actualiza la cantidad de un insumo en la tabla almacén.
     * Se llama al perder el foco o al presionar Enter en el input de cantidad.
     *
     * @param int $id_almacen El ID del registro en la tabla 'almacén'.
     * @param int $newCantidad La nueva cantidad a establecer.
     * @return void
     */
    public function updateCantidad($id_almacen, $newCantidad)
    {
        // Define las reglas y mensajes de validación
        $rules = [
            'cantidad' => 'required|integer|min:0',
        ];
        $messages = [
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad no puede ser negativa.',
        ];

        // Crea una instancia del validador con los datos y las reglas
        $validator = Validator::make([
            'cantidad' => $newCantidad // El dato a validar, con una clave 'cantidad'
        ], $rules, $messages);

        // Realiza la validación
        try {
            $validator->validate(); // Llama a validate() en la instancia del validador
        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('error', 'Error de validación: ' . implode(', ', \Illuminate\Support\Arr::flatten($e->errors())));
            throw $e; // Vuelve a lanzar la excepción para que Livewire la capture y muestre los errores si es necesario
        }

        try {
            $almacenEntry = Almacen::find($id_almacen);

            if ($almacenEntry) {
                $almacenEntry->cantidad = $newCantidad; // Usa el valor validado
                $almacenEntry->save();
                session()->flash('message', 'Cantidad actualizada exitosamente.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar la cantidad: ' . $e->getMessage());
        }
    }

    /**
     * Establece el ID del ítem a eliminar y muestra la modal de confirmación.
     *
     * @param int $id El ID del ítem de almacén a eliminar.
     * @return void
     */
    public function confirmDelete($id)
    {
        $this->itemToDeleteId = $id;
        // Asumiendo que tienes una modal de confirmación similar a las otras
        $this->dispatch('open-modal', 'modalEliminarInsumo');
    }

    /**
     * Elimina el ítem de almacén de la base de datos.
     *
     * @return void
     */
    public function deleteInsumo()
    {
        if ($this->itemToDeleteId) {
            Almacen::find($this->itemToDeleteId)->delete();
            $this->itemToDeleteId = null;
            $this->dispatch('close-modal', 'modalEliminarInsumo');
            session()->flash('message', 'Insumo eliminado exitosamente.');
            $this->dispatch('$insumoAdded'); // Recargar la tabla
        }
    }

    public function render()
    {
        // Obtener los ítems del almacén con sus relaciones, aplicar búsqueda y paginar
        $almacenItems = Almacen::query()
            ->with(['insumo', 'insumo.laboratorio', 'insumo.presentacion']) // Cargar relaciones necesarias
            ->when($this->search, function ($query) {
                // Buscar por descripción del insumo o clave
                $query->whereHas('insumo', function ($q) {
                    $q->where('descripcion', 'like', '%' . $this->search . '%')
                      ->orWhere('clave', 'like', '%' . $this->search . '%');
                });
            })
            ->paginate(10); // Paginar 10 resultados por página

        return view('livewire.odontologia.almacen-table', [
            'almacenItems' => $almacenItems,
        ]);
    }
}