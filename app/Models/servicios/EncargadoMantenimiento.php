<?php

namespace App\Models\Servicios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EncargadoMantenimiento extends Model
{
    use HasFactory;
    protected $table = 'encargados_mantenimiento';
    protected $primaryKey = 'id_encargado_man';
    public $timestamps = false;
    protected $fillable = ['nombre', 'apellidos', 'cargo', 'contacto'];
}