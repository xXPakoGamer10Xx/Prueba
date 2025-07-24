<div class="modal fade" id="modalAgregarPresentacion" tabindex="-1" aria-labelledby="modalAgregarPresentacionLabel" aria-hidden="true">
    <div style="max-width: 18.75rem;" class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
            <h1 class="modal-title fs-5" id="modalAgregarPresentacionLabel">Agregar presentación</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <!-- Formulario -->
            <form id="formularioAgregarPresentacion">
                <div class="my-4">
                <input type="hidden" name="formulario" value="presentacion_agregar">
                <div class="mensaje_agregar_presentacion text-center bg-success text-white fw-bold rounded mb-3 py-2 d-none"></div>
                <div class="mb-3">
                    <label for="presentacion" class="form-label">Descripción</label>
                    <input type="text" class="form-control" id="presentacion" name="presentacion">
                </div>
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