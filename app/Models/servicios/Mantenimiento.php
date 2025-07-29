<?php

namespace App\Models\Servicios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mantenimiento extends Model
{
    use HasFactory;

    protected $table = 'mantenimientos';
    protected $primaryKey = 'id_mantenimiento';
    public $timestamps = false;

    protected $fillable = [
        // CAMBIO AQUÍ: Volver a 'id_inventario' y 'id_encargado_man'
        'id_inventario',
        'id_encargado_man',
        'fecha',
        'tipo',
        'refacciones_material',
        'observaciones',
    ];

    public function inventario()
    {
        // CAMBIO AQUÍ: La clave foránea en la relación debe ser 'id_inventario'
        return $this->belongsTo(Inventario::class, 'id_inventario', 'id_inventario');
    }

    public function encargadoMantenimiento()
    {
        // CAMBIO AQUÍ: La clave foránea en la relación debe ser 'id_encargado_man'
        return $this->belongsTo(EncargadoMantenimiento::class, 'id_encargado_man', 'id_encargado_man');
    }
}
