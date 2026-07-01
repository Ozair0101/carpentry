@props([
    'target' => 'save',
    'label' => 'ذخیره',
    'busy' => 'در حال ذخیره…',
])

{{-- Submit button that announces itself while the request is in flight. --}}
<button
    type="submit"
    wire:target="{{ $target }}"
    wire:loading.attr="disabled"
    {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded-lg bg-amber-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-amber-700 disabled:cursor-wait disabled:opacity-70']) }}
>
    <span wire:loading.remove wire:target="{{ $target }}">{{ $label }}</span>
    <span wire:loading wire:target="{{ $target }}">{{ $busy }}</span>
</button>
