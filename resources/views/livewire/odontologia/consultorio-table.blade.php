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
                        @if(Auth::user()->rol == 'odontologia_consultorio')
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
                            <td>
                                <p class="m-0 {{ $insumoConsultorio->insumo->caducidad && $insumoConsultorio->insumo->caducidad->isPast() ? 'text-red-500' : '' }}">
                                {{ $insumoConsultorio->insumo->caducidad ? $insumoConsultorio->insumo->caducidad->format('d-m-Y') : 'Sin fecha' }}
                            </p>

                            </td>
                            <td>
                                {{-- Input para la cantidad que actualiza la BD --}}
                                @if(Auth::user()->rol == 'odontologia_consultorio')
                                    <input
                                        class="w-[3rem] text-center border-0"
                                        type="number"
                                        value="{{ $insumoConsultorio->cantidad }}"
                                        wire:change="updateCantidad({{ $insumoConsultorio->id_insumo_consultorio }}, $event.target.value)"
                                        wire:keydown.enter.prevent="updateCantidad({{ $insumoConsultorio->id_insumo_consultorio }}, $event.target.value)"
                                        min="0" max="1000"
                                    >
                                @elseif(Auth::user()->rol == 'odontologia_almacen')
                                    {{ $insumoConsultorio->cantidad }}
                                @endif
                            </td>
                            @if(Auth::user()->rol == 'odontologia_consultorio')
                                <td>
                                    <button class="btn btn-danger btn-sm"
                                        wire:click="confirmDelete({{ $insumoConsultorio->id_insumo_consultorio }})"
                                        title="Eliminar registro"
                                        data-bs-toggle='modal' 
                                        data-bs-target='#modalEliminarInsumo'
                                    >
                                        <i
                                        class='fas fa-trash-alt cursor-pointer'
                                        ></i>
                                    </button>
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
        message="¿Está seguro que desea eliminar este registro?"
    />
</div>