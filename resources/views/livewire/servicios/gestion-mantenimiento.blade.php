<div>
    <main class="container my-5">

        <h2 class="h3 mb-3">Panel de Control</h2>
        <div class="row g-4 mb-5">
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                        <div class="fs-1 text-primary"><i class="fas fa-calendar-alt"></i></div>
                        <h5 class="card-title mt-2">Reportes este Mes</h5>
                        <p class="card-text fs-2 fw-bold">{{ $stats['totalMesActual'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title text-center mb-3"><i class="fas fa-tools me-2 text-warning"></i>Top 5 Equipos con Más Mantenimiento</h5>
                        @if($stats['equiposTopMantenimiento']->isNotEmpty())
                            <ul class="list-group list-group-flush">
                                @foreach($stats['equiposTopMantenimiento'] as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ $loop->iteration }}. {{ $item->equipo->nombre }}</span>
                                    <span class="badge bg-secondary rounded-pill">{{ $item->mantenimientos_count }}</span>
                                </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="card-text text-center fst-italic mt-4">No hay datos de equipos con mantenimientos.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                        <div class="fs-1 text-info"><i class="fas fa-chart-pie"></i></div>
                        <h5 class="card-title mt-2">Mantenimientos (Último Año)</h5>
                        <div>
                            <span class="badge bg-info text-dark fs-6 me-2">Preventivos: {{ $stats['ratioPreventivo'] }}</span>
                            <span class="badge bg-warning text-dark fs-6">Correctivos: {{ $stats['ratioCorrectivo'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h2 class="h3 mb-0">Historial de Mantenimientos</h2>
            <button type="button" class="btn btn-primary" wire:click="crearMantenimiento">
                <i class="fas fa-plus me-2"></i>Añadir Nuevo Reporte
            </button>
        </div>

        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-filter me-2"></i>Filtros y Acciones</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <input wire:model.live.debounce.300ms="search" class="form-control" type="search" placeholder="Buscar por palabra clave...">
                    </div>
                    <div class="col-md-2">
                        <select wire:model.live="filtroTipo" class="form-select">
                            <option value="">Todo Tipo</option>
                            <option value="preventivo">Preventivo</option>
                            <option value="correctivo">Correctivo</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input wire:model.live="filtroFechaInicio" type="date" class="form-control" title="Fecha de inicio">
                    </div>
                    <div class="col-md-2">
                        <input wire:model.live="filtroFechaFin" type="date" class="form-control" title="Fecha de fin">
                    </div>
                    <div class="col-md-2 d-grid gap-2">
                        <div class="btn-group">
                            <button class="btn btn-outline-secondary" wire:click="resetearFiltros" title="Limpiar filtros"><i class="fas fa-times"></i></button>
                            <button class="btn btn-success" wire:click="exportarCSV" title="Exportar a CSV">
                                <i wire:loading.remove wire:target="exportarCSV" class="fas fa-file-csv"></i>
                                <span wire:loading wire:target="exportarCSV" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Equipo (Serie)</th>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Encargado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reportes as $reporte)
                    <tr>
                        <td>{{ $reporte->id_mantenimiento }}</td>
                        <td>
                            <a href="#" wire:click.prevent="verHistorial({{ $reporte->inventario->id_inventario }})" title="Ver historial del equipo">
                                {{ $reporte->inventario->equipo->nombre ?? 'N/D' }}
                            </a>
                            <br>
                            <small class="text-muted">({{ $reporte->inventario->num_serie ?? 'N/D' }})</small>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($reporte->fecha)->format('d/m/Y') }}</td>
                        <td><span class="badge {{ $reporte->tipo == 'correctivo' ? 'bg-warning text-dark' : 'bg-info text-dark' }}">{{ ucfirst($reporte->tipo) }}</span></td>
                        <td>{{ $reporte->encargadoMantenimiento->nombre ?? '' }} {{ $reporte->encargadoMantenimiento->apellidos ?? '' }}</td>
                        <td class="text-center">
                            @php
                                $reporteData = [
                                    'id_mantenimiento' => $reporte->id_mantenimiento,
                                    'fecha' => \Carbon\Carbon::parse($reporte->fecha)->format('d/m/Y'),
                                    'tipo' => ucfirst($reporte->tipo),
                                    'observaciones' => $reporte->observaciones ?? 'Sin observaciones.',
                                    'refacciones_material' => $reporte->refacciones_material ?? 'No se utilizaron refacciones o materiales.',
                                    'nombre_equipo' => $reporte->inventario->equipo->nombre ?? 'N/D',
                                    'num_serie' => $reporte->inventario->num_serie ?? 'N/D',
                                    'encargado_nombre_completo' => ($reporte->encargadoMantenimiento->nombre ?? '') . ' ' . ($reporte->encargadoMantenimiento->apellidos ?? ''),
                                ];
                            @endphp
                            <button
                                data-reporte='@json($reporteData)'
                                onclick='generarPDFDesdeBoton(this)'
                                class="btn btn-danger btn-sm"
                                title="Generar Reporte PDF">
                                <i class="fas fa-file-pdf"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class='text-center fst-italic'>No hay reportes que coincidan con los filtros actuales.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reportes->hasPages())
        <div class="mt-3">{{ $reportes->links() }}</div>
        @endif
    </main>

    @if($showHistorialModal && $equipoSeleccionadoHistorial)
    <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-history me-2"></i> Historial para: <strong>{{ $equipoSeleccionadoHistorial['nombre'] }}</strong>
                    </h5>
                    <button type="button" class="btn-close" wire:click="cerrarHistorialModal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-4"><strong>Número de Serie:</strong> {{ $equipoSeleccionadoHistorial['num_serie'] }}</p>
                    
                    @if($equipoSeleccionadoHistorial['mantenimientos']->isEmpty())
                        <div class="alert alert-info text-center">Este equipo aún no tiene reportes de mantenimiento.</div>
                    @else
                        <div class="list-group">
                            @foreach($equipoSeleccionadoHistorial['mantenimientos'] as $mantenimiento)
                                <div class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            <span class="badge {{ $mantenimiento->tipo == 'correctivo' ? 'bg-warning text-dark' : 'bg-info text-dark' }}">{{ ucfirst($mantenimiento->tipo) }}</span>
                                        </h6>
                                        <small>{{ \Carbon\Carbon::parse($mantenimiento->fecha)->format('d/m/Y') }}</small>
                                    </div>
                                    <p class="mb-1"><strong>Observaciones:</strong> {{ $mantenimiento->observaciones ?: 'Sin observaciones.' }}</p>
                                    <small class="text-muted">Encargado: {{ $mantenimiento->encargadoMantenimiento->nombre ?? '' }} {{ $mantenimiento->encargadoMantenimiento->apellidos ?? 'N/A' }}</small>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="cerrarHistorialModal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif

    {{-- EL RESTO DE LA VISTA (MODALES Y SCRIPT) SE MANTIENE IGUAL --}}

    @if($showModal)
    <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Añadir Nuevo Reporte</h5>
                    <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="id_inventario_fk" class="form-label">Equipo de inventario:</label>
                            <select wire:model="id_inventario_fk" id="id_inventario_fk" class="form-select @error('id_inventario_fk') is-invalid @enderror" required>
                                <option value="">Seleccionar equipo...</option>
                                @foreach ($equipos_inventario as $item)
                                    <option value="{{ $item->id_inventario }}">{{ $item->equipo->nombre }} ({{ $item->num_serie }})</option>
                                @endforeach
                            </select>
                            @error('id_inventario_fk') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="id_encargado_man_fk" class="form-label">Encargado:</label>
                            <div class="input-group">
                                <select wire:model="id_encargado_man_fk" id="id_encargado_man_fk" class="form-select @error('id_encargado_man_fk') is-invalid @enderror" required>
                                    <option value="">Seleccionar encargado...</option>
                                    @foreach ($encargados as $encargado)
                                        <option value="{{ $encargado->id_encargado_man }}">{{ $encargado->nombre }} {{ $encargado->apellidos }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-outline-secondary" type="button" wire:click="crearEncargado"><i class="fas fa-user-plus"></i></button>
                            </div>
                            @error('id_encargado_man_fk') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Fecha:</label>
                            <input type="date" wire:model="fecha" class="form-control @error('fecha') is-invalid @enderror" required>
                            @error('fecha') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tipo:</label>
                            <select wire:model="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                                <option value="preventivo">Preventivo</option>
                                <option value="correctivo">Correctivo</option>
                            </select>
                            @error('tipo') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Refacciones y Material:</label>
                            <textarea wire:model="refacciones_material" class="form-control @error('refacciones_material') is-invalid @enderror" rows="3"></textarea>
                            @error('refacciones_material') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Observaciones:</label>
                            <textarea wire:model="observaciones" class="form-control @error('observaciones') is-invalid @enderror" rows="3"></textarea>
                            @error('observaciones') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showModal', false)">Cancelar</button>
                    <button type="button" class="btn btn-primary" wire:click="saveMantenimiento">Guardar Reporte</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif

    @if($showEncargadoModal)
    <div class="modal fade show" style="display: block; z-index: 1060;" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Nuevo Encargado</h5>
                    <button type="button" class="btn-close" wire:click="$set('showEncargadoModal', false)"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveEncargado">
                        <div class="mb-3">
                            <label class="form-label">Nombre(s):</label>
                            <input type="text" wire:model="nombre_encargado" class="form-control @error('nombre_encargado') is-invalid @enderror">
                            @error('nombre_encargado') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Apellidos:</label>
                            <input type="text" wire:model="apellidos_encargado" class="form-control @error('apellidos_encargado') is-invalid @enderror">
                            @error('apellidos_encargado') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cargo:</label>
                            <input type="text" wire:model="cargo_encargado" class="form-control @error('cargo_encargado') is-invalid @enderror">
                            @error('cargo_encargado') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contacto (Teléfono/Email):</label>
                            <input type="text" wire:model="contacto_encargado" class="form-control @error('contacto_encargado') is-invalid @enderror">
                            @error('contacto_encargado') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-secondary" wire:click="$set('showEncargadoModal', false)">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar Encargado</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 1055;"></div>
    @endif

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        function generarPDFDesdeBoton(button) {
            const dataString = button.getAttribute('data-reporte');
            const data = JSON.parse(dataString);
            generarPDFMantenimiento(data);
        }

        function generarPDFMantenimiento(data) {
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF('p', 'mm', 'a4');
            const fechaGenerado = new Date().toLocaleDateString('es-MX', { year: 'numeric', month: 'long', day: 'numeric' });
            const pdfWidth = pdf.internal.pageSize.getWidth();
            let y = 20;

            pdf.setFont("helvetica", "bold");
            pdf.setFontSize(16);
            pdf.text('Reporte de Mantenimiento de Equipo', pdfWidth / 2, y, { align: 'center' });
            y += 8;

            pdf.setFont("helvetica", "normal");
            pdf.setFontSize(12);
            pdf.text('HOSPITAL MUNICIPAL DE CHICONCUAC – Servicios Generales', pdfWidth / 2, y, { align: 'center' });
            y += 6;

            pdf.setFontSize(10);
            pdf.text(`Generado el: ${fechaGenerado}`, pdfWidth - 15, y, { align: 'right' });
            y += 8;

            pdf.setLineWidth(0.5);
            pdf.line(15, y, pdfWidth - 15, y);
            y += 10;

            pdf.setFont("helvetica", "bold");
            pdf.setFontSize(12);
            pdf.text(`Reporte ID: ${data.id_mantenimiento}`, 15, y);
            pdf.text(`Tipo: ${data.tipo}`, pdfWidth / 2 + 20, y);
            y += 7;
            pdf.text(`Fecha del Servicio: ${data.fecha}`, 15, y);
            y += 10;

            pdf.setLineWidth(0.2);
            pdf.line(15, y - 4, pdfWidth - 15, y - 4);

            pdf.setFontSize(12);
            pdf.text('Información del Equipo', 15, y);
            y += 8;

            pdf.setFont("helvetica", "bold");
            pdf.text('Equipo:', 20, y);
            pdf.setFont("helvetica", "normal");
            pdf.text(String(data.nombre_equipo || 'No disponible'), 55, y);
            y += 7;

            pdf.setFont("helvetica", "bold");
            pdf.text('Número de Serie:', 20, y);
            pdf.setFont("helvetica", "normal");
            pdf.text(String(data.num_serie || 'No disponible'), 55, y);
            y += 10;

            pdf.line(15, y - 4, pdfWidth - 15, y - 4);

            pdf.setFont("helvetica", "bold");
            pdf.setFontSize(12);
            pdf.text('Detalles del Servicio', 15, y);
            y += 8;

            pdf.setFontSize(11);
            pdf.text('Refacciones y Material Utilizado:', 15, y);
            y += 6;
            pdf.setFont("helvetica", "normal");
            const refacciones = pdf.splitTextToSize(String(data.refacciones_material), pdfWidth - 30);
            pdf.text(refacciones, 15, y);
            y += (refacciones.length * 5) + 5;

            pdf.setFont("helvetica", "bold");
            pdf.text('Observaciones:', 15, y);
            y += 6;
            pdf.setFont("helvetica", "normal");
            const observaciones = pdf.splitTextToSize(String(data.observaciones), pdfWidth - 30);
            pdf.text(observaciones, 15, y);

            const firmaY = pdf.internal.pageSize.getHeight() - 40;

            pdf.line(25, firmaY, 85, firmaY);
            pdf.setFontSize(10);
            pdf.text('Firma de Autorización', 30, firmaY + 5);

            pdf.line(pdfWidth - 85, firmaY, pdfWidth - 25, firmaY);
            pdf.text(String(data.encargado_nombre_completo.trim() || 'Firma del Responsable'), pdfWidth - 55, firmaY + 5, { align: 'center' });
            pdf.setFontSize(8);
            pdf.text('Encargado del Mantenimiento', pdfWidth - 55, firmaY + 9, { align: 'center' });
            
            pdf.save(`Mantenimiento_Reporte_ID_${data.id_mantenimiento}.pdf`);
        }
    </script>
</div>