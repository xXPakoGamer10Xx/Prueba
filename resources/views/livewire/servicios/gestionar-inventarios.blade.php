<div>
    {{--
    Esta vista se dedica exclusivamente a mostrar la tabla de Inventarios.
    --}}
    <div class="mb-12">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold text-gray-800">Gestión de Inventario</h2>
            <button wire:click="create()" class="bg-custom-red hover:bg-red-700 text-white px-4 py-2 rounded-md">
                <i class="fas fa-plus mr-2"></i>Agregar Inventario
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
                            Equipo
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Número de Serie
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Área
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Pertenencia
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inventarios as $item)
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{ $item->id_inventario }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{ $item->equipo->nombre ?? 'N/A' }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{ $item->num_serie ?? 'N/A' }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{ $item->area->nombre ?? 'N/A' }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{ ucfirst($item->pertenencia ?? 'N/A') }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{ ucfirst($item->status ?? 'N/A') }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <button wire:click="edit({{ $item->id_inventario }})" class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="delete({{ $item->id_inventario }})" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center text-gray-500">
                                No hay registros de inventario disponibles.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Enlaces de paginación para esta tabla --}}
        <div class="mt-4">
            {{ $inventarios->links() }}
        </div>
    </div>

    <!-- Modal para Crear/Editar Inventario -->
    @if ($isModalOpen)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $isEditMode ? 'Editar Inventario' : 'Agregar Nuevo Inventario' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form wire:submit.prevent="store">
                    <div class="mb-4">
                        <label for="id_equipo_fk" class="block text-sm font-medium text-gray-700 mb-2">Equipo:</label>
                        <select id="id_equipo_fk" wire:model="id_equipo_fk" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-custom-red">
                            <option value="">Seleccionar equipo...</option>
                            @foreach($equipos as $equipo)
                                <option value="{{ $equipo->id_equipo }}">{{ $equipo->nombre }}</option>
                            @endforeach
                        </select>
                        @error('id_equipo_fk') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="num_serie" class="block text-sm font-medium text-gray-700 mb-2">Número de Serie:</label>
                        <input type="text" id="num_serie" wire:model="num_serie" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-custom-red">
                        @error('num_serie') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="num_serie_sicopa" class="block text-sm font-medium text-gray-700 mb-2">Número de Serie SICOPA:</label>
                        <input type="text" id="num_serie_sicopa" wire:model="num_serie_sicopa" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-custom-red">
                        @error('num_serie_sicopa') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="num_serie_sia" class="block text-sm font-medium text-gray-700 mb-2">Número de Serie SIA:</label>
                        <input type="text" id="num_serie_sia" wire:model="num_serie_sia" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-custom-red">
                        @error('num_serie_sia') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="id_area_fk" class="block text-sm font-medium text-gray-700 mb-2">Área:</label>
                        <select id="id_area_fk" wire:model="id_area_fk" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-custom-red">
                            <option value="">Seleccionar área...</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id_area }}">{{ $area->nombre }}</option>
                            @endforeach
                        </select>
                        @error('id_area_fk') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="pertenencia" class="block text-sm font-medium text-gray-700 mb-2">Pertenencia:</label>
                        <select id="pertenencia" wire:model="pertenencia" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-custom-red">
                            <option value="">Seleccionar pertenencia...</option>
                            <option value="propia">Propia</option>
                            <option value="prestamo">Préstamo</option>
                            <option value="comodato">Comodato</option>
                        </select>
                        @error('pertenencia') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Estado:</label>
                        <select id="status" wire:model="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-custom-red">
                            <option value="">Seleccionar estado...</option>
                            <option value="funcionando">Funcionando</option>
                            <option value="sin funcionar">Sin Funcionar</option>
                            <option value="parcialmente funcional">Parcialmente Funcional</option>
                            <option value="proceso de baja">Proceso de Baja</option>
                        </select>
                        @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="id_garantia_fk" class="block text-sm font-medium text-gray-700 mb-2">Garantía (Opcional):</label>
                        <select id="id_garantia_fk" wire:model="id_garantia_fk" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-custom-red">
                            <option value="">Sin garantía</option>
                            @foreach($garantias as $garantia)
                                <option value="{{ $garantia->id_garantia }}">{{ $garantia->empresa ?? 'Garantía #' . $garantia->id_garantia }}</option>
                            @endforeach
                        </select>
                        @error('id_garantia_fk') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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