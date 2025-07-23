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
                        @if(request()->routeIs('odontologia.consultorio.index'))
                            <th scope="col">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($insumosConsultorio as $insumoConsultorio) {{-- Cambiado a $insumoConsultorio para mayor claridad --}}
                        <tr>
                            <td>{{ $insumoConsultorio->id_insumo_consultorio }}</td>
                            {{-- Accede a las propiedades del insumo a través de la relación --}}
                            <td>{{ $insumoConsultorio->insumo->clave }}</td>
                            <td>{{ $insumoConsultorio->insumo->descripcion }}</td>
                            <td>{{ $insumoConsultorio->insumo->laboratorio->descripcion }}</td>
                            <td>{{ $insumoConsultorio->insumo->presentacion->descripcion }}</td>
                            <td>{{ $insumoConsultorio->insumo->contenido }}</td>
                            {{-- Muestra la caducidad formateada --}}
                            <td>{{ $insumoConsultorio->insumo->caducidad ? $insumoConsultorio->insumo->caducidad->format('d-m-Y') : 'Sin fecha' }}</td>
                            <td>
                                {{-- Input para la cantidad que actualiza la BD --}}
                                <input
                                    class="w-[3rem] text-center"
                                    type="number"
                                    value="{{ $insumoConsultorio->cantidad }}"
                                    wire:change="updateCantidad({{ $insumoConsultorio->id_insumo_consultorio }}, $event.target.value)"
                                    wire:keydown.enter.prevent="updateCantidad({{ $insumoConsultorio->id_insumo_consultorio }}, $event.target.value)"
                                    min="0"
                                >
                            </td>
                            @if(request()->routeIs('odontologia.consultorio.index'))
                                <td>
                                    <i
                                    class='fa-solid fa-trash-can cursor-pointer'
                                    wire:click="confirmDelete({{ $insumoConsultorio->id_insumo_consultorio }})"
                                    title="Eliminar registro"
                                    ></i>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $insumosConsultorio->links() }}
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