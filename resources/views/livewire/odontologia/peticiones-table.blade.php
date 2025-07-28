<div>
    @if($pedidos->isEmpty())
        <p class="text-center">No se encontraron peticiones de insumos.</p>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped text-center">
                <thead class="bg-cafe text-white table-dark">
                    <tr>
                        <th scope="col">ID <br>(Almcen)</th>
                        <th scope="col">Clave</th>
                        <th scope="col">Descripción</th>
                        <th scope="col">Cantidad Solicitada</th>
                        <th scope="col">Cantidad Autorizada</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Fecha Solicitada</th>
                        <th scope="col">Fecha Entrega</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pedidos as $pedido)
                        <tr>
                            <td>{{ $pedido->id_insumo_almacen_fk }}</td>
                            {{-- Acceder a las propiedades del insumo a través de la relación --}}
                            <td>{{ $pedido->almacenItem->insumo->clave }}</td>
                            <td>{{ $pedido->almacenItem->insumo->descripcion }}</td>
                            <td>{{ $pedido->cantidad_solicitada }}</td>
                            <td>{{ $pedido->cantidad_autorizada }}</td>
                            <td>{{ $pedido->estado_pedido }}</td>
                            <td>{{ $pedido->fecha_pedido ? $pedido->fecha_pedido->format('d-m-Y') : 'Sin fecha' }}</td>
                            <td>{{ $pedido->fecha_entrega ? $pedido->fecha_entrega->format('d-m-Y') : 'Pendiente' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Enlaces de paginación --}}
        <div class="mt-4">
            {{ $pedidos->links() }}
        </div>
    @endif
</div>