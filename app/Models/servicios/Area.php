<?php

namespace App\Models\servicios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'id_area';
    protected $fillable = ['nombre', 'id_encargado_area_fk'];

    /**
     * Indica al modelo que no se deben gestionar las marcas de tiempo.
     *
     * @var bool
     */
    public $timestamps = false;

    // Un área pertenece a un encargado
    public function encargado()
    {
        return $this->belongsTo(EncargadoArea::class, 'id_encargado_area_fk', 'id_encargado_area');
    }
}
