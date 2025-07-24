<?php

namespace App\Livewire\Odontologia;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Odontologia\Presentacion;

class PresentacionesTable extends Component
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
        // Obtener los presentaciones, aplicar búsqueda si existe y paginar
        $presentaciones = Presentacion::query()
            ->when($this->search, function ($query) {
                // Buscar por descripción de la presentacion
                $query->where('descripcion', 'like', '%' . $this->search . '%');
            })
            ->paginate(10); // Paginar 10 resultados por página

        return view('livewire.odontologia.presentaciones-table', [
            'presentaciones' => $presentaciones,
        ]);
    }
}