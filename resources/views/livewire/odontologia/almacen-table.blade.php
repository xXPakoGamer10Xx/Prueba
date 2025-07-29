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
                            <td>
                                <p class="m-0 {{ $item->insumo->caducidad && $item->insumo->caducidad->isPast() ? 'text-red-500' : '' }}">
                                {{ $item->insumo->caducidad ? $item->insumo->caducidad->format('d-m-Y') : 'Sin fecha' }}
                            </p>
                            <td>
                                {{-- Input para la cantidad que actualiza la BD --}}
                                @if(Auth::user()->rol == 'odontologia_consultorio')
                                    {{ $item->cantidad }}
                                @elseif(Auth::user()->rol == 'odontologia_almacen')
                                    <input
                                        class="w-[3rem] text-center border-0"
                                        type="number"
                                        value="{{ $item->cantidad }}"
                                        wire:change="updateCantidad({{ $item->id_insumo_almacen }}, $event.target.value)"
                                        wire:keydown.enter.prevent="updateCantidad({{ $item->id_insumo_almacen }}, $event.target.value)"
                                        min="0"
                                    >
                                @endif
                            </td>
                            <td>
                                @if(Auth::user()->rol == 'odontologia_consultorio')
                                <button
                                    class="btn btn-primary btn-sm text-white"
                                    title="Pedir insumo"
                                    wire:click="openPeticionModal({{ $item->id_insumo_almacen }})"
                                >
                                    <i
                                    class='fa-solid fa-plus cursor-pointer'
                                    ></i>
                                </button>
                                @else
                                <button class="btn btn-danger btn-sm"
                                    wire:click="confirmDelete({{ $item->id_insumo_almacen }})"
                                    title="Eliminar registro"
                                >
                                    <i
                                    class='fa-solid fa-trash-can cursor-pointer'
                                    ></i>
                                </button>
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
        message="¿Está seguro que desea eliminar este registro?"
    />
</div>