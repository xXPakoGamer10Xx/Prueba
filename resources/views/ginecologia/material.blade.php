@extends('components.layouts.ginecologia.nav-ginecologia')

@section('contenido')

    <main class="container my-5">
    <h3 class="text-center fw-bold mb-4">Listado de material</h3>

    {{-- Bloque para mostrar mensajes de éxito o errores de validación --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>¡Error!</strong> Por favor, corrige los siguientes errores:
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        
        {{-- CAMBIO APLICADO AQUÍ: Buscador como formulario --}}
        <form action="{{ route('material.index') }}" method="GET" class="d-flex">
            <div class="input-group w-auto">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control" 
                    placeholder="Buscar por nombre..." 
                    value="{{ request('search') }}">
                <button class="btn btn-outline-secondary" type="submit">🔍</button>
            </div>
        </form>

        <button class="btn-add" data-bs-toggle="modal" data-bs-target="#modalAgregar">+</button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Cantidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="materiales-table-body">
                @forelse ($materiales as $material)
                    <tr>
                        <td>{{ $material->id_material }}</td>
                        <td>{{ $material->nombre_material }}</td>
                        <td>{{ $material->cantidad_material }}</td>
                        <td>
                            <button class="icon-btn text-warning btn-editar" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalEditar"
                                    data-url="{{ route('material.update', $material) }}"
                                    data-nombre="{{ $material->nombre_material }}"
                                    data-cantidad="{{ $material->cantidad_material }}">
                                ✏️
                            </button>
                            <button class="icon-btn text-danger btn-eliminar" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalEliminar"
                                    data-url="{{ route('material.destroy', $material) }}">
                                🗑️
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No se encontraron materiales.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {!! $materiales->links() !!}
    </div>

</main>

{{-- =================================================================== --}}
{{-- MODALES (sin cambios) --}}
{{-- =================================================================== --}}

<div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-cafe text-white">
                <h5 class="modal-title">Agregar Material</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('material.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="id_material" class="form-label fw-bold">ID del Material</label>
                        <input id="id_material" name="id_material" type="text" class="form-control" placeholder="Ej. MAT-001" required value="{{ old('id_material') }}">
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-bold">Nombre</label>
                        <input id="nombre" name="nombre" class="form-control" placeholder="Ej. Guantes" required value="{{ old('nombre') }}">
                    </div>
                    <div class="mb-3">
                        <label for="cantidad" class="form-label fw-bold">Cantidad</label>
                        <input id="cantidad" name="cantidad" type="number" class="form-control" placeholder="Ej. 25" required value="{{ old('cantidad') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">Editar Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditar" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nombre" class="form-label fw-bold">Nombre</label>
                        <input id="edit_nombre" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_cantidad" class="form-label fw-bold">Cantidad</label>
                        <input id="edit_cantidad" name="cantidad" type="number" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Eliminar Material</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEliminar" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    ¿Estás seguro de que deseas eliminar este material?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection