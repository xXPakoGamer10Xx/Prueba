<?php

namespace App\Http\Controllers\Ginecologia;

use App\Http\Controllers\Controller;

class ReporteController extends Controller
{
    /**
     * Muestra la vista del formulario de reportes.
     */
    public function index()
    {
        return view('ginecologia.reporte');
    }
}