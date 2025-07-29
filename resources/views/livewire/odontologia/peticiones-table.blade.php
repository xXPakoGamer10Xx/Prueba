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
                            @if($pedido->estado_pedido == 'Cancelado' || $pedido->estado_pedido == 'Entregado')
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
                                @elseif($pedido->estado_pedido == 'Pendiente')
                                    Pendiente
                                @else
                                    {{ $pedido->cantidad_autorizada }}
                                @endif
                            </td>
                            <td>{{ $pedido->estado_pedido }}</td>
                            <td>{{ $pedido->fecha_pedido ? $pedido->fecha_pedido->format('d-m-Y') : 'Sin fecha' }}</td>
                            <td>
                                @if($pedido->estado_pedido == 'Cancelado')
                                    Cancelado

                                @elseif($pedido->estado_pedido == 'Pendiente')
                                    Pendiente
                                @else
                                    {{ $pedido->fecha_entrega ? $pedido->fecha_entrega->format('d-m-Y') : 'Pendiente' }}
                                @endif
                        </td>
                            <td class='d-flex justify-content-evenly'>
                                <div
                                    class="flex gap-2"
                                >
                                    @if($pedido->estado_pedido == 'Pendiente')
                                        @if(Auth::user()->rol == 'odontologia_almacen')
                                        <button 
                                            type='button'
                                            class="btn btn-success btn-sm text-white"
                                            title="Confirmar pedido"
                                            wire:click="confirmPedidoModal({{ $pedido->id_pedido }})"
                                        >
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                        @endif

                                    @endif

                                        <button 
                                            type='button'
                                            class='btn-cancelar text-black border-0 bg-transparent'
                                            title="Cancelar pedido"
                                            wire:click="confirmCancel({{ $pedido->id_pedido }})"
                                            @if($pedido->estado_pedido == 'Cancelado' || $pedido->estado_pedido == 'Entregado')
                                                disabled
                                            @endif
                                        >
                                            <i class='fa-solid fa-xmark'></i>
                                        </button>

                                    @if(Auth::user()->rol == 'odontologia_almacen')
                                    <button 
                                        type='button'
                                        class="btn btn-danger btn-sm"
                                        title="Eliminar registro"
                                        wire:click="confirmDelete({{ $pedido->id_pedido }})"
                                    >
                                        <i class='fas fa-trash-alt cursor-pointer'></i>
                                    </button>
                                    @endif
                                </div>
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

    {{-- Modal de Cancelar Pedido --}}
    <x-modals.delete-confirmation
        modalId="modalConfirmarPedido"
        formId="formularioCancelarPedido"
        wireModel="pedidoToConfirmId"
        confirmAction="confirmPedido"
        iconClass=""
        iconSize="4rem"
        width="18.75rem"
        titleId="modalEliminarPedidoLabel"
        title="Confirmar pedido"
        message="¿Está seguro que desea cancelar este pedido?"
    ></x-modals.delete-confirmation>

    {{-- Modal de Cancelar Pedido --}}
    <x-modals.delete-confirmation
        modalId="modalCancelarPedido"
        formId="formularioCancelarPedido"
        wireModel="pedidoToCancelId"
        confirmAction="cancelPedido"
        iconClass="fa-solid fa-circle-exclamation"
        iconSize="4rem"
        width="18.75rem"
        titleId="modalEliminarPedidoLabel"
        message="¿Está seguro que desea cancelar este pedido?"
    ></x-modals.delete-confirmation>

        {{-- Modal de Eliminar Registro --}}
    <x-modals.delete-confirmation
        modalId="modalEliminarRegistro"
        formId="formularioCancelarPedido"
        wireModel="pedidoToCancelId"
        confirmAction="deleteRegistro"
        iconClass="fa-solid fa-circle-exclamation"
        iconSize="4rem"
        width="18.75rem"
        titleId="modalEliminarPedidoLabel"
        message="¿Está seguro que desea eliminar este registro?"
    ></x-modals.delete-confirmation>
</div>