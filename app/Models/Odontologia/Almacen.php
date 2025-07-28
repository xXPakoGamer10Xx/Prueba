<?php

// app/Models/Odontologia/Almacen.php
namespace App\Models\Odontologia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    use HasFactory;

    protected $table = 'almacen'; // Asegúrate de que el nombre de la tabla sea correcto
    protected $primaryKey = 'id_insumo_almacen'; // Asegúrate de que la clave primaria sea correcta
    protected $fillable = ['id_insumo_fk', 'cantidad']; // Campos que se pueden asignar masivamente

    public $timestamps = false;

    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'id_insumo_fk', 'id_insumo');
    }
}