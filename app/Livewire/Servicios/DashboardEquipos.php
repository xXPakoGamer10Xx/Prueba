<?php

namespace App\Livewire\Servicios;

use Livewire\Component;
use Livewire\WithPagination; // Importar el trait de paginación para manejar la paginación de los resultados

// Importar los modelos necesarios para interactuar con la base de datos
use App\Models\Servicios\Inventario;
use App\Models\Servicios\Equipo; // Aunque se accede a través de Inventario, es bueno tenerlo importado si se usara directamente
use App\Models\Servicios\ProcesoBaja;

class DashboardEquipos extends Component
{
    use WithPagination; // Habilitar la funcionalidad de paginación de Livewire

    // Propiedad pública para almacenar el término de búsqueda.
    // Livewire la vinculará automáticamente con un input en la vista (ej. wire:model="search").
    public $search = '';

    // Propiedad pública para definir el número de elementos por página en la paginación.
    public $perPage = 10;

    /**
     * Método que se ejecuta automáticamente cuando la propiedad $search cambia.
     * Es un "hook" de Livewire.
     * Resetea la paginación a la primera página cada vez que se modifica el término de búsqueda.
     */
    public function updatedSearch()
    {
        $this->resetPage(); // Vuelve a la primera página de resultados.
    }

    /**
     * Método para obtener los datos de los equipos del inventario.
     * Realiza una consulta a la base de datos, aplica filtros de búsqueda y pagina los resultados.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getEquipos()
    {
        // Iniciar una consulta sobre el modelo Inventario.
        $query = Inventario::query()
            // Seleccionar columnas específicas. Es importante calificar las columnas con el nombre de la tabla
            // cuando se usan joins para evitar ambigüedades (ej. 'inventarios.id_inventario').
            ->select('inventarios.id_inventario', 'equipos.nombre', 'inventarios.status')
            // Unir la tabla 'equipos' para poder acceder al nombre del equipo.
            // La condición del join es que 'id_equipo_fk' de inventarios sea igual a 'id_equipo' de equipos.
            ->join('equipos', 'inventarios.id_equipo_fk', '=', 'equipos.id_equipo');

        // Aplicar filtro de búsqueda si la propiedad $search no está vacía.
        if ($this->search) {
            $query->where(function ($q) {
                // Buscar coincidencias en el nombre del equipo (de la tabla 'equipos')
                // o en el estado del inventario (de la tabla 'inventarios').
                $q->where('equipos.nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('inventarios.status', 'like', '%' . $this->search . '%');
            });
        }

        // Retornar los resultados paginados, usando la propiedad $perPage.
        return $query->paginate($this->perPage);
    }

    /**
     * Método para obtener los datos necesarios para la gráfica de pastel (pie chart).
     * Calcula el conteo de inventarios por estado y de procesos de baja.
     *
     * @return array Un array asociativo con etiquetas, datos y colores para la gráfica.
     */
    public function getChartData()
    {
        // Contar el número de inventarios para cada estado posible.
        $funcionando = Inventario::where('status', 'funcionando')->count();
        $parcialmente_funcional = Inventario::where('status', 'parcialmente funcional')->count();
        $sin_funcionar = Inventario::where('status', 'sin funcionar')->count();
        $proceso_baja = Inventario::where('status', 'proceso de baja')->count();

        // Contar el número de procesos de baja que han sido completados.
        $baja_completa = ProcesoBaja::where('estado', 'baja completa')->count();

        // Retornar los datos en un formato adecuado para ser consumidos por una librería de gráficos (ej. Chart.js).
        return [
            'labels' => [
                'Funcionando',
                'Parcialmente Funcional',
                'Sin Funcionar',
                'Proceso de Baja',
                'Dado de Baja' // Etiqueta para los equipos con baja completa
            ],
            'data' => [
                $funcionando,
                $parcialmente_funcional,
                $sin_funcionar,
                $proceso_baja,
                $baja_completa
            ],
            'colors' => [ // Colores hexadecimales para cada segmento de la gráfica.
                '#28a745', // Verde para Funcionando
                '#ffc107', // Amarillo para Parcialmente Funcional
                '#17a2b8', // Azul-Cian para Sin Funcionar
                '#6c757d', // Gris para Proceso de Baja
                '#dc3545'  // Rojo para Dado de Baja
            ]
        ];
    }

    /**
     * El método render() es el corazón de un componente Livewire.
     * Se encarga de renderizar la vista asociada al componente y pasarle los datos necesarios.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function render()
    {
        return view('livewire.servicios.dashboard-equipos', [
            // Pasar los equipos paginados a la vista.
            'equipos' => $this->getEquipos(),
            // Pasar los datos de la gráfica a la vista.
            'chartData' => $this->getChartData(),
        ]);
    }
}
