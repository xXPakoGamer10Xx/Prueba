<div>
    {{-- Mensajes de sesión --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Inventario de Equipos</h2>
        <button wire:click="create()" class="btn btn-primary flex-shrink-0"><i class="fas fa-plus me-2"></i>Agregar Equipo</button>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-filter me-2"></i>Filtros y Acciones</h5>
            <div class="row g-3">
                {{-- Búsqueda General --}}
                <div class="col-md-4">
                    <input wire:model.live.debounce.300ms="search" class="form-control" type="search" placeholder="Buscar por equipo, marca, serie...">
                </div>
                {{-- Filtro por Estado --}}
                <div class="col-md-2">
                    <select wire:model.live="filtroStatus" class="form-select">
                        <option value="">-- Todo Estado --</option>
                        <option value="funcionando">Funcionando</option>
                        <option value="sin funcionar">Sin funcionar</option>
                        <option value="parcialmente funcional">Parcialmente funcional</option>
                        <option value="proceso de baja">Proceso de baja</option>
                        <option value="baja">Baja</option>
                    </select>
                </div>
                {{-- Filtro por Área --}}
                <div class="col-md-2">
                    <select wire:model.live="filtroArea" class="form-select">
                        <option value="">-- Toda Área --</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id_area }}">{{ $area->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Filtro por Garantía --}}
                <div class="col-md-2">
                    <select wire:model.live="filtroGarantia" class="form-select">
                        <option value="">-- Toda Garantía --</option>
                        <option value="con">Con Garantía</option>
                        <option value="sin">Sin Garantía</option>
                    </select>
                </div>
                {{-- Botones de Acción --}}
                <div class="col-md-2 d-grid">
                    <div class="btn-group">
                        <button class="btn btn-outline-secondary" wire:click="resetearFiltros" title="Limpiar filtros">
                            <i class="fas fa-times"></i>
                        </button>
                        <button class="btn btn-success" wire:click="exportarCSV" title="Exportar a CSV">
                            <i wire:loading.remove wire:target="exportarCSV" class="fas fa-file-csv"></i>
                            <span wire:loading wire:target="exportarCSV" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <section>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Equipo</th>
                        <th>Núm. Serie</th>
                        <th>Serie SICOPA</th>
                        <th>Serie SIA</th>
                        <th>Pertenencia</th>
                        <th>Estado</th>
                        <th>Área</th>
                        <th>Garantía</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inventario as $item)
                    <tr>
                        <td>{{ $item->id_inventario }}</td>
                        <td>
                            <strong>{{ $item->equipo->nombre ?? 'N/A' }}</strong><br>
                            <small>{{ $item->equipo->marca ?? '' }} {{ $item->equipo->modelo ?? '' }}</small>
                        </td>
                        <td>{{ $item->num_serie ?? 'N/A' }}</td>
                        <td>{{ $item->num_serie_sicopa ?? 'N/A' }}</td>
                        <td>{{ $item->num_serie_sia ?? 'N/A' }}</td>
                        <td>{{ ucfirst($item->pertenencia) }}</td>
                        <td>
                             <select class="form-select form-select-sm @if($item->status == 'baja') bg-danger text-white @elseif($item->status == 'sin funcionar' || $item->status == 'proceso de baja') bg-warning-subtle @endif"
                                wire:change="actualizarStatus({{ $item->id_inventario }}, $event.target.value)"
                                @if($item->status == 'baja') disabled @endif
                            >
                                <option value="funcionando" @if($item->status == 'funcionando') selected @endif>Funcionando</option>
                                <option value="sin funcionar" @if($item->status == 'sin funcionar') selected @endif>Sin funcionar</option>
                                <option value="parcialmente funcional" @if($item->status == 'parcialmente funcional') selected @endif>Parcialmente funcional</option>
                                <option value="proceso de baja" @if($item->status == 'proceso de baja') selected @endif>Proceso de baja</option>
                                <option value="baja" @if($item->status == 'baja') selected @endif>Baja</option>
                            </select>
                        </td>
                        <td>{{ $item->area->nombre ?? 'N/A' }}</td>
                        <td>
                            @if($item->garantia)
                                <span class="badge {{ $item->garantia->status == 'activa' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($item->garantia->status) }}
                                </span>
                            @else
                                <span class="badge bg-light text-dark">N/A</span>
                            @endif
                        </td>
                        <td class='text-center'>
                             <div class="btn-group">
                                <button wire:click="edit({{ $item->id_inventario }})" class="btn btn-warning btn-sm" title="Editar Equipo"><i class="fas fa-edit"></i></button>
                                <button wire:click="registrarMantenimiento({{ $item->id_inventario }})" class="btn btn-info btn-sm" title="Registrar Mantenimiento"><i class="fas fa-wrench"></i></button>
                                <button wire:click="registrarBaja({{ $item->id_inventario }})" class="btn btn-danger btn-sm" title="Registrar Baja"><i class="fas fa-arrow-down"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center fst-italic">No se encontraron registros que coincidan con los filtros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $inventario->links() }}
    </section>

    {{-- El modal se mantiene igual, no es necesario cambiarlo --}}
    @if ($isModalOpen)
    <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEditMode ? 'Editar Equipo del Inventario' : 'Agregar Nuevo Equipo al Inventario' }}</h5>
                    <button wire:click="closeModal()" type="button" class="btn-close"></button>
                </div>
                <form wire:submit.prevent="store" id="inventory-form">
                    <div class="modal-body">
                        <h6>Datos del Equipo</h6>
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label">Nombre del Equipo</label><input type="text" class="form-control" wire:model="nombre">@error('nombre')<span class="text-danger">{{$message}}</span>@enderror</div>
                            <div class="col-md-6"><label class="form-label">Marca</label><input type="text" class="form-control" wire:model="marca">@error('marca')<span class="text-danger">{{$message}}</span>@enderror</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12"><label class="form-label">Modelo</label><input type="text" class="form-control" wire:model="modelo">@error('modelo')<span class="text-danger">{{$message}}</span>@enderror</div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Frecuencia de Mantenimiento (meses)</label>
                                <select class="form-select" wire:model="frecuencia_mantenimiento">
                                    <option value="1">Mensual (1)</option>
                                    <option value="3">Trimestral (3)</option>
                                    <option value="6">Semestral (6)</option>
                                    <option value="12">Anual (12)</option>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <h6>Datos de Inventario</h6>
                        <div class="row mb-3">
                            <div class="col-md-4"><label class="form-label">Número de Serie</label><input type="text" class="form-control" wire:model="num_serie">@error('num_serie')<span class="text-danger">{{$message}}</span>@enderror</div>
                            <div class="col-md-4"><label class="form-label">Serie SICOPA</label><input type="text" class="form-control" wire:model="num_serie_sicopa">@error('num_serie_sicopa')<span class="text-danger">{{$message}}</span>@enderror</div>
                            <div class="col-md-4"><label class="form-label">Serie SIA</label><input type="text" class="form-control" wire:model="num_serie_sia">@error('num_serie_sia')<span class="text-danger">{{$message}}</span>@enderror</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><label class="form-label">Pertenencia</label><select class="form-select" wire:model="pertenencia"><option value="propia">Propia</option><option value="comodato">Comodato</option></select></div>
                            <div class="col-md-4"><label class="form-label">Estado</label><select class="form-select" wire:model="status"><option value="funcionando">Funcionando</option><option value="sin funcionar">Sin funcionar</option><option value="parcialmente funcional">Parcialmente funcional</option><option value="proceso de baja">Proceso de baja</option></select></div>
                            <div class="col-md-4"><label class="form-label">Área de Ubicación</label><select class="form-select" wire:model="id_area_fk"><option value="">Seleccione...</option>@foreach($areas as $area)<option value="{{$area->id_area}}">{{$area->nombre}}</option>@endforeach</select>@error('id_area_fk')<span class="text-danger">{{$message}}</span>@enderror</div>
                        </div>
                        <hr>
                        <h6>Datos de Garantía</h6>
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label">Garantía</label><select class="form-select" wire:model.live="id_garantia_fk"><option value="">Sin garantía</option><option value="new_warranty">--- Crear Nueva Garantía ---</option>@foreach($garantias as $garantia)<option value="{{$garantia->id_garantia}}">{{$garantia->empresa}} - {{$garantia->status}}</option>@endforeach</select></div>
                        </div>
                        @if($showNewWarrantyFields)
                        <div class="p-3 border rounded bg-light">
                            <h5>Detalles de la Nueva Garantía</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3"><label class="form-label">Estado</label><select class="form-select" wire:model="nueva_garantia_status"><option value="activa">Activa</option><option value="terminada">Terminada</option></select></div>
                                <div class="col-md-6 mb-3"><label class="form-label">Fecha Vencimiento</label><input type="date" class="form-control" wire:model="nueva_garantia_fecha">@error('nueva_garantia_fecha')<span class="text-danger">{{$message}}</span>@enderror</div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3"><label class="form-label">Empresa</label><input type="text" class="form-control" wire:model="nueva_garantia_empresa">@error('nueva_garantia_empresa')<span class="text-danger">{{$message}}</span>@enderror</div>
                                <div class="col-md-6 mb-3"><label class="form-label">Contacto</label><input type="text" class="form-control" wire:model="nueva_garantia_contacto">@error('nueva_garantia_contacto')<span class="text-danger">{{$message}}</span>@enderror</div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button wire:click="closeModal()" type="button" class="btn btn-secondary">Cancelar</button>
                        <button type="submit" class="btn btn-primary">{{ $isEditMode ? 'Guardar Cambios' : 'Guardar Equipo' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>