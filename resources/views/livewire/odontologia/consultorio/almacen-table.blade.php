<div>
    @if($almacenItems->isEmpty())
        <p class="text-center">No se encontraron ítems en el almacén.</p>
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
                    @foreach ($almacenItems as $item)
                        <tr>
                            <td>{{ $item->id_insumo_almacen }}</td>
                            <td>{{ $item->insumo->clave ?? 'N/A' }}</td>
                            <td>{{ $item->insumo->descripcion ?? 'N/A' }}</td>
                            <td>{{ $item->insumo->laboratorio->descripcion ?? 'N/A' }}</td>
                            <td>{{ $item->insumo->presentacion->descripcion ?? 'N/A' }}</td>
                            <td>{{ $item->insumo->contenido ?? 'N/A' }}</td>
                            <td>{{ $item->insumo->caducidad ? $item->insumo->caducidad->format('d-m-Y') : 'Sin fecha' }}</td>
                            <td>{{ $item->cantidad }}</td>
                            <td>
                                <i
                                    class='fa-solid fa-plus cursor-pointer'
                                    title="Pedir insumo"
                                ></i>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $almacenItems->links() }}
        </div>
    @endif
</div>