@props([
    'modalId' => 'modalEliminarRegistro',
    'formId' => 'formularioEliminarRegistro',
    'wireModel' => 'itemToDeleteId',
    'confirmAction' => 'deleteItem',
    'iconClass' => 'fa-solid fa-circle-exclamation',
    'iconSize' => '4rem',
    'width' => '18.75rem',
    'titleId' => 'modalEliminarRegistroLabel',
    'title' => '',
    'message' => '',
])

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true" wire:ignore.self>
    <div style="width: {{ $width }};" class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold">{{ $title }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="{{ $confirmAction }}" id="{{ $formId }}">
                    <input type="hidden" name="formulario" value="eliminar">
                    <input type="hidden" wire:model="{{ $wireModel }}">

                    {{-- Condicional para mostrar el campo de cantidad si la acción es confirmar pedido --}}
                    @if ($confirmAction === 'confirmPedido')
                        <div class="mb-3">
                            <label for="cantidad" class="form-label">Cantidad Autorizada</label>
                            <input 
                                type="number" 
                                min="0" 
                                placeholder="0" 
                                class="text-center form-control form-nuevo-input @error('cantidad') is-invalid @enderror" 
                                id="cantidad" 
                                wire:model="cantidad"
                            >
                            {{-- 
                            @error('cantidad') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @else
                                @if ($message) 
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @endif
                            @enderror
                             --}}
                        </div>
                    @else
                        <div class="mb-3 d-flex flex-column align-items-center gap-4">
                            <i style="font-size: {{ $iconSize }};" class="{{ $iconClass }}"></i>
                            <h2 class="text-center fs-5 fw-normal" id="{{ $titleId }}">{{ $message }}</h2>
                        </div>
                    @endif

                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="border-0 rounded-2 py-2 px-3 bg-rojo text-white fw-semibold" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="border-0 rounded-2 py-2 px-3 bg-cafe text-white fw-semibold">Confirmar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>