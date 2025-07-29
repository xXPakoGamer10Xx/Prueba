<?php

namespace App\Livewire\Servicios;

use Livewire\Component;
use Livewire\WithPagination; // Importar el trait de paginación
use App\Models\Servicios\Inventario; // Importar el modelo Inventario
use App\Models\Servicios\Equipo; // Importar el modelo Equipo (ya está relacionado en Inventario, pero por claridad)
use App\Models\Servicios\ProcesoBaja; // Importar el modelo ProcesoBaja

class DashboardEquipos extends Component
{
    use WithPagination; // Usar el trait para paginación

    public $search = ''; // Propiedad para el término de búsqueda
    public $perPage = 10; // Propiedad para el número de elementos por página

    // Método que se ejecuta cuando la propiedad $search cambia (Livewire reactive)
    public function updatedSearch()
    {
        $this->resetPage(); // Resetear la paginación cuando se realiza una búsqueda
    }

    // Método para obtener los datos de la tabla de equipos
    public function getEquipos()
    {
        // Obtener equipos del inventario, con sus nombres desde la tabla 'equipos'
        $query = Inventario::query()
            ->select('inventarios.id_inventario', 'equipos.nombre', 'inventarios.status')
            ->join('equipos', 'inventarios.id_equipo_fk', '=', 'equipos.id_equipo');

        // Aplicar filtro de búsqueda si $search no está vacío
        if ($this->search) {
            $query->where(function($q) {
                $q->where('equipos.nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('inventarios.status', 'like', '%' . $this->search . '%');
            });
        }

        // Retornar los resultados paginados
        return $query->paginate($this->perPage);
    }

    // Método para obtener los datos de la gráfica de pastel
    public function getChartData()
    {
        // Conteo de inventarios por estado
        $funcionando = Inventario::where('status', 'funcionando')->count();
        $parcialmente_funcional = Inventario::where('status', 'parcialmente funcional')->count();
        $sin_funcionar = Inventario::where('status', 'sin funcionar')->count();
        $proceso_baja = Inventario::where('status', 'proceso de baja')->count();

        // Conteo de procesos de baja con estado 'baja completa'
        $baja_completa = ProcesoBaja::where('estado', 'baja completa')->count();

        // Retornar los datos en un formato adecuado para la gráfica
        return [
            'labels' => [
                'Funcionando',
                'Parcialmente Funcional',
                'Sin Funcionar',
                'Proceso de Baja',
                'Dado de Baja'
            ],
            'data' => [
                $funcionando,
                $parcialmente_funcional,
                $sin_funcionar,
                $proceso_baja,
                $baja_completa
            ],
            'colors' => [ // Colores que coincidan con los de tu imagen o preferencia
                '#28a745', // Verde para Funcionando
                '#ffc107', // Amarillo para Parcialmente Funcional
                '#17a2b8', // Azul (o similar a tu diseño original) para Sin Funcionar
                '#6c757d', // Gris para Proceso de Baja
                '#dc3545'  // Rojo para Dado de Baja
            ]
        ];
    }

    public function render()
    {
        return view('livewire.servicios.dashboard-equipos', [
            'equipos' => $this->getEquipos(),
            'chartData' => $this->getChartData(),
        ]);
    }
}
