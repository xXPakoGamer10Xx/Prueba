<?php

namespace App\Models\Ginecologia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CirugiaGinecologica extends Model
{
    use HasFactory;

    protected $table = 'cirugiasginecologica';
    protected $primaryKey = 'id_cirugia_ginecologica';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_cirugia_ginecologica', 'fecha_ingreso', 'fecha_egreso', 'id_doctor',
        'id_paciente', 'id_diagnostico', 'tipoCirugia', 'apeo',
    ];

    // --- RELACIONES CORREGIDAS ---
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'id_doctor', 'id_doctor');
    }
    
    // También necesita su propia relación de diagnóstico
    public function diagnostico()
    {
        return $this->belongsTo(Diagnostico::class, 'id_diagnostico', 'id_diagnostico');
    }
}