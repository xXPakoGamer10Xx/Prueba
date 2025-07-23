<?php

namespace App\Models\Odontologia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presentacion extends Model
{
    use HasFactory;

    protected $table = 'presentaciones';
    protected $primaryKey = 'id_presentacion';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
    ];

    /**
     * Get the insumos for the presentacion.
     */
    public function insumos()
    {
        return $this->hasMany(Insumo::class, 'id_presentacion');
    }
}