<?php

namespace App\Livewire\Servicios;

use App\Models\servicios\Inventario;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Componente de Livewire para gestionar la tabla de Inventarios.
 * Se encarga de la paginación y visualización de los registros de inventario.
 */
class GestionarInventarios extends Component
{
    use WithPagination;

    /**
     * Define el tema de paginación para que coincida con Tailwind CSS.
     *
     * @var string
     */
    protected $paginationTheme = 'tailwind';

    /**
     * Renderiza la vista del componente con los datos paginados.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.servicios.gestionar-inventarios', [
            'inventarios' => Inventario::paginate(10), // Paginamos solo los inventarios
        ]);
    }
}