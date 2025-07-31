<?php

namespace App\Models\Ginecologia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CirugiaGeneral extends Model
{
    use HasFactory;

    protected $table = 'cirugiasgeneral';
    protected $primaryKey = 'id_cirugia_general';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_cirugia_general', 'fecha_ingreso', 'fecha_egreso', 'pieza_patologica',
        'region_anatomica', 'tipoCirugia', 'id_doctor', 'id_paciente', 'id_diagnostico',
    ];

    // --- RELACIONES CORREGIDAS ---
    // Se agrega el tercer parámetro para indicar la clave primaria de la tabla relacionada.
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'id_doctor', 'id_doctor');
    }
    
    public function diagnostico()
    {
        return $this->belongsTo(Diagnostico::class, 'id_diagnostico', 'id_diagnostico');
    }
}