<?php

namespace App\Models\Servicios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Importa los modelos relacionados para definir las relaciones correctamente.
use App\Models\Servicios\Inventario;
use App\Models\Servicios\EncargadoMantenimiento;
use App\Models\Servicios\ProcesoBaja; // Añadido para la relación hasMany

class Mantenimiento extends Model
{
    use HasFactory;

    // Define el nombre de la tabla asociada al modelo.
    protected $table = 'mantenimientos';

    // Define la clave primaria personalizada de la tabla 'mantenimientos'.
    protected $primaryKey = 'id_mantenimiento';

    // Indica al modelo que no se deben gestionar las marcas de tiempo (created_at, updated_at).
    // Esto es porque la tabla 'mantenimientos' no tiene estas columnas.
    public $timestamps = false;

    // Especifica los atributos que pueden ser asignados masivamente.
    // IMPORTANTE: Se han corregido los nombres de las claves foráneas
    // para que coincidan con el esquema de tu base de datos ('id_inventario_fk', 'id_encargado_man_fk').
    protected $fillable = [
        'id_inventario_fk',       // Corregido de 'id_inventario'
        'id_encargado_man_fk',    // Corregido de 'id_encargado_man'
        'fecha',
        'tipo',
        'refacciones_material',
        'observaciones',
    ];

    /**
     * Define la relación de pertenencia (belongsTo) con el modelo Inventario.
     * Un Mantenimiento pertenece a un Inventario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inventario()
    {
        // La clave foránea en la tabla 'mantenimientos' es 'id_inventario_fk'.
        // La clave primaria en la tabla 'inventarios' es 'id_inventario'.
        // Esto corrige el error 'Unknown column id_inventario' en el INSERT.
        return $this->belongsTo(Inventario::class, 'id_inventario_fk', 'id_inventario');
    }

    /**
     * Define la relación de pertenencia (belongsTo) con el modelo EncargadoMantenimiento.
     * Un Mantenimiento pertenece a un EncargadoMantenimiento.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function encargadoMantenimiento()
    {
        // La clave foránea en la tabla 'mantenimientos' es 'id_encargado_man_fk'.
        // La clave primaria en la tabla 'encargados_mantenimiento' es 'id_encargado_man'.
        return $this->belongsTo(EncargadoMantenimiento::class, 'id_encargado_man_fk', 'id_encargado_man');
    }

    /**
     * Define la relación de uno a uno (hasOne) con el modelo ProcesoBaja.
     * Un Mantenimiento puede tener un ProcesoBaja asociado.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function procesoBaja()
    {
        // Un mantenimiento tiene un proceso de baja, donde 'id_mantenimiento_fk'
        // es la clave foránea en la tabla 'procesos_baja' y 'id_mantenimiento' es la clave primaria en este modelo.
        return $this->hasOne(ProcesoBaja::class, 'id_mantenimiento_fk', 'id_mantenimiento');
    }
}
