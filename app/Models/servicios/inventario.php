<?php

namespace App\Models\servicios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Se añaden los 'use' para los modelos relacionados.
use App\Models\servicios\Area; 
use App\Models\servicios\Equipo; 
use App\Models\servicios\Garantia; 

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

    // Relaciones con otras tablas
    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'id_equipo_fk', 'id_equipo');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'id_area_fk', 'id_area');
    }

    public function garantia()
    {
        return $this->belongsTo(Garantia::class, 'id_garantia_fk', 'id_garantia');
    }
}
