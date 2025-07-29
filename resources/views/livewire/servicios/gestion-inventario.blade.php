<div>
    @if (session()->has('mensaje'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('mensaje') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Inventario de Equipos</h2>
        <div class="d-flex align-items-center gap-2">
            <input wire:model.live.debounce.300ms="search" type="search" class="form-control" placeholder="Buscar equipo, serie, área...">
            <button wire:click="create()" class="btn btn-primary flex-shrink-0"><i class="fas fa-plus me-2"></i>Agregar Equipo</button>
        </div>
    </div>
    
    <section>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Equipo</th>
                        <th>Num. Serie</th>
                        <th>Pertenencia</th>
                        <th>Status</th>
                        <th>Área</th>
                        <th>Garantía</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inventario as $item)
                    <tr>
                        <td>{{ $item->id_inventario }}</td>
                        <td>{{ $item->equipo->nombre ?? 'N/A' }}</td>
                        <td>{{ $item->num_serie }}</td>
                        <td>{{ ucfirst($item->pertenencia) }}</td>
                        <td>{{ ucfirst($item->status) }}</td>
                        <td>{{ $item->area->nombre ?? 'N/A' }}</td>
                        <td>{{ $item->garantia->status ?? 'N/A' }}</td>
                        <td class='text-center'>
                            {{-- Botones de acción --}}
                            <button wire:click="edit({{ $item->id_inventario }})" class="btn btn-warning btn-sm" title="Editar Equipo"><i class="fas fa-edit"></i></button>
                            <button wire:click="registrarMantenimiento({{ $item->id_inventario }})" class="btn btn-info btn-sm" title="Registrar Mantenimiento"><i class="fas fa-wrench"></i></button>
                            <button wire:click="registrarBaja({{ $item->id_inventario }})" class="btn btn-danger btn-sm" title="Registrar Baja"><i class="fas fa-arrow-down"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center">No se encontraron registros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $inventario->links() }}
    </section>

    <!-- Modal para Agregar/Editar Equipo -->
    @if ($isModalOpen)
    <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEditMode ? 'Editar Equipo del Inventario' : 'Agregar Nuevo Equipo al Inventario' }}</h5>
                    <button wire:click="closeModal()" type="button" class="btn-close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="store" id="inventory-form">
                        <!-- Datos del Equipo -->
                        <h6>Datos del Equipo</h6>
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label">Nombre del Equipo</label><input type="text" class="form-control" wire:model="nombre">@error('nombre')<span class="text-danger">{{$message}}</span>@enderror</div>
                            <div class="col-md-6"><label class="form-label">Marca</label><input type="text" class="form-control" wire:model="marca"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12"><label class="form-label">Modelo</label><input type="text" class="form-control" wire:model="modelo"></div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6"><label class="form-label">Frecuencia de Mantenimiento</label><select class="form-select" wire:model="frecuencia_mantenimiento"><option value="mensual">Mensual</option><option value="trimestral">Trimestral</option><option value="semestral">Semestral</option><option value="anual">Anual</option></select></div>
                        </div>
                        <hr>
                        <!-- Datos de Inventario -->
                        <h6>Datos de Inventario</h6>
                        <div class="row mb-3">
                            <div class="col-md-4"><label class="form-label">Número de Serie</label><input type="text" class="form-control" wire:model="num_serie"></div>
                            <div class="col-md-4"><label class="form-label">Serie SICOPA</label><input type="text" class="form-control" wire:model="num_serie_sicopa"></div>
                            <div class="col-md-4"><label class="form-label">Serie SIA</label><input type="text" class="form-control" wire:model="num_serie_sia"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><label class="form-label">Pertenencia</label><select class="form-select" wire:model="pertenencia"><option value="propia">Propia</option><option value="comodato">Comodato</option></select></div>
                            <div class="col-md-4"><label class="form-label">Status</label><select class="form-select" wire:model="status"><option value="funcionando">Funcionando</option><option value="sin funcionar">Sin funcionar</option><option value="parcialmente funcional">Parcialmente funcional</option><option value="proceso de baja">Proceso de baja</option></select></div>
                            <div class="col-md-4"><label class="form-label">Área de Ubicación</label><select class="form-select" wire:model="id_area_fk"><option value="">Seleccione...</option>@foreach($areas as $area)<option value="{{$area->id_area}}">{{$area->nombre}}</option>@endforeach</select>@error('id_area_fk')<span class="text-danger">{{$message}}</span>@enderror</div>
                        </div>
                        <hr>
                        <!-- Datos de Garantía -->
                        <h6>Datos de Garantía</h6>
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label">Garantía</label><select class="form-select" wire:model.live="id_garantia_fk"><option value="">Sin garantía</option><option value="new_warranty">--- Crear Nueva Garantía ---</option>@foreach($garantias as $garantia)<option value="{{$garantia->id_garantia}}">{{$garantia->empresa}} - {{$garantia->status}}</option>@endforeach</select></div>
                        </div>
                        @if($showNewWarrantyFields)
                        <div class="p-3 border rounded bg-light">
                            <h5>Detalles de la Nueva Garantía</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3"><label class="form-label">Status</label><select class="form-select" wire:model="nueva_garantia_status"><option value="activa">Activa</option><option value="terminada">Terminada</option></select></div>
                                <div class="col-md-6 mb-3"><label class="form-label">Fecha Vencimiento</label><input type="date" class="form-control" wire:model="nueva_garantia_fecha"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3"><label class="form-label">Empresa</label><input type="text" class="form-control" wire:model="nueva_garantia_empresa"></div>
                                <div class="col-md-6 mb-3"><label class="form-label">Contacto</label><input type="text" class="form-control" wire:model="nueva_garantia_contacto"></div>
                            </div>
                        </div>
                        @endif
                    </form>
                </div>
                <div class="modal-footer">
                    <button wire:click="closeModal()" type="button" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" form="inventory-form" class="btn btn-primary">{{ $isEditMode ? 'Guardar Cambios' : 'Guardar Equipo' }}</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>
