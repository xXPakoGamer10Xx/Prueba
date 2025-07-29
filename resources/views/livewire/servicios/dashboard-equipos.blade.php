<div>
    <h2 class="mb-4">Resumen de Equipos</h2>

    <section class="mb-5 card p-4">
        <h3 class="mb-3">Estado General de Equipos</h3>
        <div class="row">
            <div class="col-md-6 d-flex justify-content-center align-items-center">
                <canvas id="equipmentPieChart" style="max-height: 300px; max-width: 300px;"></canvas>
            </div>
            <div class="col-md-6 d-flex align-items-center">
                <ul class="list-unstyled">
                    @foreach($chartData['labels'] as $index => $label)
                        <li>
                            <span style="display:inline-block; width:15px; height:15px; background-color:{{ $chartData['colors'][$index] }}; border-radius:3px; margin-right: 5px;"></span>
                            {{ $label }} ({{ $chartData['data'][$index] }})
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    <section class="card p-4">
        <h3 class="mb-3">Listado de Equipos</h3>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="col-md-6">
                <input
                    type="text"
                    wire:model.live="search" {{-- wire:model.live actualiza la propiedad search en tiempo real --}}
                    class="form-control"
                    placeholder="Buscar equipo..."
                >
            </div>
            <div>
                {{-- Muestra la paginación en la parte superior si lo deseas --}}
                {{-- La paginación de Livewire renderiza los enlaces automáticamente --}}
                {{ $equipos->links('pagination::bootstrap-5') }}
            </div>
        </div>


        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($equipos as $equipo)
                        <tr>
                            <td>{{ $equipo->id_inventario }}</td> {{-- Usamos id_inventario ya que la consulta trae ese ID --}}
                            <td>{{ $equipo->nombre }}</td>
                            <td>{{ $equipo->status }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No hay equipos que coincidan con la búsqueda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{-- La paginación de Livewire renderiza los enlaces automáticamente --}}
            {{ $equipos->links('pagination::bootstrap-5') }}
        </div>
    </section>

    {{-- Script para Chart.js - Debe estar dentro del componente para reactividad --}}
    @script
    <script>
        let chartInstance = null; // Variable para almacenar la instancia de la gráfica

        function renderChart() {
            const ctx = document.getElementById('equipmentPieChart');

            if (chartInstance) {
                chartInstance.destroy(); // Destruir instancia anterior si existe
            }

            // Obtener los datos de la gráfica directamente de la propiedad Livewire
            const chartData = @json($chartData);

            chartInstance = new Chart(ctx, {
                type: 'pie', // Tipo de gráfica a pastel
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Cantidad',
                        data: chartData.data,
                        backgroundColor: chartData.colors,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // Permite controlar mejor el tamaño del canvas
                    plugins: {
                        title: {
                            display: false, // Ya tenemos un h3 para el título
                            text: 'Resumen de Estados de Equipos'
                        },
                        legend: {
                            display: false, // Ocultar leyenda de Chart.js para usar la nuestra de HTML
                        }
                    }
                }
            });
        }

        // Llamar a renderChart cuando el componente se inicializa
        renderChart();

        // Escuchar eventos de Livewire para re-renderizar la gráfica si los datos cambian
        Livewire.on('chartDataUpdated', () => {
            renderChart();
        });
    </script>
    @endscript
</div>
