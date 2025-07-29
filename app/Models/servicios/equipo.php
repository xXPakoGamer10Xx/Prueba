<?php

namespace App\Models\Servicios; // Corregido para estar en la carpeta principal de Modelos

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Corregido a "Equipo" con mayúscula inicial
class Equipo extends Model
{
    use HasFactory;
    protected $table = 'equipos';
    protected $primaryKey = 'id_equipo';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'marca',
        'modelo',
        'cantidad', // Añadido para consistencia
        'frecuencia_mantenimiento',
    ];

    // --- RELACIÓN AÑADIDA ---
    // Esto le dice a un Equipo que puede tener muchos Inventarios.
    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'id_equipo_fk', 'id_equipo');
    }
}