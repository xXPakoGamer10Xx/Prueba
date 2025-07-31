<?php

namespace App\Models\Servicios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EncargadoMantenimiento extends Model
{
    use HasFactory;

    // Define el nombre de la tabla asociada al modelo.
    protected $table = 'encargados_mantenimiento';

    // Define la clave primaria personalizada de la tabla 'encargados_mantenimiento'.
    protected $primaryKey = 'id_encargado_man';

    // Indica al modelo que no se deben gestionar las marcas de tiempo (created_at, updated_at).
    // Esto es porque la tabla 'encargados_mantenimiento' no tiene estas columnas.
    public $timestamps = false;

    // Especifica los atributos que pueden ser asignados masivamente.
    protected $fillable = [
        'nombre',
        'apellidos',
        'cargo',
        'contacto'
    ];

    /**
     * Define la relación de uno a muchos (hasMany) con el modelo Mantenimiento.
     * Un EncargadoMantenimiento puede tener muchos Mantenimientos asociados.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mantenimientos()
    {
        // Un encargado de mantenimiento tiene muchos mantenimientos, donde 'id_encargado_man_fk'
        // es la clave foránea en la tabla 'mantenimientos' y 'id_encargado_man' es la clave primaria en este modelo.
        return $this->hasMany(\App\Models\Servicios\Mantenimiento::class, 'id_encargado_man_fk', 'id_encargado_man');
    }
}
