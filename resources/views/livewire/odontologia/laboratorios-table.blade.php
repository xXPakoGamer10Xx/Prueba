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
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($laboratorios as $laboratorio)
                        <tr>
                            <td>{{ $laboratorio->id_laboratorio }}</td>
                            <td>{{ $laboratorio->descripcion }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $laboratorios->links() }}
        </div>
    @endif
</div>