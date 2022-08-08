<div class="relative">
    <div class="h-full py-3 px-4 relative rounded overflow-hidden w-full bg-white shadow border border-gray-200">
        @if (isset($item->{$imageField}) && $item->{$imageField} != null)
            <div class="-mx-1 relative">
                <img src="{{ $item->{$imageField} }}" class="h-40 mb-3 w-full object-cover object-top" alt="" />
            </div>
        @endif

        @if (isset($metaField) && isset($item->{$metaField}) && $item->{$metaField} != null)
            <small class="block -mt-0.5 mb-1 opacity-50 text-sm">
                {{ $item->{$metaField} }}
            </small>
        @endif

        <h5 class="text-lg font-medium">
            @if (isset($item->{$titleField}) && $item->{$titleField} != null)
                {{ $item->{$titleField} }}
            @else
                ...
            @endif
        </h5>

        @if (isset($item->{$descriptionField}) && $item->{$descriptionField} != null)
            <p class="opacity-80 font-light">
                {{ mb_strimwidth($item->{$descriptionField}, 0, 100, '...') }}
            </p>
        @endif
    </div>
</div>
