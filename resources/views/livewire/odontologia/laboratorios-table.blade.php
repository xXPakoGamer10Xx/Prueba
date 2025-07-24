<div>
    @if($laboratorios->isEmpty())
        <p class="text-center">No se encontraron laboratorios registrados.</p>
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
                    @foreach ($laboratorios as $laboratorio)
                        <tr>
                            <td>{{ $laboratorio->id_laboratorio }}</td>
                            <td>{{ $laboratorio->descripcion }}</td>
                            <td>
                                <i
                                    class='fa-solid fa-trash-can cursor-pointer'
                                    wire:click="confirmDelete({{ $laboratorio->id_laboratorio }})"
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
            {{ $laboratorios->links() }}
        </div>

        {{-- Modal de confirmación de eliminación --}}
        <x-modals.delete-confirmation
            modalId="modalEliminarLaboratorio"
            formId="formularioEliminarLaboratorio"
            wireModel="laboratorioToDeleteId"
            confirmAction="deleteLaboratorio"
        />
    @endif
</div>