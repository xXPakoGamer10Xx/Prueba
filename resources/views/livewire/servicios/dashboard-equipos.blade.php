<div>

    <h2 class="mb-4">Resumen de Equipos</h2>


    <section class="mb-5 card p-4">

        <h3 class="mb-3">Estado General de Equipos</h3>

        <div class="row">

            <div class="col-md-6 d-flex justify-content-center align-items-center" style="min-height: 400px;">

                <canvas id="equipmentPieChart" style="height: 400px; width: 400px;"></canvas>

            </div>

            <div class="col-md-6 d-flex align-items-center">

                <ul class="list-unstyled fs-5">

                    @foreach($chartData['labels'] as $index => $label)

                        <li class="mb-2">

                            <span style="display:inline-block; width:18px; height:18px; background-color:{{ $chartData['colors'][$index] }}; border-radius:3px; margin-right: 8px;"></span>

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

                    wire:model.live="search"

                    class="form-control"

                    placeholder="Buscar equipo..."

                >

            </div>

            <div>

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

                            <td>{{ $equipo->id_inventario }}</td>

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

            {{ $equipos->links('pagination::bootstrap-5') }}

        </div>

    </section>


    @script

    <script>

        let chartInstance = null;


        function renderChart() {

            const ctx = document.getElementById('equipmentPieChart');


            if (chartInstance) {

                chartInstance.destroy();

            }


            const chartData = @json($chartData);


            chartInstance = new Chart(ctx, {

                type: 'pie',

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

                    maintainAspectRatio: false,

                    plugins: {

                        title: {

                            display: false

                        },

                        legend: {

                            display: false

                        }

                    }

                }

            });

        }


        renderChart();


        Livewire.on('chartDataUpdated', () => {

            renderChart();

        });

    </script>

    @endscript

</div>
