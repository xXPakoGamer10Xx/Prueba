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
                                    class='fa-solid fa-trash-can cursor-pointer'
                                    wire:click="confirmDelete({{ $item->id_almacen }})"
                                    title="Eliminar registro"
                                ></i>
                                {{-- Si quieres un input de cantidad actualizable, sería similar al de insumos --}}
                                <!-- <input
                                    class="w-[3rem] text-center"
                                    type="number"
                                    value="{{ $item->cantidad }}"
                                    wire:change="updateCantidad({{ $item->id_almacen }}, $event.target.value)"
                                    wire:keydown.enter.prevent="updateCantidad({{ $item->id_almacen }}, $event.target.value)"
                                    min="0"
                                > -->
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

    {{-- Modal de confirmación de eliminación (asegúrate de que existe en x-modals) --}}
    <x-modals.delete-confirmation
        modalId="deleteAlmacenItemModal"
        formId="deleteAlmacenItemForm"
        wireModel="itemToDeleteId"
        confirmAction="deleteItem"
        message="¿Está seguro que desea eliminar este ítem del almacén?"
    />
</div>