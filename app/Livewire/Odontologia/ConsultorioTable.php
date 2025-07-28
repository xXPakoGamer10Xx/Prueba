<?php

namespace App\Livewire\Odontologia;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Odontologia\Consultorio;
use Illuminate\Support\Facades\Validator; // Importar la fachada Validator

class ConsultorioTable extends Component
{
    use WithPagination;

    public $search = ''; // Propiedad para la búsqueda
    public $insumoToDeleteId; // Propiedad para almacenar el ID del insumo a eliminar

    protected $queryString = ['search' => ['except' => '']];

    // Listeners para eventos de Livewire (para refrescar la tabla al agregar/eliminar insumos)
    protected $listeners = ['insumoAdded' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Actualiza la cantidad de un insumo en la tabla consultorio.
     * Se llama al perder el foco o al presionar Enter en el input de cantidad.
     *
     * @param int $consultorioId El ID del registro en la tabla 'consultorio'.
     * @param int $newCantidad La nueva cantidad a establecer.
     * @return void
     */
    public function updateCantidad($consultorioId, $newCantidad)
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
            $consultorioEntry = Consultorio::find($consultorioId);

            if ($consultorioEntry) {
                $consultorioEntry->cantidad = $newCantidad; // Usa el valor validado
                $consultorioEntry->save();
                session()->flash('success', 'Cantidad actualizada exitosamente.');
                return redirect(request()->header('Referer'));
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar la cantidad: ' . $e->getMessage());
        }
    }

    /**
     * Confirma la eliminación de un insumo y establece el ID.
     *
     * @param int $id
     * @return void
     */
    public function confirmDelete($id)
    {
        $this->insumoToDeleteId = $id;
        $this->dispatch('open-modal', 'modalEliminarInsumo'); // Despacha un evento para abrir la modal
    }

    /**
     * Elimina el insumo del consultorio.
     *
     * @return void
     */
    public function deleteInsumo()
    {
        if ($this->insumoToDeleteId) {
            Consultorio::find($this->insumoToDeleteId)->delete();
            $this->insumoToDeleteId = null; // Limpia el ID después de la eliminación
            session()->flash('success', 'Insumo eliminado exitosamente.'); // Opcional: mensaje de éxito
            return redirect(request()->header('Referer'));
        }
    }


    public function render()
    {
        $insumosConsultorio = Consultorio::query()
            // Eager load relationships: insumo, and then its laboratorio and presentacion
            ->with(['insumo.laboratorio', 'insumo.presentacion'])
            // Selecciona solo los campos de la tabla 'consultorio' necesarios, la relación traerá el resto
            ->select(
                'consultorio.id_insumo_consultorio',
                'consultorio.cantidad',
                'consultorio.id_insumo_fk' // Importante para que la relación 'insumo' se cargue
            )
            ->when($this->search, function ($query) {
                // Modifica las condiciones de búsqueda para usar las relaciones eager-loaded
                $query->whereHas('insumo', function ($subQuery) {
                    $subQuery->where('clave', 'like', '%' . $this->search . '%')
                             ->orWhere('descripcion', 'like', '%' . $this->search . '%')
                             ->orWhere('contenido', 'like', '%' . $this->search . '%');
                })
                // También puedes buscar por laboratorio y presentación a través de la relación de insumo
                ->orWhereHas('insumo.laboratorio', function ($subQuery) {
                    $subQuery->where('descripcion', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('insumo.presentacion', function ($subQuery) {
                    $subQuery->where('descripcion', 'like', '%' . $this->search . '%');
                });
            })
            ->paginate(10); // Pagina los resultados, 10 por página

        return view('livewire.odontologia.consultorio-table', [
            'insumosConsultorio' => $insumosConsultorio,
        ]);
    }
}