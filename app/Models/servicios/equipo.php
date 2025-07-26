<?php

namespace App\Models\servicios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo para la tabla 'equipos'.
 *
 * Representa un equipo dentro del sistema de inventario de servicios.
 */
class Equipo extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada con el modelo.
     *
     * Laravel intentaría buscar 'equipos' por defecto, pero es una buena práctica
     * definirlo explícitamente para evitar cualquier ambigüedad.
     *
     * @var string
     */
    protected $table = 'equipos';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * Es una medida de seguridad para protegerte contra vulnerabilidades
     * al crear o actualizar registros. Debes listar aquí todas las columnas
     * de tu tabla 'equipos' que quieres poder guardar desde un formulario.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'marca',
        'modelo',
        'numero_serie',
        'fecha_adquisicion',
        'id_garantia', // Ejemplo, ajusta según tu migración
        'id_area',     // Ejemplo, ajusta según tu migración
    ];

    /**
     * Define la relación con el modelo Garantia.
     *
     * Un Equipo pertenece a una Garantia.
     */
    public function garantia()
    {
        // Asegúrate de que el modelo Garantia exista y esté nombrado correctamente.
        return $this->belongsTo(Garantia::class, 'id_garantia');
    }

    /**
     * Define la relación con el modelo Area.
     *
     * Un Equipo pertenece a un Area.
     */
    public function area()
    {
        // Asegúrate de que el modelo Area exista y esté nombrado correctamente.
        return $this->belongsTo(Area::class, 'id_area');
    }
}