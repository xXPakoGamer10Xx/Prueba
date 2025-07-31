<?php

namespace App\Models\Servicios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Garantia extends Model
{
    use HasFactory;

    // Define el nombre de la tabla asociada al modelo.
    protected $table = 'garantias';

    // Define la clave primaria personalizada de la tabla 'garantias'.
    protected $primaryKey = 'id_garantia';

    // Indica al modelo que no se deben gestionar las marcas de tiempo (created_at, updated_at).
    // Esto es porque la tabla 'garantias' no tiene estas columnas.
    public $timestamps = false;

    // Especifica los atributos que pueden ser asignados masivamente.
    protected $fillable = [
        'status',
        'empresa',
        'contacto',
        'fecha_garantia'
    ];

    /**
     * Define la relación de uno a muchos (hasMany) con el modelo Inventario.
     * Una Garantia puede tener muchos Inventarios asociados.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventarios()
    {
        // Una garantía tiene muchos inventarios, donde 'id_garantia_fk'
        // es la clave foránea en la tabla 'inventarios' y 'id_garantia' es la clave primaria en este modelo.
        return $this->hasMany(Inventario::class, 'id_garantia_fk', 'id_garantia');
    }
}
