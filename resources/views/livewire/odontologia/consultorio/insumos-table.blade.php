<div>
    @if($insumosConsultorio->isEmpty())
        <p class="text-center">No se encontraron insumos en el consultorio.</p>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped text-center">
                <thead class="bg-cafe text-white table-dark">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Clave</th>
                        <th scope="col">Descripción</th>
                        <th scope="col">Laboratorio</th>
                        <th scope="col">Presentación</th>
                        <th scope="col">Contenido</th>
                        <th scope="col">Caducidad</th>
                        <th scope="col">Cantidad</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($insumosConsultorio as $insumo)
                        <tr>
                            <td>{{ $insumo->id_insumo_consultorio }}</td>
                            <td>{{ $insumo->clave }}</td>
                            <td>{{ $insumo->descripcion }}</td>
                            <td>{{ $insumo->laboratorio }}</td>
                            <td>{{ $insumo->presentacion }}</td>
                            <td>{{ $insumo->contenido }}</td>
                            <td>{{ $insumo->caducidad ? $insumo->caducidad->format('Y-m-d') : 'Sin fecha' }}</td>
                            <td><input class="w-[3rem] text-center" type="number" value="{{ $insumo->cantidad }}"></td>
                            <td><i class='fa-solid fa-trash-can cursor-pointer'></i></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $insumosConsultorio->links() }}
        </div>
    @endif
</div>