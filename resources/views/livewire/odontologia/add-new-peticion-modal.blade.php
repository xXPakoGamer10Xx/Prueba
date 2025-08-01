<div class="modal fade" id="modalPedir" tabindex="-1" aria-labelledby="modalPedirLabel" aria-hidden="true" wire:ignore.self>
   <div style="max-width: 18.75rem;" class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
      	<div class="modal-header">
         	<h1 class="modal-title fs-5" id="modalPedirLabel">Pedir insumo</h1>
         	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <form wire:submit.prevent="savePeticion" id="formularioPedirInsumo">
					<div class="my-4">
						<input type="hidden" name="id_insumo_almacen_fk" wire:model="id_insumo_almacen_fk">
						<div class="mb-3">
							<label for="cantidad_solicitada" class="form-label">Ingrese cuanto desea pedir:</label>
							<input type="number" class="form-control text-center @error('cantidad_solicitada') is-invalid @enderror" id="cantidad_solicitada" wire:model="cantidad_solicitada" placeholder="0" min="1">
                            @error('cantidad_solicitada') <div class="invalid-feedback">{{ $message }}</div> @enderror
						</div>
					</div>

					<div class="modal-footer d-flex justify-content-between px-0">
						<button type="button" class="border-0 rounded-2 m-0 py-2 px-3 bg-rojo text-white fw-semibold" data-bs-dismiss="modal">Cancelar</button>
						<button style="width: 5.8819rem;" type="submit" class="border-0 rounded-2 m-0 py-2 px-3 bg-cafe text-white fw-semibold">Pedir</button>
					</div>
         	</form>
      	</div>
	   </div>
   </div>
</div>