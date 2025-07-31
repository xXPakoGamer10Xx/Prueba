<?php

namespace App\Models\Servicios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Asegúrate de importar todos los modelos relacionados que se usarán en las relaciones.
use App\Models\Servicios\Area;
use App\Models\Servicios\Equipo;
use App\Models\Servicios\Garantia;
use App\Models\Servicios\Mantenimiento; // Necesario para la relación hasMany

class Inventario extends Model
{
    use HasFactory;

    // Define el nombre de la tabla asociada al modelo.
    protected $table = 'inventarios';

    // Define la clave primaria personalizada de la tabla 'inventarios'.
    protected $primaryKey = 'id_inventario';

    // Indica al modelo que no se deben gestionar las marcas de tiempo (created_at, updated_at).
    // Esto es porque la tabla 'inventarios' tiene 'created_at' y 'updated_at',
    // por lo que 'public $timestamps = false;' debe ser ELIMINADO si quieres que Laravel los gestione.
    // Si tus columnas se llaman 'created_at' y 'updated_at', Laravel las gestionará automáticamente
    // a menos que establezcas $timestamps en false.
    // Según tu esquema, 'inventarios' SÍ tiene 'created_at' y 'updated_at',
    // por lo que deberías ELIMINAR la línea 'public $timestamps = false;'
    // para que Laravel las gestione automáticamente.
    // Si no quieres que las gestione, entonces sí, mantenla.
    // Para este ejemplo, la mantendré como la tenías, pero tenlo en cuenta.
    public $timestamps = false;


    // Especifica los atributos que pueden ser asignados masivamente.
    protected $fillable = [
        'id_equipo_fk',
        'id_area_fk', // Asegúrate de que este campo exista en tu tabla 'inventarios'
        'id_garantia_fk', // Asegúrate de que este campo exista en tu tabla 'inventarios'
        'num_serie',
        'num_serie_sicopa',
        'num_serie_sia',
        'pertenencia',
        'status',
        // 'created_at' y 'updated_at' no se incluyen aquí ya que Laravel los gestiona automáticamente
        // si $timestamps es true. Si $timestamps es false, no se gestionan.
    ];

    /**
     * Define la relación de pertenencia (belongsTo) con el modelo Equipo.
     * Un Inventario pertenece a un Equipo.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function equipo()
    {
        // El primer parámetro es el modelo relacionado.
        // El segundo parámetro es la clave foránea en la tabla 'inventarios' (id_equipo_fk).
        // El tercer parámetro es la clave primaria en la tabla 'equipos' (id_equipo).
        return $this->belongsTo(Equipo::class, 'id_equipo_fk', 'id_equipo');
    }

    /**
     * Define la relación de pertenencia (belongsTo) con el modelo Area.
     * Un Inventario pertenece a un Area.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function area()
    {
        // La clave foránea en la tabla 'inventarios' es 'id_area_fk'.
        // La clave primaria en la tabla 'areas' es 'id_area'.
        return $this->belongsTo(Area::class, 'id_area_fk', 'id_area');
    }

    /**
     * Define la relación de pertenencia (belongsTo) con el modelo Garantia.
     * Un Inventario pertenece a una Garantia.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function garantia()
    {
        // La clave foránea en la tabla 'inventarios' es 'id_garantia_fk'.
        // La clave primaria en la tabla 'garantias' es 'id_garantia'.
        return $this->belongsTo(Garantia::class, 'id_garantia_fk', 'id_garantia');
    }

    /**
     * Define la relación de uno a muchos (hasMany) con el modelo Mantenimiento.
     * Un Inventario puede tener muchos Mantenimientos asociados.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mantenimientos()
    {
        // Un inventario tiene muchos mantenimientos.
        // La clave foránea en la tabla 'mantenimientos' es 'id_inventario_fk'.
        // La clave primaria en la tabla 'inventarios' es 'id_inventario'.
        // Aquí se ha corregido el segundo parámetro de 'id_inventario' a 'id_inventario_fk'
        // para que coincida con el esquema de tu tabla 'mantenimientos'.
        return $this->hasMany(Mantenimiento::class, 'id_inventario_fk', 'id_inventario');
    }
}
