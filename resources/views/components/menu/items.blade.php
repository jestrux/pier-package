<div x-menu:items x-anchor.bottom-end.offset.3="document.getElementById($id('alpine-menu-button'))"
    {{ $attributes->merge([
        'class' =>
            'z-10 bg-card border border-content/10 divide-y divide-content/10 rounded-md shadow-lg py-1 outline-none',
    ]) }}
    x-cloak>
    {{ $slot }}
</div>
