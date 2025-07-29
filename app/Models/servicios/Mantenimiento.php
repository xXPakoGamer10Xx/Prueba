<?php

namespace App\Models\Servicios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mantenimiento extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     * @var string
     */
    protected $table = 'mantenimientos';

    /**
     * La clave primaria de la tabla.
     * @var string
     */
    protected $primaryKey = 'id_mantenimiento';

    /**
     * Indica si el modelo debe tener timestamps (created_at y updated_at).
     * @var bool
     */
    public $timestamps = false;

    /**
     * 🛡️ CAMPO REQUERIDO: Atributos que se pueden asignar de forma masiva.
     * Esto le dice a Laravel qué campos son seguros para rellenar desde un formulario.
     * He añadido las claves foráneas con el sufijo "_fk" que es la convención que usas.
     */
    protected $fillable = [
        'id_inventario_fk',
        'id_encargado_man_fk',
        'fecha',
        'tipo',
        'refacciones_material',
        'observaciones',
    ];

    /**
     * Define la relación: un Mantenimiento pertenece a un Inventario.
     */
    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'id_inventario_fk', 'id_inventario');
    }

    /**
     * Define la relación: un Mantenimiento es realizado por un Encargado.
     */
    public function encargadoMantenimiento()
    {
        return $this->belongsTo(EncargadoMantenimiento::class, 'id_encargado_man_fk', 'id_encargado_man');
    }
}
