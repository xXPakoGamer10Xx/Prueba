    <div>
        {{-- Alertas de sesión --}}
        @if (session()->has('mensaje'))
            <div class="alert alert-{{ session('mensaje_tipo', 'info') }} alert-dismissible fade show" role="alert">
                {{ session('mensaje') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                <div>
                    <h3 class="h5 mb-0"><i class="fas fa-map-marker-alt me-2"></i>Áreas del Hospital</h3>
                </div>
                <div>
                    <input wire:model.live.debounce.300ms="search" type="text" class="form-control form-control-sm d-inline-block w-auto" placeholder="Buscar...">
                    <button wire:click="create()" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Agregar Área</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Área</th>
                                <th>Responsable</th>
                                <th class="text-center" style="width: 120px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($areas as $area)
                            <tr>
                                <td>{{ $area->id_area }}</td>
                                <td>{{ $area->nombre }}</td>
                                <td>{{ $area->encargado->nombre ?? 'N/A' }} {{ $area->encargado->apellidos ?? '' }}</td>
                                <td class="text-center">
                                    <button wire:click="edit({{ $area->id_area }})" class="btn btn-warning btn-sm"><i class="fas fa-pencil-alt"></i></button>
                                    <button wire:click="delete({{ $area->id_area }})" wire:confirm="¿Está seguro de que desea eliminar esta área?" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No hay áreas para mostrar.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($areas->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $areas->links() }}
                </div>
                @endif
            </div>
        </div>
    
        <!-- Modal para Crear/Editar Área -->
        @if ($isModalOpen)
        <div class="modal fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isEditMode ? 'Editar Área' : 'Agregar Nueva Área' }}</h5>
                        <button type="button" wire:click="closeModal" class="btn-close"></button>
                    </div>
                    <form wire:submit.prevent="store">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre:</label>
                                <input type="text" class="form-control" id="nombre" wire:model="nombre">
                                @error('nombre') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="id_encargado_area_fk" class="form-label">Responsable:</label>
                                <select class="form-select" id="id_encargado_area_fk" wire:model="id_encargado_area_fk">
                                    <option value="" disabled>Seleccionar...</option>
                                    @foreach($listaEncargados as $enc)
                                        <option value="{{ $enc->id_encargado_area }}">{{ $enc->nombre }} {{ $enc->apellidos }}</option>
                                    @endforeach
                                </select>
                                @error('id_encargado_area_fk') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" wire:click="closeModal" class="btn btn-secondary">Cancelar</button>
                            <button type="submit" class="btn {{ $isEditMode ? 'btn-warning' : 'btn-primary' }}">
                                {{ $isEditMode ? 'Actualizar' : 'Guardar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
        @endif
    </div>
    