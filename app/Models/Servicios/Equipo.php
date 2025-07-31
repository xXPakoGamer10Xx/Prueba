<?php

namespace App\Models\Servicios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    use HasFactory;

    // Define el nombre de la tabla asociada al modelo.
    protected $table = 'equipos';

    // Define la clave primaria personalizada de la tabla 'equipos'.
    protected $primaryKey = 'id_equipo';

    // Indica al modelo que no se deben gestionar las marcas de tiempo (created_at, updated_at).
    // Esto es porque la tabla 'equipos' no tiene estas columnas.
    public $timestamps = false;

    // Especifica los atributos que pueden ser asignados masivamente.
    // Se ha eliminado 'cantidad' ya que no aparece en el esquema de la tabla 'equipos'.
    protected $fillable = [
        'nombre',
        'marca',
        'modelo',
        'frecuencia_mantenimiento',
    ];

    /**
     * Define la relación de uno a muchos (hasMany) con el modelo Inventario.
     * Un Equipo puede tener muchos Inventarios asociados.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventarios()
    {
        // Un equipo tiene muchos inventarios, donde 'id_equipo_fk'
        // es la clave foránea en la tabla 'inventarios' y 'id_equipo' es la clave primaria en este modelo.
        return $this->hasMany(Inventario::class, 'id_equipo_fk', 'id_equipo');
    }
}
