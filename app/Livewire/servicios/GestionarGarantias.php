<?php

namespace App\Livewire\Servicios;

use App\Models\servicios\Garantia;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Componente de Livewire para gestionar la tabla de Garantías.
 */
class GestionarGarantias extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public function render()
    {
        return view('livewire.servicios.gestionar-garantias', [
            'garantias' => Garantia::paginate(10),
        ]);
    }
}