<?php

namespace App\Livewire\Odontologia;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Odontologia\Laboratorio;

class LaboratoriosTable extends Component
{
    use WithPagination;

    public $search = ''; // Propiedad para búsqueda, si se desea implementar

    // Opcional: Para resetear la paginación cuando cambia la búsqueda
    public function updatingSearch()
    {
        $this->resetPage();
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