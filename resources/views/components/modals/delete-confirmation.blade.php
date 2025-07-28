@props([
    'modalId' => 'modalEliminarRegistro', // ID por defecto si no se especifica
    'formId' => 'formularioEliminarRegistro', // ID por defecto del formulario
    'wireModel' => 'itemToDeleteId', // Propiedad Livewire para el ID del elemento
    'confirmAction' => 'deleteItem', // Método Livewire a llamar al confirmar
    'iconClass' => 'fa-solid fa-circle-exclamation', // Clase del icono (por defecto de exclamación)
    'iconSize' => '4rem', // Tamaño del icono
    'width' => '18.75rem', // Ancho del modal
    'titleId' => 'modalEliminarRegistroLabel', // ID del título del modal
])

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $titleId }}" aria-hidden="true">
    <div style="width: {{ $width }};" class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="{{ $confirmAction }}" id="{{ $formId }}">
                    <input type="hidden" name="formulario" value="eliminar">
                    <input type="hidden" wire:model="{{ $wireModel }}">
                    <div class="mb-3 d-flex flex-column align-items-center gap-4">
                        <i style="font-size: {{ $iconSize }};" class="{{ $iconClass }}"></i>
                        <h2 class="text-center fs-5 fw-normal" id="{{ $titleId }}">¿Está seguro que desea eliminar este registro?</h2>
                    </div>

                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="border-0 rounded-2 py-2 px-3 bg-rojo text-white fw-semibold" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="border-0 rounded-2 py-2 px-3 bg-cafe text-white fw-semibold">Confirmar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>