<?php

namespace App\Models\Servicios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    // Define la clave primaria personalizada de la tabla 'areas'.
    protected $primaryKey = 'id_area';

    // Especifica los atributos que pueden ser asignados masivamente.
    protected $fillable = [
        'nombre',
        'id_encargado_area_fk'
    ];

    /**
     * Indica al modelo que no se deben gestionar las marcas de tiempo (created_at, updated_at).
     * Esto es porque la tabla 'areas' no tiene estas columnas.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Define la relación de pertenencia (belongsTo) con el modelo EncargadoArea.
     * Un Area pertenece a un EncargadoArea.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function encargado()
    {
        // El primer parámetro es el modelo relacionado.
        // El segundo parámetro es la clave foránea en la tabla 'areas' (id_encargado_area_fk).
        // El tercer parámetro es la clave primaria en la tabla 'encargados_area' (id_encargado_area).
        return $this->belongsTo(EncargadoArea::class, 'id_encargado_area_fk', 'id_encargado_area');
    }

    /**
     * Define la relación de uno a muchos (hasMany) con el modelo Inventario.
     * Un Area puede tener muchos Inventarios.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventarios()
    {
        // Un área tiene muchos inventarios, donde 'id_area_fk' es la clave foránea en la tabla 'inventarios'.
        return $this->hasMany(\App\Models\Servicios\Inventario::class, 'id_area_fk', 'id_area');
    }
}
