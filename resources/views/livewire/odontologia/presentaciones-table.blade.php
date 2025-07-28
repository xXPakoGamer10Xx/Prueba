<div>
    @if($presentaciones->isEmpty())
        <p class="text-center">No se encontraron presentaciones registradas.</p>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped text-center">
                <thead class="bg-cafe text-white table-dark">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Descripción</th>
                        <th scope="col" class="w-[8rem]">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($presentaciones as $presentacion)
                        <tr>
                            <td>{{ $presentacion->id_presentacion }}</td>
                            <td>{{ $presentacion->descripcion }}</td>
                            <td>
                                <i
                                    class='fa-solid fa-trash-can cursor-pointer'
                                    wire:click="confirmDelete({{ $presentacion->id_presentacion }})"
                                    title="Eliminar registro"
                                ></i>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $presentaciones->links() }}
        </div>

        {{-- Modal de confirmación de eliminación --}}
        <x-modals.delete-confirmation
            modalId="modalEliminarPresentacion"
            formId="formularioEliminarPresentacion"
            wireModel="presentacionToDeleteId"
            confirmAction="deletePresentacion"
        />
    @endif
</div>