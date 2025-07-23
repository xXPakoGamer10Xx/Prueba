<div>
    @if($materialesExternos->isEmpty())
        <p class="text-center">No se encontraron registros</p>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped text-center">
                <thead class="bg-cafe text-white table-dark">
                    <tr>
                        <th scope="col" class="w-[8rem]">ID</th>
                        <th scope="col">Descripción</th>
                        <th scope="col" class="w-[8rem]">Cantidad</th>
                        <th scope="col" class="w-[8rem]">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($materialesExternos as $material)
                        <tr>
                            <td>{{ $material->id_material }}</td>
                            <td><input class="w-full" type="text" value="{{ $material->descripcion }}"></td>
                            <td><input class="w-full" type="number" min="0" value="{{ $material->cantidad }}"></td>
                            <td>
                                <i
                                    class='fa-solid fa-trash-can cursor-pointer'
                                    wire:click="confirmDelete({{ $material->id_material }})"
                                    title="Eliminar registro"
                                ></i>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Paginación --}}
    <div class="mt-4">
        {{ $materialesExternos->links() }}
    </div>

    {{-- Modal de confirmación de eliminación --}}
    <x-modals.delete-confirmation
        modalId="deleteMaterialModal"
        formId="deleteMaterialForm"
        wireModel="showDeleteConfirmationModal"
        confirmAction="deleteMaterial"
    >
    </x-modals.delete-confirmation>
</div>