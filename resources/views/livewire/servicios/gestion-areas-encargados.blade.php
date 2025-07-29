<div>
    {{-- Alertas de sesión --}}
    @if (session()->has('mensaje'))
        <div class="alert alert-{{ session('mensaje_tipo', 'info') }} alert-dismissible fade show" role="alert">
            {{ session('mensaje') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <section class="card mb-5">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h3 class="h5 mb-0"><i class="fas fa-map-marker-alt me-2"></i>Áreas del Hospital</h3>
            <div class="d-flex align-items-center gap-2">
                {{-- CAMBIO AQUÍ: searchArea --}}
                <input wire:model.live.debounce.300ms="searchArea" type="search" class="form-control form-control-sm d-inline-block w-auto" placeholder="Buscar por área o responsable...">
                {{-- CAMBIO AQUÍ: createArea() --}}
                <button wire:click="createArea()" class="btn btn-primary btn-sm flex-shrink-0"><i class="fas fa-plus me-1"></i>Agregar Área</button>
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
                                {{-- CAMBIO AQUÍ: editArea() --}}
                                <button wire:click="editArea({{ $area->id_area }})" class="btn btn-warning btn-sm"><i class="fas fa-pencil-alt"></i></button>
                                {{-- CAMBIO AQUÍ: deleteArea() --}}
                                <button wire:click="deleteArea({{ $area->id_area }})" wire:confirm="¿Seguro que quieres eliminar esta área?" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button>
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
    </section>

    <section class="card" id="lista-encargados">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h3 class="h5 mb-0"><i class="fas fa-users me-2"></i>Encargados de Área</h3>
            <div class="d-flex align-items-center gap-2">
                {{-- ESTOS YA ESTABAN CORRECTOS --}}
                <input wire:model.live.debounce.300ms="searchEncargado" type="search" class="form-control form-control-sm d-inline-block w-auto" placeholder="Buscar por nombre, apellido o cargo...">
                <button wire:click="createEncargado()" class="btn btn-success btn-sm flex-shrink-0"><i class="fas fa-user-plus me-1"></i>Agregar Encargado</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre(s)</th>
                            <th>Apellidos</th>
                            <th>Cargo</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($encargados as $enc)
                        <tr>
                            <td>{{ $enc->id_encargado_area }}</td>
                            <td>{{ $enc->nombre }}</td>
                            <td>{{ $enc->apellidos }}</td>
                            <td>{{ $enc->cargo }}</td>
                            <td class="text-center">
                                {{-- ESTOS YA ESTABAN CORRECTOS --}}
                                <button wire:click="editEncargado({{ $enc->id_encargado_area }})" class="btn btn-warning btn-sm"><i class="fas fa-user-edit"></i></button>
                                <button wire:click="deleteEncargado({{ $enc->id_encargado_area }})" wire:confirm="¿Seguro que quieres eliminar este encargado?" class="btn btn-danger btn-sm"><i class="fas fa-user-times"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center">No hay encargados para mostrar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $encargados->links() }}
        </div>
    </section>

    {{-- CAMBIO AQUÍ: isAreaModalOpen --}}
    @if ($isAreaModalOpen)
    <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- CAMBIO AQUÍ: isAreaEditMode --}}
                    <h5 class="modal-title">{{ $isAreaEditMode ? 'Editar Área' : 'Agregar Nueva Área' }}</h5>
                    {{-- CAMBIO AQUÍ: closeAreaModal --}}
                    <button wire:click="closeAreaModal" type="button" class="btn-close"></button>
                </div>
                {{-- CAMBIO AQUÍ: storeArea --}}
                <form wire:submit.prevent="storeArea">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombreArea" class="form-label">Nombre:</label>
                            {{-- CAMBIO AQUÍ: wire:model="nombreArea" y @error('nombreArea') --}}
                            <input type="text" id="nombreArea" class="form-control" wire:model="nombreArea">
                            @error('nombreArea') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="id_encargado_area_fk" class="form-label">Responsable:</label>
                            {{-- ESTOS YA ESTABAN CORRECTOS --}}
                            <select id="id_encargado_area_fk" class="form-select" wire:model="id_encargado_area_fk">
                                <option value="">Seleccionar...</option>
                                @foreach($listaEncargados as $enc)
                                    <option value="{{$enc->id_encargado_area}}">{{$enc->nombre}} {{$enc->apellidos}}</option>
                                @endforeach
                            </select>
                            @error('id_encargado_area_fk') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- CAMBIO AQUÍ: closeAreaModal --}}
                        <button type="button" wire:click="closeAreaModal" class="btn btn-secondary">Cancelar</button>
                        <button type="submit" class="btn {{ $isAreaEditMode ? 'btn-warning' : 'btn-primary' }}">
                            {{-- CAMBIO AQUÍ: isAreaEditMode --}}
                            {{ $isAreaEditMode ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif

    {{-- ESTE MODAL YA ESTABA CORRECTO EN TU VISTA ANTERIOR --}}
    @if ($isEncargadoModalOpen)
    <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEncargadoEditMode ? 'Editar Encargado' : 'Agregar Encargado' }}</h5>
                    <button wire:click="closeEncargadoModal" type="button" class="btn-close"></button>
                </div>
                <form wire:submit.prevent="storeEncargado">
                    <div class="modal-body">
                        <div class="mb-3"><label for="nombreEncargado" class="form-label">Nombre(s):</label><input type="text" id="nombreEncargado" class="form-control" wire:model="nombreEncargado">@error('nombreEncargado')<span class="text-danger">{{$message}}</span>@enderror</div>
                        <div class="mb-3"><label for="apellidosEncargado" class="form-label">Apellidos:</label><input type="text" id="apellidosEncargado" class="form-control" wire:model="apellidosEncargado">@error('apellidosEncargado')<span class="text-danger">{{$message}}</span>@enderror</div>
                        <div class="mb-3"><label for="cargoEncargado" class="form-label">Cargo:</label><input type="text" id="cargoEncargado" class="form-control" wire:model="cargoEncargado">@error('cargoEncargado')<span class="text-danger">{{$message}}</span>@enderror</div>
                        {{-- Nota: El campo 'contacto' no estaba aquí, si lo necesitas, agrégalo.
                        <div class="mb-3"><label for="contactoEncargado" class="form-label">Contacto:</label><input type="text" id="contactoEncargado" class="form-control" wire:model="contactoEncargado">@error('contactoEncargado')<span class="text-danger">{{$message}}</span>@enderror</div>
                        --}}
                    </div>
                    <div class="modal-footer"><button wire:click="closeEncargadoModal" type="button" class="btn btn-secondary">Cancelar</button><button type="submit" class="btn btn-success">Guardar</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>
