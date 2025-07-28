<?php

namespace App\Models\Ginecologia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    protected $table = 'pacientes';
    protected $primaryKey = 'id_paciente';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'id_paciente',
        'nombre_paciente',
        'apellido1_paciente',
        'apellido2_paciente',
        'fecha_nac',
        'genero_paciente',
    ];

    /**
     * Un atributo calculado para obtener el nombre completo fácilmente.
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre_paciente} {$this->apellido1_paciente} {$this->apellido2_paciente}";
    }
}