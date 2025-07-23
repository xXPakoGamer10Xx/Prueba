<?php

namespace App\Models\Odontologia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    use HasFactory;

    protected $table = 'insumos';
    protected $primaryKey = 'id_insumo';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'clave',
        'descripcion',
        'id_laboratorio',
        'id_presentacion',
        'contenido',
        'caducidad',
    ];

    protected $casts = [
        'caducidad' => 'date', // Castea la columna caducidad a un objeto fecha
    ];

    /**
     * Get the laboratorio that owns the insumo.
     */
    public function laboratorio()
    {
        return $this->belongsTo(Laboratorio::class, 'id_laboratorio', 'id_laboratorio');
    }

    /**
     * Get the presentacion that owns the insumo.
     */
    public function presentacion()
    {
        return $this->belongsTo(Presentacion::class, 'id_presentacion', 'id_presentacion');
    }

    /**
     * Get the consultorio entries for the insumo.
     */
    public function consultorios()
    {
        return $this->hasMany(Consultorio::class, 'id_insumo_fk', 'id_insumo');
    }
}