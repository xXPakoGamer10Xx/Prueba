<div>
    @if($almacenItems->isEmpty())
        <p class="text-center">No se encontraron insumos en el almacén.</p>
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
                            <td>
                                {{-- Input para la cantidad que actualiza la BD --}}
                                @if(Auth::user()->rol == 'odontologia_consultorio')
                                    {{ $item->cantidad }}
                                @elseif(Auth::user()->rol == 'odontologia_almacen')
                                    <input
                                        class="w-[3rem] text-center"
                                        type="number"
                                        value="{{ $item->cantidad }}"
                                        {{-- 
                                        wire:change="updateCantidad({{ $insumoConsultorio->id_insumo_consultorio }}, $event.target.value)"
                                        wire:keydown.enter.prevent="updateCantidad({{ $insumoConsultorio->id_insumo_consultorio }}, $event.target.value)"
                                        --}}
                                        min="0"
                                    > 
                                @endif
                            </td>
                            <td>
                                @if(Auth::user()->rol == 'odontologia_consultorio')
                                    <i
                                    class='fa-solid fa-plus cursor-pointer'
                                    title="Pedir insumo"
                                    ></i>
                                @else
                                    <i
                                    class='fa-solid fa-trash-can cursor-pointer'
                                    wire:click="confirmDelete({{ $item->id_insumo_almacen }})"
                                    title="Eliminar registro"
                                    ></i>
                                @endif
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

    <x-modals.delete-confirmation
        modalId="modalEliminarInsumo"
        formId="formularioEliminarInsumo"
        wireModel="insumoToDeleteId"
        confirmAction="deleteInsumo"
    />
</div>