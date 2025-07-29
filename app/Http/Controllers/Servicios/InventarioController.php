<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    /**
     * Muestra la vista principal del inventario, que cargará el componente de Livewire.
     */
    public function index()
    {
        return view('servicios.inventario');
    }
}
