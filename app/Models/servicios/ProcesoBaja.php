<?php

namespace App\Models\Servicios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcesoBaja extends Model
{
    use HasFactory;

    protected $table = 'procesos_baja'; // El nombre de la tabla es correcto
    protected $primaryKey = 'id_proceso_baja';
    public $timestamps = false;

    protected $fillable = [
        // CAMBIO AQUÍ: Usar los nombres de columna exactos de la DB
        'id_inventario_fk',
        'id_mantenimiento_fk',
        'motivo',
        'estado',
        'fecha_baja' // <--- ¡AÑADIDO AQUÍ!
    ];

    /**
     * Define la relación "pertenece a" con el modelo Inventario.
     * La clave foránea en 'procesos_baja' es 'id_inventario_fk'.
     */
    public function inventario()
    {
        // CAMBIO AQUÍ: La clave foránea en la relación debe ser 'id_inventario_fk'
        return $this->belongsTo(Inventario::class, 'id_inventario_fk', 'id_inventario');
    }

    /**
     * Define la relación "pertenece a" con el modelo Mantenimiento.
     * La clave foránea en 'procesos_baja' es 'id_mantenimiento_fk'.
     */
    public function mantenimiento()
    {
        // CAMBIO AQUÍ: La clave foránea en la relación debe ser 'id_mantenimiento_fk'
        return $this->belongsTo(Mantenimiento::class, 'id_mantenimiento_fk', 'id_mantenimiento');
    }
}
