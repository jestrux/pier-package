<input
    {{ $attributes->merge(['type' => 'search']) }}
    :pier-ref="ref"
    :value="filters.q"
    @input.debounce.{{$debounce}}ms="e => filters = {...filters, q: e.target.value}"
/>