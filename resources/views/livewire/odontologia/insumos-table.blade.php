<div>
    @if($insumos->isEmpty())
        <p class="text-center">No se encontraron insumos en el inventario.</p>
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
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($insumos as $insumo) {{-- Cambiado a $insumo para mayor claridad --}}
                        <tr>
                            <td>{{ $insumo->id_insumo }}</td>
                            {{-- Accede a las propiedades del insumo a través de la relación --}}
                            <td>{{ $insumo->clave }}</td>
                            <td>{{ $insumo->descripcion }}</td>
                            <td>{{ $insumo->laboratorio->descripcion }}</td>
                            <td>{{ $insumo->presentacion->descripcion }}</td>
                            <td>{{ $insumo->contenido }}</td>
                            {{-- Muestra la caducidad formateada --}}
                            <td>{{ $insumo->caducidad ? $insumo->caducidad->format('d-m-Y') : 'Sin fecha' }}</td>
                            <td>
                                <i
                                class='fa-solid fa-trash-can cursor-pointer'
                                wire:click="confirmDelete({{ $insumo->id_insumo }})"
                                title="Eliminar registro"
                                ></i>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $insumos->links() }}
        </div>
    @endif

    <x-modals.delete-confirmation
        modalId="modalEliminarInsumo"
        formId="formularioEliminarInsumo"
        wireModel="insumoToDeleteId"
        confirmAction="deleteInsumo"
        message="¿Está seguro que desea eliminar este insumo?"
    />
</div>