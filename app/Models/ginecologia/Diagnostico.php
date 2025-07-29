<?php

namespace App\Models\Ginecologia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnostico extends Model
{
    use HasFactory;
    protected $table = 'diagnosticos'; // Asegúrate que este sea el nombre de tu tabla
    protected $primaryKey = 'id_diagnostico';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
}