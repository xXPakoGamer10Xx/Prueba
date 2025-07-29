<?php

namespace App\Models\Servicios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcesoBaja extends Model
{
    use HasFactory;

    /**
     * 🛠️ CORRECCIÓN: El nombre de la tabla debe estar en plural
     * para coincidir con las convenciones de Laravel y la migración.
     */
    protected $table = 'procesos_baja';

    protected $primaryKey = 'id_proceso_baja';
    public $timestamps = false;
    protected $fillable = ['id_inventario_fk', 'id_mantenimiento_fk', 'motivo', 'estado'];

    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'id_inventario_fk', 'id_inventario');
    }

    public function mantenimiento()
    {
        return $this->belongsTo(Mantenimiento::class, 'id_mantenimiento_fk', 'id_mantenimiento');
    }
}
