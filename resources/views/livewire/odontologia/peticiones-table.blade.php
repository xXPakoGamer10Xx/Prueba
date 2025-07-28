<div>
    @if($pedidos->isEmpty())
        <p class="text-center">No se encontraron peticiones de insumos.</p>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped text-center">
                <thead class="bg-cafe text-white table-dark">
                    <tr>
                        <th scope="col">ID<br>(Almacén)</th>
                        <th scope="col">Clave</th>
                        <th scope="col">Descripción</th>
                        <th scope="col">Cantidad<br>Solicitada</th>
                        <th scope="col">Cantidad<br>Autorizada</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Fecha<br>Solicitada</th>
                        <th scope="col">Fecha<br>Entrega</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pedidos as $pedido)
                        <tr
                            @if($pedido->estado_pedido == 'Cancelado')
                            class="opacity-50"
                            @endif
                        >
                            <td>{{ $pedido->id_insumo_almacen_fk }}</td>
                            {{-- Acceder a las propiedades del insumo a través de la relación --}}
                            <td>{{ $pedido->almacenItem->insumo->clave }}</td>
                            <td>{{ $pedido->almacenItem->insumo->descripcion }}</td>
                            <td>{{ $pedido->cantidad_solicitada }}</td>
                            <td>
                                @if($pedido->estado_pedido == 'Cancelado')
                                    Cancelado
                                @else
                                    {{ $pedido->cantidad_autorizada }}
                                @endif
                            </td>
                            <td>{{ $pedido->estado_pedido }}</td>
                            <td>{{ $pedido->fecha_pedido ? $pedido->fecha_pedido->format('d-m-Y') : 'Sin fecha' }}</td>
                            <td>
                                @if($pedido->estado_pedido == 'Cancelado')
                                    Cancelado
                                @else
                                    {{ $pedido->fecha_entrega ? $pedido->fecha_entrega->format('d-m-Y') : 'Pendiente' }}
                                @endif
                        </td>
                            <td class='d-flex justify-content-evenly'>
                                <button 
                                    type='button'
                                    class='btn-cancelar text-black border-0 bg-transparent'
                                    title="Cancelar pedido"
                                    data-bs-toggle='modal' 
                                    data-bs-target='#modalCancelarPedido'
                                    @if($pedido->estado_pedido == 'Cancelado')
                                        disabled
                                    @endif
                                >
                                    <i class='fa-solid fa-xmark'></i>
                                </button>
                            </td>
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