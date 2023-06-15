@php
    $lean = $lean ?? false;
    $showActions = $showActions ?? true;
@endphp

<div class="grid grid-cols-2 lg:grid-cols-2 xl:grid-cols-3" style="gap: {{ $gap ?? '20px' }}">
    @foreach ($data as $item)
        <div id="item{{ $item->_id }}" class="group relative">
            <div class="h-full py-3 px-4 relative rounded w-full bg-white shadow border border-gray-200">
                @isset($image)
                    <div class="-mx-1 relative h-40 mb-3 w-full overflow-hidden">
                        {!! eval('?>' . Blade::compileString($image)) !!}
                    </div>
                @elseif(isset($item->{$imageField}) && $item->{$imageField} != null)
                    <div class="-mx-1 relative">
                        <img src="{{ $item->{$imageField} }}" class="h-40 mb-3 w-full object-cover" alt="" />
                    </div>
                @endisset

                @isset($meta)
                    <small class="block mb-1 opacity-50 text-sm">
                        {!! eval('?>' . Blade::compileString($meta)) !!}
                    </small>
                @elseif(isset($metaField) && isset($item->{$metaField}) && $item->{$metaField} != null)
                    <small class="block -mt-0.5 mb-1 opacity-50 text-sm">
                        {{ $item->{$metaField} }}
                    </small>
                @endisset

                @isset($title)
                    <h5 class="text-lg font-medium">
                        {!! eval('?>' . Blade::compileString($title)) !!}
                    </h5>
                @elseif(isset($item->{$titleField}) && $item->{$titleField} != null)
                    <h5 class="text-lg font-medium">
                        {{ $item->{$titleField} }}
                    </h5>
                @elseif(!is_null($titleField))
                    <h5 class="text-lg font-medium">
                        &nbsp;
                    </h5>
                @endisset

                @isset($description)
                    <p class="opacity-80 font-light">
                        {!! eval('?>' . Blade::compileString($description)) !!}
                    </p>
                @elseif(isset($item->{$descriptionField}) && $item->{$descriptionField} != null)
                    <p class="opacity-80 font-light">
                        {{ mb_strimwidth(strip_tags($item->{$descriptionField}), 0, 100, '...') }}
                    </p>
                @endisset
            </div>

            @if (!$lean && $showActions)
                <div class="absolute right-3 top-3">
                    <x-pier-action-buttons :model="$model" :row-id="$item->_id" />
                </div>
            @endif
        </div>
    @endforeach
</div>
