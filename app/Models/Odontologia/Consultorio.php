<?php

namespace App\Models\Odontologia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultorio extends Model
{
    use HasFactory;

    protected $table = 'consultorio';
    protected $primaryKey = 'id_insumo_consultorio';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'id_insumo_fk',
        'cantidad',
    ];

    /**
     * Get the insumo that owns the consultorio entry.
     */
    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'id_insumo_fk', 'id_insumo');
    }
}