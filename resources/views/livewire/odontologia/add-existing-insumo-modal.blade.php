<div class="modal fade" id="modalAgregarInsumo" tabindex="-1" aria-labelledby="modalAgregarInsumoLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="modalAgregarInsumoLabel">Agregar insumo existente</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="addInsumoToConsultorio" id="formularioAgregarExistente">
                    {{-- Mensaje de éxito/error --}}
                    @if ($message)
                        <div class="text-center text-white fw-bold rounded py-2 mb-3
                            @if($messageType == 'success') bg-success @else bg-danger @endif">
                            {{ $message }}
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="select-insumo" class="form-label">Insumo:</label>
                        <select id="select-insumo" wire:model.live="selectedInsumoId" class="form-select @error('selectedInsumoId') is-invalid @enderror" aria-label="Default select example">
                            <option value="" class="text-center">-- Seleccione un insumo --</option>
                            @foreach ($this->insumos as $insumo)
                                <option value="{{ $insumo->id_insumo }}">(ID: {{ $insumo->id_insumo }}) {{ $insumo->descripcion }}</option>
                            @endforeach
                        </select>
                        @error('selectedInsumoId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="form-existente-agregar-cantidad" class="form-label">Cantidad:</label>
                        <input type="number" class="form-control text-center @error('cantidad') is-invalid @enderror" id="form-existente-agregar-cantidad" wire:model="cantidad" min="0" placeholder="0">
                        @error('cantidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="modal-footer d-flex justify-content-between px-0">
                        <button type="button" class="border-0 rounded-2 m-0 py-2 px-3 bg-rojo text-white fw-semibold" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="border-0 rounded-2 m-0 py-2 px-3 bg-cafe text-white fw-semibold">Agregar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>