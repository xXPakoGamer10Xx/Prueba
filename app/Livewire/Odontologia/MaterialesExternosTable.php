<?php

namespace App\Livewire\Odontologia;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Odontologia\MaterialesExternos;
use Illuminate\Support\Facades\Validator; // Importar la fachada Validator

class MaterialesExternosTable extends Component
{
    use WithPagination;

    public $search = ''; // Propiedad para la búsqueda, si se desea implementar
    public $materialToDeleteId; // Propiedad para almacenar el ID del material a eliminar

    protected $listeners = ['materialAdded' => '$refresh'];

    // Opcional: Para resetear la paginación cuando cambia la búsqueda
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Actualiza la cantidad de un material externo.
     *
     * @param int $id El ID del material externo a actualizar.
     * @param int $newCantidad La nueva cantidad del material.
     * @return void
     */
    public function updateCantidad($id, $newCantidad)
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
            // Si hay errores de validación, puedes manejarlos aquí.
            // Por ejemplo, puedes enviar un mensaje flash o lanzar la excepción.
            session()->flash('error', 'Error de validación: ' . implode(', ', \Illuminate\Support\Arr::flatten($e->errors())));
            throw $e; // Vuelve a lanzar la excepción para que Livewire la capture y muestre los errores si es necesario
        }

        try {
            $material = MaterialesExternos::find($id);
            if ($material) {
                $material->cantidad = $newCantidad; // Usa el valor validado
                $material->save();
                session()->flash('success', 'Cantidad actualizada correctamente.');
                return redirect(request()->header('Referer'));
            } else {
                session()->flash('success', 'Material no encontrado.');
                return redirect(request()->header('Referer'));
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar la cantidad: ' . $e->getMessage());
        }
    }

    /**
     * Establece el ID del material a eliminar y muestra la modal de confirmación.
     *
     * @param int $id El ID del material externo a eliminar.
     * @return void
     */
    public function confirmDelete($id)
    {
        $this->materialToDeleteId = $id;
    }

    /**
     * Elimina el material externo de la base de datos.
     *
     * @return void
     */
    public function deleteMaterial()
    {
        if ($this->materialToDeleteId) {
            MaterialesExternos::find($this->materialToDeleteId)->delete();
            $this->materialToDeleteId = null; // Limpiar el ID después de la eliminación
            session()->flash('success', 'Material externo eliminado exitosamente.'); // Mensaje de éxito
            return redirect(request()->header('Referer'));
        }
    }

    public function render()
    {
        // Obtener los materiales externos, aplicar búsqueda si existe y paginar
        $materialesExternos = MaterialesExternos::query()
            ->when($this->search, function ($query) {
                // Buscar por descripcion o cualquier otro campo relevante
                $query->where('descripcion', 'like', '%' . $this->search . '%');
            })
            ->paginate(10); // Paginar 10 resultados por página

        return view('livewire.odontologia.materiales-externos-table', [
            'materialesExternos' => $materialesExternos,
        ]);
    }
}