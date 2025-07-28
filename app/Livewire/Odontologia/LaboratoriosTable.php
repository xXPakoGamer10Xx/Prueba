<?php

namespace App\Livewire\Odontologia;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Odontologia\Laboratorio;

class LaboratoriosTable extends Component
{
    use WithPagination;

    public $search = ''; // Propiedad para búsqueda, si se desea implementar
    public $laboratorioToDeleteId;
    protected $listeners = ['insumoAdded' => '$refresh'];


    // Opcional: Para resetear la paginación cuando cambia la búsqueda
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->laboratorioToDeleteId = $id;
        // Despacha un evento para abrir la modal de confirmación de eliminación
        $this->dispatch('open-modal', 'modalEliminarLaboratorio');
    }

    /**
     * Elimina el insumo de la base de datos.
     *
     * @return void
     */
    public function deleteLaboratorio()
    {
        if ($this->laboratorioToDeleteId) {
            Laboratorio::find($this->laboratorioToDeleteId)->delete();
            $this->laboratorioToDeleteId = null; // Limpia el ID después de la eliminación
            $this->dispatch('close-modal', 'modalEliminarLaboratorio'); // Cierra la modal
            session()->flash('message', 'Laboratorio eliminado exitosamente.'); // Mensaje de éxito
            $this->dispatch('insumoAdded'); // Despacha un evento para refrescar la tabla
        }
    }

    public function render()
    {
        // Obtener los laboratorios, aplicar búsqueda si existe y paginar
        $laboratorios = Laboratorio::query()
            ->when($this->search, function ($query) {
                // Buscar por descripción del laboratorio
                $query->where('descripcion', 'like', '%' . $this->search . '%');
            })
            ->paginate(10); // Paginar 10 resultados por página

        return view('livewire.odontologia.laboratorios-table', [
            'laboratorios' => $laboratorios,
        ]);
    }
}