<?php

namespace App\Models\servicios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EncargadoArea extends Model
{
    use HasFactory;

    protected $table = 'encargados_area';
    protected $primaryKey = 'id_encargado_area';
    protected $fillable = ['nombre', 'apellidos', 'cargo'];

    /**
     * Indica al modelo que no se deben gestionar las marcas de tiempo.
     *
     * @var bool
     */
    public $timestamps = false;

    // Un encargado puede tener muchas áreas
    public function areas()
    {
        return $this->hasMany(Area::class, 'id_encargado_area_fk', 'id_encargado_area');
    }
}
