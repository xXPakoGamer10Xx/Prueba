<div class="modal fade" id="modalAgregarMaterial" tabindex="-1" aria-labelledby="modalAgregarMaterialLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="modalAgregarMaterialLabel">Agregar nuevo material</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form wire:submit.prevent="saveNewMaterial" id="formularioAgregarMaterial" class="mt-3">
                    <input type="hidden" name="formulario" value="agregar">

                    <div class="mb-3">
                        <label for="formularioAgregarMaterialDescripcion" class="form-label">Descripción:</label>
                        <input type="text" class="form-control text-center @error('descripcion') is-invalid @enderror" id="formularioAgregarMaterialDescripcion" wire:model="descripcion" placeholder="ej. Hojas de consentimiento">
                        @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="formularioAgregarMaterialCantidad" class="form-label">Cantidad:</label>
                        <input type="number" class="form-control text-center @error('cantidad') is-invalid @enderror" id="formularioAgregarMaterialCantidad" wire:model="cantidad" min="0" placeholder="0">
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