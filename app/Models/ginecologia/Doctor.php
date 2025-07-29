<?php

namespace App\Models\Ginecologia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;
    
    // Datos básicos del modelo
    protected $table = 'doctores';
    protected $primaryKey = 'id_doctor';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    // CAMBIO APLICADO AQUÍ
    /**
     * Define un "accesor" para obtener el nombre completo del doctor.
     * Ahora podemos usar la propiedad "nombre_completo" en cualquier lugar,
     * como si fuera una columna más de la tabla.
     *
     * @return string
     */
    public function getNombreCompletoAttribute()
    {
        // Concatena el nombre y los dos apellidos
        return "{$this->nombre_doctor} {$this->apellido1_doctor} {$this->apellido2_doctor}";
    }
}