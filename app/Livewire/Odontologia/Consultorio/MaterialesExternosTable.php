<?php

namespace App\Livewire\Odontologia\Consultorio;

use Livewire\Component;
use Livewire\WithPagination; // Importar el trait de paginación
use App\Models\Odontologia\MaterialesExternos; // Importar el modelo de Materiales Externos

class MaterialesExternosTable extends Component
{
    use WithPagination;

    public $search = ''; // Propiedad para la búsqueda, si se desea implementar

    // Opcional: Para resetear la paginación cuando cambia la búsqueda
    public function updatingSearch()
    {
        $this->resetPage();
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

        return view('livewire.odontologia.consultorio.materiales-externos-table', [
            'materialesExternos' => $materialesExternos,
        ]);
    }
}