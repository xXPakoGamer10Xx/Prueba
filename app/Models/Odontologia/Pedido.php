<?php

namespace App\Models\Odontologia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedidos';
    protected $primaryKey = 'id_pedido';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'id_insumo_almacen_fk',
        'cantidad_solicitada',
        'cantidad_autorizada',
        'estado_pedido',
        'fecha_pedido',
        'fecha_entrega',
    ];

    protected $casts = [
        'fecha_pedido' => 'date',
        'fecha_entrega' => 'date',
    ];

    public function almacenItem()
    {
        return $this->belongsTo(Almacen::class, 'id_insumo_almacen_fk', 'id_insumo_almacen');
    }
}
