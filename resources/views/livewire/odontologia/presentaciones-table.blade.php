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
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($presentaciones as $presentacion)
                        <tr>
                            <td>{{ $presentacion->id_presentacion }}</td>
                            <td>{{ $presentacion->descripcion }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $presentaciones->links() }}
        </div>
    @endif
</div>