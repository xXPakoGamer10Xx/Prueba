@props([
    'title',
    'description',
    'titleClass' => '',
    'descriptionClass' => '',
])

<div class="flex w-full flex-col text-center">
    <flux:heading size="xl" :class="$titleClass">{{ $title }}</flux:heading>
    <flux:subheading :class="$descriptionClass">{{ $description }}</flux:subheading>
</div>
