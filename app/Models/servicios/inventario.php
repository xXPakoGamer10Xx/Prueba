<?php

namespace App\Models\Servicios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Servicios\Area; // Corregido 'servicios' a 'Servicios'
use App\Models\Servicios\Equipo; // Corregido 'servicios' a 'Servicios'
use App\Models\Servicios\Garantia; // Corregido 'servicios' a 'Servicios'

class Inventario extends Model
{
    use HasFactory;

    protected $table = 'inventarios';
    protected $primaryKey = 'id_inventario';
    public $timestamps = false;

    protected $fillable = [
        'id_equipo_fk',
        'num_serie',
        'num_serie_sicopa',
        'num_serie_sia',
        'pertenencia',
        'status',
        'id_area_fk',
        'id_garantia_fk',
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'id_equipo_fk', 'id_equipo');
    }

    public function area()
    {
        // Asegúrate de que la clave foránea aquí también coincida con tu DB si es diferente
        return $this->belongsTo(Area::class, 'id_area_fk', 'id_area'); // Asumo id_area_fk en DB
    }

    public function garantia()
    {
        // Asegúrate de que la clave foránea aquí también coincida con tu DB si es diferente
        return $this->belongsTo(Garantia::class, 'id_garantia_fk', 'id_garantia'); // Asumo id_garantia_fk en DB
    }

    // ¡ESTA ES LA CLAVE! Asegúrate de que la clave foránea sea 'id_inventario'
    public function mantenimientos()
    {
        return $this->hasMany(Mantenimiento::class, 'id_inventario', 'id_inventario');
    }
}
