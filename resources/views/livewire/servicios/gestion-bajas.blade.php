<div>
    <main class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h2 class="h3 mb-0">Historial de Bajas de Equipo</h2>
            <button type="button" class="btn btn-danger" wire:click="openModal">
                <i class="fas fa-plus me-2"></i>Registrar Nueva Baja
            </button>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
        @endif

        <div class="d-flex mb-4">
            <input wire:model.live.debounce.300ms="search" class="form-control me-2" type="search" placeholder="Buscar por equipo, serie o motivo...">
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Equipo (Serie)</th>
                        <th>Fecha de Baja</th>
                        <th>Estado</th>
                        <th>Motivo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bajas as $baja)
                    <tr>
                        <td>{{ $baja->id_proceso_baja }}</td>
                        <td>{{ $baja->inventario->equipo->nombre ?? 'N/D' }} ({{ $baja->inventario->num_serie ?? 'N/D' }})</td>
                        <td>{{ isset($baja->fecha_baja) ? \Carbon\Carbon::parse($baja->fecha_baja)->format('d/m/Y') : 'N/A' }}</td>
                        <td><span class="badge @switch($baja->estado) @case('baja completa') bg-danger @break @case('en proceso') bg-warning text-dark @break @case('cancelada') bg-secondary @break @default bg-light text-dark @endswitch">{{ ucfirst($baja->estado) }}</span></td>
                        <td>{{ Str::limit($baja->motivo, 50) }}</td>
                        <td>
                            @php
                                $bajaData = [
                                    'id_proceso_baja' => $baja->id_proceso_baja,
                                    'fecha_baja' => isset($baja->fecha_baja) ? \Carbon\Carbon::parse($baja->fecha_baja)->format('d/m/Y') : 'No especificada',
                                    'estado' => ucfirst($baja->estado),
                                    'motivo' => $baja->motivo ?? 'No especificado.',
                                    'nombre_equipo' => $baja->inventario->equipo->nombre ?? 'N/D',
                                    'num_serie' => $baja->inventario->num_serie ?? 'N/D',
                                ];
                            @endphp
                            <button 
                                data-reporte='@json($bajaData)' 
                                onclick='generarPDFDesdeBoton(this)' 
                                class="btn btn-danger btn-sm" 
                                title="Generar Reporte PDF">
                                <i class="fas fa-file-pdf"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center">No hay registros de baja para mostrar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bajas->hasPages())
            <div class="mt-3">{{ $bajas->links() }}</div>
        @endif
    </main>

    {{-- Modal para Bajas --}}
    @if($showModal)
    <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Baja de Equipo</h5>
                    <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveBaja" id="baja-form" class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Equipo del Inventario:</label>
                            <select wire:model.live="id_inventario" class="form-select @error('id_inventario') is-invalid @enderror" required>
                                <option value="">Selecciona un equipo...</option>
                                @foreach ($equipos_inventario as $item)
                                    <option value="{{ $item->id_inventario }}">
                                        {{ $item->equipo->nombre }} (Serie: {{ $item->num_serie ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_inventario') <span class="invalid-feedback">{{ $message }}</span> @enderror

                            @if($ultimoMantenimientoInfo)
                                <div class="form-text mt-2 p-2 bg-light border rounded">{{ $ultimoMantenimientoInfo }}</div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha de Baja:</label>
                            <input type="date" wire:model="fecha_baja" class="form-control @error('fecha_baja') is-invalid @enderror" required>
                            @error('fecha_baja') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado del Proceso:</label>
                            <select wire:model="estado" class="form-select @error('estado') is-invalid @enderror" required>
                                <option value="">Selecciona un estado...</option>
                                <option value="en proceso">En proceso</option>
                                <option value="baja completa">Baja completa</option>
                                <option value="cancelada">Cancelada</option>
                            </select>
                             @error('estado') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Motivo de la Baja:</label>
                            <textarea wire:model="motivo" class="form-control @error('motivo') is-invalid @enderror" rows="3" required></textarea>
                            @error('motivo') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showModal', false)">Cancelar</button>
                    <button type="submit" form="baja-form" class="btn btn-danger">Registrar Baja</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        function generarPDFDesdeBoton(button) {
            const dataString = button.getAttribute('data-reporte');
            const data = JSON.parse(dataString);
            generarPDFBaja(data);
        }

        function generarPDFBaja(data) {
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF('p', 'mm', 'a4');
            const fechaGenerado = new Date().toLocaleDateString('es-MX', { year: 'numeric', month: 'long', day: 'numeric' });
            const pdfWidth = pdf.internal.pageSize.getWidth();
            let y = 20;

            pdf.setFont("helvetica", "bold");
            pdf.setFontSize(16);
            pdf.text('Reporte de Baja de Equipo', pdfWidth / 2, y, { align: 'center' });
            y += 8;
            
            pdf.setFont("helvetica", "normal");
            pdf.setFontSize(12);
            pdf.text('Hospital Municipal de Chiconcuac – Servicios Generales', pdfWidth / 2, y, { align: 'center' });
            y += 6;
            
            pdf.setFontSize(10);
            pdf.text(`Generado el: ${fechaGenerado}`, pdfWidth - 15, y, { align: 'right' });
            y += 8;

            pdf.setLineWidth(0.5);
            pdf.line(15, y, pdfWidth - 15, y);
            y += 10;

            pdf.setFont("helvetica", "bold");
            pdf.setFontSize(12);
            pdf.text(`Proceso de Baja ID: ${data.id_proceso_baja}`, 15, y);
            pdf.text(`Estado: ${data.estado}`, pdfWidth / 2 + 20, y);
            y += 7;
            pdf.text(`Fecha de Baja: ${data.fecha_baja}`, 15, y);
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
            pdf.text('Motivo de la Baja:', 15, y);
            y += 8;
            pdf.setFont("helvetica", "normal");
            const motivo = pdf.splitTextToSize(String(data.motivo), pdfWidth - 30);
            pdf.text(motivo, 15, y);
            
            const firmaY = pdf.internal.pageSize.getHeight() - 40;
            
            pdf.line(25, firmaY, 85, firmaY);
            pdf.setFontSize(10);
            pdf.text('Firma de Autorización', 30, firmaY + 5);

            pdf.line(pdfWidth - 85, firmaY, pdfWidth - 25, firmaY);
            pdf.text('Firma del Responsable', pdfWidth - 55, firmaY + 5, { align: 'center' });
            
            pdf.save(`Baja_Equipo_ID_${data.id_proceso_baja}.pdf`);
        }
    </script>
</div>
