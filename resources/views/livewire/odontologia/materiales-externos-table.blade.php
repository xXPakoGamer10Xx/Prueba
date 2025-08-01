<div>
    @if($materialesExternos->isEmpty())
        <p class="text-center">No se encontraron materiales externos registrados.</p>
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
                            <td>{{ $material->descripcion }}</td>
                            <td>
                                <input
                                    class="w-[3rem] text-center border-0"
                                    type="number"
                                    value="{{ $material->cantidad }}"
                                    wire:change="updateCantidad({{ $material->id_material }}, $event.target.value)"
                                    wire:keydown.enter.prevent="updateCantidad({{ $material->id_material }}, $event.target.value)"
                                    min="0"
                                >
                            </td>
                            <td>
                                <button class="btn btn-danger btn-sm"
                                    wire:click="confirmDelete({{ $material->id_material }})"
                                    title="Eliminar registro"
                                    data-bs-toggle='modal' 
                                    data-bs-target='#deleteMaterialModal'
                                >
                                    <i
                                    class='fa-solid fa-trash-can cursor-pointer'
                                    ></i>
                                </button>
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
        message="¿Está seguro que desea eliminar este registro?"
    >
    </x-modals.delete-confirmation>
</div>