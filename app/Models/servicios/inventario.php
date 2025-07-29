<?php

namespace App\Models\Servicios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;
    protected $table = 'inventarios';
    protected $primaryKey = 'id_inventario';
    public $timestamps = false;

    /**
     * 🛡️ CAMBIO AÑADIDO: Atributos que se pueden asignar de forma masiva.
     * Esto es necesario para que los métodos create() y updateOrCreate() funcionen.
     */
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

    /**
     * Define la relación: un Inventario pertenece a un Equipo.
     */
    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'id_equipo_fk', 'id_equipo');
    }

    /**
     * RELACIÓN AÑADIDA: Un Inventario pertenece a un Área.
     */
    public function area()
    {
        return $this->belongsTo(Area::class, 'id_area_fk', 'id_area');
    }

    /**
     * RELACIÓN AÑADIDA: Un Inventario tiene una Garantía.
     */
    public function garantia()
    {
        return $this->belongsTo(Garantia::class, 'id_garantia_fk', 'id_garantia');
    }
}