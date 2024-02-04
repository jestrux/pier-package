<div x-data x-popover {{ $attributes->merge([
    'class' => 'relative',
]) }}>
    {{ $slot }}
</div>
