<a x-menu:item
    x-bind:class="{
        'bg-content/5 text-content/90': $menuItem.isActive,
        'text-content/70': !$menuItem.isActive,
        'opacity-50 cursor-not-allowed': $menuItem.isDisabled,
    }"
    class="flex items-center gap-2 w-full px-3 py-1.5 text-left text-sm hover:bg-content/5 disabled:text-content/20 transition-colors"
    {{ $attributes->merge([
        'type' => 'button',
    ]) }}>
    {{ $slot }}
</a>
