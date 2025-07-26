<?php

namespace App\Livewire\Servicios;

use App\Models\servicios\Equipo;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Componente de Livewire para gestionar la tabla de Equipos.
 */
class GestionarEquipos extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public function render()
    {
        return view('livewire.servicios.gestionar-equipos', [
            'equipos' => Equipo::paginate(10),
        ]);
    }
}