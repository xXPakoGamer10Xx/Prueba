<?php

namespace App\Models\Servicios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Garantia extends Model
{
    use HasFactory;
    protected $table = 'garantias';
    protected $primaryKey = 'id_garantia';
    public $timestamps = false;
    protected $fillable = ['status', 'empresa', 'contacto', 'fecha_garantia'];
}
