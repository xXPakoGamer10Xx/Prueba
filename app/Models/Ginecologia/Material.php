<?php

namespace App\Models\Ginecologia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'materiales';

    /**
     * La clave primaria para el modelo.
     *
     * @var string
     */
    protected $primaryKey = 'id_material';

    /**
     * Indica si la clave primaria es auto-incremental.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * El tipo de dato de la clave primaria.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'id_material', // Agregamos el ID para poder crearlo manualmente
        'nombre_material',
        'cantidad_material',
    ];

    /**
     * Indica si el modelo debe tener timestamps (created_at y updated_at).
     * Como tu tabla no los tiene, lo ponemos en false.
     *
     * @var bool
     */
    public $timestamps = false;
}