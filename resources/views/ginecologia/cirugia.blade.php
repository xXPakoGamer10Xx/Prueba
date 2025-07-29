@extends('components.layouts.ginecologia.nav-ginecologia')

@section('contenido')
<main class="container my-5">
    <h3 class="text-center fw-bold mb-4">Gestión de Cirugías</h3>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>¡Error!</strong> Revisa los campos del formulario.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    {{-- ################# TABLA DE CIRUGÍAS GENERALES ################# --}}
    {{-- ################# TABLA DE CIRUGÍAS GENERALES ################# --}}
<div class="card shadow-sm mb-5">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Cirugías Generales</h5>
        <div class="d-flex align-items-center">
            {{-- Formulario de Búsqueda --}}
            <form action="{{ route('cirugia.index') }}" method="GET" class="d-flex me-2">
                <div class="input-group input-group-sm">
                    <input type="text" name="search_general" class="form-control" placeholder="Buscar..." value="{{ request('search_general') }}">
                    <button class="btn btn-outline-secondary" type="submit">🔍</button>
                </div>
            </form>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAgregarGeneral">+</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-hover text-center">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Fecha Ingreso</th>
                        <th>Fecha Egreso</th>
                        <th>Pieza Patológica</th>
                        <th>Región Anatómica</th>
                        <th>Tipo</th>
                        <th>Doctor</th>
                        <th>Paciente</th>
                        <th>Diagnóstico</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cirugiasGenerales as $cirugia)
                    <tr>
                        <td>{{ $cirugia->id_cirugia_general }}</td>
                        <td>{{ $cirugia->fecha_ingreso ? \Carbon\Carbon::parse($cirugia->fecha_ingreso)->format('d/m/Y') : 'N/A' }}</td>
                        <td>{{ $cirugia->fecha_egreso ? \Carbon\Carbon::parse($cirugia->fecha_egreso)->format('d/m/Y') : 'N/A' }}</td>
                        <td>{{ $cirugia->pieza_patologica }}</td>
                        <td>{{ $cirugia->region_anatomica }}</td>
                        <td>{{ ucfirst($cirugia->tipoCirugia) }}</td>
                        <td>{{ $cirugia->doctor->nombre_completo ?? 'N/A' }}</td>
                        <td>{{ $cirugia->paciente->nombre_completo ?? 'N/A' }}</td>
                        <td>{{ $cirugia->diagnostico->nombre_diagnostico ?? 'N/A' }}</td>
                        <td>
                            <button class="icon-btn text-warning" data-bs-toggle="modal" data-bs-target="#modalEditarGeneral"
                                data-url="{{ route('cirugiageneral.update', $cirugia) }}"
                                data-id="{{ $cirugia->id_cirugia_general }}"
                                data-fecha_ingreso="{{ $cirugia->fecha_ingreso }}"
                                data-fecha_egreso="{{ $cirugia->fecha_egreso }}"
                                data-pieza_patologica="{{ $cirugia->pieza_patologica }}"
                                data-region_anatomica="{{ $cirugia->region_anatomica }}"
                                data-tipoCirugia="{{ $cirugia->tipoCirugia }}"
                                data-id_doctor="{{ $cirugia->id_doctor }}"
                                data-id_paciente="{{ $cirugia->id_paciente }}"
                                data-id_diagnostico="{{ $cirugia->id_diagnostico }}">✏️
                            </button>
                            <button class="icon-btn text-danger" data-bs-toggle="modal" data-bs-target="#modalEliminarGeneral"
                                data-url="{{ route('cirugiageneral.destroy', $cirugia) }}">🗑️
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        {{-- Ajustamos el colspan al nuevo número de columnas --}}
                        <td colspan="10" class="text-center">No hay cirugías generales registradas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center">
            {{-- appends() ayuda a que la paginación funcione junto con la búsqueda --}}
            {!! $cirugiasGenerales->appends(request()->except('ginecologicas_page'))->links() !!}
        </div>
    </div>
</div>

    {{-- ################# TABLA DE CIRUGÍAS GINECOLÓGICAS ################# --}}
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Cirugías Ginecológicas</h5>
        <div class="d-flex align-items-center">
            {{-- Formulario de Búsqueda --}}
            <form action="{{ route('cirugia.index') }}" method="GET" class="d-flex me-2">
                <div class="input-group input-group-sm">
                    <input type="text" name="search_ginecologica" class="form-control" placeholder="Buscar..." value="{{ request('search_ginecologica') }}">
                    <button class="btn btn-outline-secondary" type="submit">🔍</button>
                </div>
            </form>
            <button class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalAgregarGinecologica">+</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-hover text-center">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Paciente</th>
                        <th>Doctor</th>
                        <th>Fecha Ingreso</th>
                        <th>Fecha Egreso</th>
                        <th>Tipo</th>
                        <th>Diagnóstico</th>
                        <th>APEO</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cirugiasGinecologicas as $cirugia)
                    <tr>
                        <td>{{ $cirugia->id_cirugia_ginecologica }}</td>
                        <td>{{ $cirugia->paciente->nombre_completo ?? 'N/A' }}</td>
                        <td>{{ $cirugia->doctor->nombre_completo ?? 'N/A' }}</td>
                        <td>{{ $cirugia->fecha_ingreso ? \Carbon\Carbon::parse($cirugia->fecha_ingreso)->format('d/m/Y') : 'N/A' }}</td>
                        <td>{{ $cirugia->fecha_egreso ? \Carbon\Carbon::parse($cirugia->fecha_egreso)->format('d/m/Y') : 'N/A' }}</td>
                        <td>{{ ucfirst($cirugia->tipoCirugia) }}</td>
                        <td>{{ $cirugia->diagnostico->nombre_diagnostico ?? 'N/A' }}</td>
                        <td>{{ $cirugia->apeo }}</td>
                        <td>
                           <button class="icon-btn text-warning" data-bs-toggle="modal" data-bs-target="#modalEditarGinecologica"
                                data-url="{{ route('cirugiaginecologica.update', $cirugia) }}"
                                data-id="{{ $cirugia->id_cirugia_ginecologica }}"
                                data-fecha_ingreso="{{ $cirugia->fecha_ingreso }}"
                                data-fecha_egreso="{{ $cirugia->fecha_egreso }}"
                                data-apeo="{{ $cirugia->apeo }}"
                                data-tipoCirugia="{{ $cirugia->tipoCirugia }}"
                                data-id_doctor="{{ $cirugia->id_doctor }}"
                                data-id_paciente="{{ $cirugia->id_paciente }}"
                                data-id_diagnostico="{{ $cirugia->id_diagnostico }}">✏️
                            </button>
                            <button class="icon-btn text-danger" data-bs-toggle="modal" data-bs-target="#modalEliminarGinecologica"
                                data-url="{{ route('cirugiaginecologica.destroy', $cirugia) }}">🗑️
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        {{-- Ajustamos el colspan al nuevo número de columnas --}}
                        <td colspan="9" class="text-center">No hay cirugías ginecológicas registradas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center">
            {{-- appends() ayuda a que la paginación funcione junto con la búsqueda --}}
            {!! $cirugiasGinecologicas->appends(request()->except('generales_page'))->links() !!}
        </div>
    </div>
</div>
</main>

{{-- Incluimos los modales desde archivos separados para mantener el orden --}}
@include('ginecologia.modals.general')
@include('ginecologia.modals.ginecologica')

@endsection