<?php

namespace App\Models\Odontologia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laboratorio extends Model
{
    use HasFactory;

    protected $table = 'laboratorios';
    protected $primaryKey = 'id_laboratorio';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
    ];

    /**
     * Get the insumos for the laboratorio.
     */
    public function insumos()
    {
        return $this->hasMany(Insumo::class, 'id_laboratorio');
    }
}