<?php

namespace App\Models\Servicios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\servicios\Area; 
use App\Models\servicios\Equipo; 
use App\Models\servicios\Garantia; 

class Inventario extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'inventarios';

    /**
     * La clave primaria para el modelo.
     *
     * @var string
     */
    protected $primaryKey = 'id_inventario';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_equipo_fk',
        'id_area_fk',
        'num_serie',
        'num_serie_sicopa',
        'num_serie_sia',
        'pertenencia',
        'status',
        'id_garantia_fk',
    ];

    /**
     * Define la relación "pertenece a" con el modelo Equipo.
     * Se especifica la clave foránea local ('id_equipo_fk') y la clave del propietario ('id_equipo').
     */
    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'id_equipo_fk', 'id_equipo');
    }

    /**
     * Define la relación "pertenece a" con el modelo Area.
     * Se especifica la clave foránea local ('id_area_fk') y la clave del propietario ('id_area').
     */
    public function area()
    {
        return $this->belongsTo(Area::class, 'id_area_fk', 'id_area');
    }

    /**
     * Define la relación "pertenece a" con el modelo Garantia.
     * Se especifica la clave foránea local ('id_garantia_fk') y la clave del propietario ('id_garantia').
     */
    public function garantia()
    {
        return $this->belongsTo(Garantia::class, 'id_garantia_fk', 'id_garantia');
    }
}
