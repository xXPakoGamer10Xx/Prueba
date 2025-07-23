<?php

namespace App\Livewire\Odontologia\Consultorio;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Odontologia\Consultorio;
use App\Models\Odontologia\Insumo;
use App\Models\Odontologia\Laboratorio;
use App\Models\Odontologia\Presentacion;

class InsumosTable extends Component
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
        // Define los datos que quieres validar explícitamente en un array
        // La clave 'cantidad_ingresada' no es una propiedad pública, es solo un nombre para la validación
        $dataToValidate = ['cantidad_ingresada' => $newCantidad];

        // Define las reglas y mensajes de validación para esos datos
        $rules = [
            'cantidad_ingresada' => 'required|integer|min:0',
        ];
        $messages = [
            'cantidad_ingresada.required' => 'La cantidad es obligatoria.',
            'cantidad_ingresada.integer' => 'La cantidad debe ser un número entero.',
            'cantidad_ingresada.min' => 'La cantidad no puede ser negativa.',
        ];

        // Realiza la validación
        try {
            $this->validate($dataToValidate, $rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Livewire capturará y mostrará estos errores automáticamente en la vista
            // Opcional: Si quieres un mensaje flash global para errores de validación, puedes usarlo aquí
            session()->flash('error', 'Error de validación: ' . implode(', ', \Illuminate\Support\Arr::flatten($e->errors())));
            throw $e; // Es importante volver a lanzar la excepción para que Livewire la maneje
        }


        try {
            $consultorioEntry = Consultorio::find($consultorioId);

            if ($consultorioEntry) {
                $consultorioEntry->cantidad = $dataToValidate['cantidad_ingresada']; // Usa el valor validado
                $consultorioEntry->save();
                session()->flash('message', 'Cantidad actualizada exitosamente.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar la cantidad: ' . $e->getMessage());
            // Para depuración: \Log::error("Error al actualizar cantidad: " . $e->getMessage());
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
            $this->dispatch('close-modal', 'modalEliminarInsumo'); // Despacha un evento para cerrar la modal
            session()->flash('message', 'Insumo eliminado exitosamente.'); // Opcional: mensaje de éxito
            $this->dispatch('insumoAdded'); // Despachar para refrescar otras tablas si escuchan
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

        return view('livewire.odontologia.consultorio.insumos-table', [
            'insumosConsultorio' => $insumosConsultorio,
        ]);
    }
}