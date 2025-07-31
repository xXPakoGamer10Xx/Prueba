<?php

namespace App\Models\Servicios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Importa los modelos relacionados para definir las relaciones correctamente.
use App\Models\Servicios\Inventario;
use App\Models\Servicios\Mantenimiento;

class ProcesoBaja extends Model
{
    use HasFactory;

    // Define el nombre de la tabla asociada al modelo.
    protected $table = 'procesos_baja';

    // Define la clave primaria personalizada de la tabla 'procesos_baja'.
    protected $primaryKey = 'id_proceso_baja';

    // Indica al modelo que no se deben gestionar las marcas de tiempo (created_at, updated_at).
    // Esto es porque la tabla 'procesos_baja' no tiene estas columnas.
    public $timestamps = false;

    // Especifica los atributos que pueden ser asignados masivamente.
    // Se han utilizado los nombres de columna exactos de la base de datos.
    protected $fillable = [
        'id_inventario_fk',
        'id_mantenimiento_fk',
        'motivo',
        'estado',
        'fecha_baja'
    ];

    /**
     * Define la relación de pertenencia (belongsTo) con el modelo Inventario.
     * Un ProcesoBaja pertenece a un Inventario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inventario()
    {
        // La clave foránea en la tabla 'procesos_baja' es 'id_inventario_fk'.
        // La clave primaria en la tabla 'inventarios' es 'id_inventario'.
        return $this->belongsTo(Inventario::class, 'id_inventario_fk', 'id_inventario');
    }

    /**
     * Define la relación de pertenencia (belongsTo) con el modelo Mantenimiento.
     * Un ProcesoBaja pertenece a un Mantenimiento.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mantenimiento()
    {
        // La clave foránea en la tabla 'procesos_baja' es 'id_mantenimiento_fk'.
        // La clave primaria en la tabla 'mantenimientos' es 'id_mantenimiento'.
        return $this->belongsTo(Mantenimiento::class, 'id_mantenimiento_fk', 'id_mantenimiento');
    }
}
