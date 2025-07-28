<?php

namespace App\Models\Odontologia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialesExternos extends Model
{
    use HasFactory;

    // Especifica el nombre de la tabla si no sigue la convención de nombres de Laravel (plural del nombre del modelo)
    protected $table = 'materiales_externos';

    // Especifica la clave primaria si no es 'id'
    protected $primaryKey = 'id_material';

    // Indica si la clave primaria es auto-incrementable (por defecto es true)
    public $incrementing = true;

    // Especifica el tipo de la clave primaria si no es entero (por defecto es int)
    protected $keyType = 'int';

    // Define los campos que se pueden asignar masivamente (fillable)
    protected $fillable = [
        'descripcion',
        'cantidad',
        // Asegúrate de añadir aquí todos los campos que deseas poder asignar masivamente
    ];

    // Opcional: Si no usas timestamps (created_at, updated_at), desactívalos
    public $timestamps = false;
}