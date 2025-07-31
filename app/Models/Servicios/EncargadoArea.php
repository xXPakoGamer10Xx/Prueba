<?php

namespace App\Models\Servicios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EncargadoArea extends Model
{
    use HasFactory;

    // Define el nombre de la tabla asociada al modelo.
    protected $table = 'encargados_area';

    // Define la clave primaria personalizada de la tabla 'encargados_area'.
    protected $primaryKey = 'id_encargado_area';

    // Especifica los atributos que pueden ser asignados masivamente.
    protected $fillable = [
        'nombre',
        'apellidos',
        'cargo'
    ];

    /**
     * Indica al modelo que no se deben gestionar las marcas de tiempo (created_at, updated_at).
     * Esto es porque la tabla 'encargados_area' no tiene estas columnas.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Define la relación de uno a muchos (hasMany) con el modelo Area.
     * Un EncargadoArea puede tener muchas Areas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function areas()
    {
        // Un encargado de área tiene muchas áreas, donde 'id_encargado_area_fk' es la clave foránea en la tabla 'areas'.
        // 'id_encargado_area' es la clave primaria en la tabla actual.
        return $this->hasMany(Area::class, 'id_encargado_area_fk', 'id_encargado_area');
    }
}
