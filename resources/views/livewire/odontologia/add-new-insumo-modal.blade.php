<div class="modal fade" id="modalAgregarNuevoInsumo" tabindex="-1" aria-labelledby="modalAgregarNuevoInsumoLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="modalAgregarNuevoInsumoLabel">Agregar nuevo insumo</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form wire:submit.prevent="saveNewInsumo" id="formularioNuevoInsumo" class="mt-3">
                    <input type="hidden" name="formulario" value="consultorio">

                    <div class="row">
                        <div class="mb-3 col-6">
                            <label for="clave" class="form-label">Clave</label>
                            <input type="text" class="form-control form-nuevo-input @error('clave') is-invalid @enderror" id="clave" wire:model="clave">
                            @error('clave') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-6">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <input type="text" class="form-control form-nuevo-input @error('descripcion') is-invalid @enderror" id="descripcion" wire:model="descripcion">
                            @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3 col-6">
                            <label for="id_laboratorio" class="form-label">Laboratorio</label>
                            <div class="d-flex flex-column">
                                <select id="id_laboratorio" wire:model.live="id_laboratorio" class="form-select form-nuevo-select @error('id_laboratorio') is-invalid @enderror" aria-label="Default select example">
                                    <option value="">-- Seleccione --</option>
                                    @foreach ($laboratorios as $laboratorio)
                                        <option value="{{ $laboratorio->id_laboratorio }}">{{ $laboratorio->descripcion }}</option>
                                    @endforeach
                                </select>
                                @error('id_laboratorio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-6">
                            <label for="contenido" class="form-label">Contenido</label>
                            <input type="text" class="form-control form-nuevo-input @error('contenido') is-invalid @enderror" id="contenido" wire:model="contenido">
                            @error('contenido') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3 col-6">
                            <label for="id_presentacion" class="form-label">Presentación</label>
                            <div class="d-flex flex-column">
                                <select id="id_presentacion" wire:model.live="id_presentacion" class="form-select form-nuevo-select @error('id_presentacion') is-invalid @enderror" aria-label="Default select example">
                                    <option value="">-- Seleccione --</option>
                                    @foreach ($presentaciones as $presentacion)
                                        <option value="{{ $presentacion->id_presentacion }}">{{ $presentacion->descripcion }}</option>
                                    @endforeach
                                </select>
                                @error('id_presentacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-6">
                            <label for="caducidad" class="form-label">Caducidad</label>
                            <input type="date" class="form-control form-nuevo-input @error('caducidad') is-invalid @enderror" id="caducidad" wire:model="caducidad">
                            <div id="caducidadHelp" class="form-text">
                                Si se desconoce o no tiene caducidad, dejar casilla en valor predeterminado (dd/mm/aaaa)
                            </div>
                            @error('caducidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        @if($formulario === 'consultorio' || $formulario === 'almacen')
                            <div class="mb-3 col">
                                <label for="cantidad" class="form-label">Cantidad</label>
                                <input type="number" class="form-control text-center form-nuevo-input @error('cantidad') is-invalid @enderror" wire:model="cantidad" id="cantidad" min="0" max="1000" placeholder="0">
                                @error('cantidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        @endif
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