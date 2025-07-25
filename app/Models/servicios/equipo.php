<?php

namespace App\Models\servicios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    use HasFactory;
    protected $table = 'equipos';
    protected $primaryKey = 'id_equipo';
    public $timestamps = false;
    protected $fillable = ['nombre', 'marca', 'modelo', 'cantidad', 'frecuencia_mantenimiento'];
}
