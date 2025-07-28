<?php

namespace App\Livewire\Odontologia;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Odontologia\Insumo;

class InsumosTable extends Component
{
    use WithPagination;

    public $search = '';
    public $itemToDeleteId;
    public $message = '';
    public $messageType = '';
    protected $listeners = [
        'insumoAdded' => '$refresh',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Establece el ID del insumo a eliminar y muestra la modal de confirmación.
     *
     * @param int $id
     * @return void
     */
    public function confirmDelete($id)
    {
        $this->itemToDeleteId = $id;
        // Despacha un evento para abrir la modal de confirmación de eliminación
        $this->dispatch('open-modal', 'modalEliminarInsumo');
    }

    /**
     * Elimina el insumo de la base de datos.
     *
     * @return void
     */
    public function deleteInsumo()
    {
        try {
            if ($this->itemToDeleteId) {
                Insumo::find($this->itemToDeleteId)->delete();
                $this->itemToDeleteId = null; // Limpia el ID después de la eliminación
                $this->message = 'Insumo eliminado exitosamente.';
                $this->messageType = 'success';
            }
        } catch(\Exception $e) {
            $this->message = 'No se pudo eliminar el registro ya que está siendo utilizado en otras tablas.';
            $this->messageType = 'error';
        }
        session()->flash($this->messageType, $this->message);
        return redirect(request()->header('Referer'));

    }

    public function render()
    {
        // Obtener los insumos con sus relaciones 'laboratorio' y 'presentacion'
        $insumos = Insumo::query()
            // Carga directamente las relaciones 'laboratorio' y 'presentacion' del modelo Insumo
            ->with(['laboratorio', 'presentacion'])
            ->when($this->search, function ($query) {
                // Busca por clave, descripción, contenido del insumo,
                // o por la descripción del laboratorio o presentación a través de las relaciones
                $query->where('clave', 'like', '%' . $this->search . '%')
                      ->orWhere('descripcion', 'like', '%' . $this->search . '%')
                      ->orWhere('contenido', 'like', '%' . $this->search . '%')
                      ->orWhereHas('laboratorio', function ($q) {
                          $q->where('descripcion', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('presentacion', function ($q) {
                          $q->where('descripcion', 'like', '%' . $this->search . '%');
                      });
            })
            ->paginate(10); // Pagina 10 resultados por página

        return view('livewire.odontologia.insumos-table', [
            'insumos' => $insumos,
        ]);
    }
}