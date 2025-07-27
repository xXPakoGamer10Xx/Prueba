<div>
    <div class="mb-12">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold mb-4 text-gray-800">Gestión de Equipos</h2>
            <button wire:click="create()" class="bg-custom-red hover:bg-red-700 text-white px-4 py-2 rounded-md">
                <i class="fas fa-plus mr-2"></i>Agregar Equipo
            </button>
        </div>
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            ID
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Nombre del Equipo
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Marca
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Modelo
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Cantidad
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Frecuencia Mant.
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($equipos as $equipo)
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{ $equipo->id_equipo }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{ $equipo->nombre ?? 'N/A' }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{ $equipo->marca ?? 'N/A' }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{ $equipo->modelo ?? 'N/A' }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{ $equipo->cantidad ?? 'N/A' }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{ $equipo->frecuencia_mantenimiento ?? 'N/A' }} meses
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <button wire:click="edit({{ $equipo->id_equipo }})" class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="delete({{ $equipo->id_equipo }})" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                         <tr>
                            <td colspan="7" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center text-gray-500">
                                No hay equipos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $equipos->links() }}
        </div>
    </div>

    <!-- Modal para Crear/Editar Equipo -->
    @if ($isModalOpen)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $isEditMode ? 'Editar Equipo' : 'Agregar Nuevo Equipo' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form wire:submit.prevent="store">
                    <div class="mb-4">
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">Nombre del Equipo:</label>
                        <input type="text" id="nombre" wire:model="nombre" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-custom-red">
                        @error('nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="marca" class="block text-sm font-medium text-gray-700 mb-2">Marca:</label>
                        <input type="text" id="marca" wire:model="marca" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-custom-red">
                        @error('marca') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="modelo" class="block text-sm font-medium text-gray-700 mb-2">Modelo:</label>
                        <input type="text" id="modelo" wire:model="modelo" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-custom-red">
                        @error('modelo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="cantidad" class="block text-sm font-medium text-gray-700 mb-2">Cantidad:</label>
                        <input type="number" id="cantidad" wire:model="cantidad" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-custom-red">
                        @error('cantidad') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="frecuencia_mantenimiento" class="block text-sm font-medium text-gray-700 mb-2">Frecuencia de Mantenimiento (meses):</label>
                        <input type="number" id="frecuencia_mantenimiento" wire:model="frecuencia_mantenimiento" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-custom-red">
                        @error('frecuencia_mantenimiento') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 bg-custom-red text-white rounded-md hover:bg-red-700">
                            {{ $isEditMode ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>