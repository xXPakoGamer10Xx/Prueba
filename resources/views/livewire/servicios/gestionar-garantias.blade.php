<div>
    <div class="mb-12">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Gestión de Garantías</h2>
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            ID
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Descripción
                        </th>
                        {{-- Agrega más cabeceras si es necesario --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse ($garantias as $garantia)
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{ $garantia->id }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{-- Asumiendo que tu modelo Garantia tiene 'descripcion' --}}
                                {{ $garantia->descripcion ?? 'N/A' }}
                            </td>
                        </tr>
                    @empty
                         <tr>
                            <td colspan="2" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center text-gray-500">
                                No hay garantías registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $garantias->links() }}
        </div>
    </div>
</div>