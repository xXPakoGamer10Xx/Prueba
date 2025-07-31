<?php

namespace App\Http\Controllers\Ginecologia;

use App\Http\Controllers\Controller;
use App\Models\Ginecologia\CirugiaGeneral;
use App\Models\Ginecologia\CirugiaGinecologica;
use App\Models\Ginecologia\Doctor;
use App\Models\Ginecologia\Paciente;
use App\Models\Ginecologia\Diagnostico;

class CirugiaPageController extends Controller
{
    public function index()
    {
        $cirugiasGenerales = CirugiaGeneral::with(['paciente', 'doctor', 'diagnostico'])->paginate(5, ['*'], 'generales_page');
        $cirugiasGinecologicas = CirugiaGinecologica::with(['paciente', 'doctor', 'diagnostico'])->paginate(5, ['*'], 'ginecologicas_page');

        $pacientes = Paciente::all();
        $doctores = Doctor::all();
        $diagnosticos = Diagnostico::all();

        return view('ginecologia.cirugia', compact(
            'cirugiasGenerales', 
            'cirugiasGinecologicas',
            'pacientes',
            'doctores',
            'diagnosticos'
        ));
    }
}