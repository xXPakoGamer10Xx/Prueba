<div>
    {{--
    Esta vista se dedica exclusivamente a mostrar la tabla de Inventarios.
    --}}
    <div class="mb-12">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Gestión de Inventario</h2>
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            ID
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Nombre
                        </th>
                        {{-- Agrega aquí más cabeceras según tu tabla inventarios --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inventarios as $item)
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{ $item->id }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{-- Asumiendo que tu modelo Inventario tiene un campo 'nombre' --}}
                                {{ $item->nombre ?? 'Sin nombre' }}
                            </td>
                            {{-- Agrega aquí más celdas --}}
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center text-gray-500">
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
</div>