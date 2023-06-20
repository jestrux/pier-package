@aware(['model'])

@php
    $lean = $lean ?? false;
    $showActions = $showActions ?? true;
@endphp

<div class="grid grid-cols-2 lg:grid-cols-2 xl:grid-cols-3" style="gap: {{ $gap ?? '12px' }}">
    @foreach ($data as $item)
        <div id="item{{ $item->_id }}" class="group relative p-0.5">
            <div class="h-full py-1 px-1 relative rounded w-full bg-white shadow border border-gray-200">
                @isset($image)
                    <div class="relative rounded-t-sm overflow-hidden">
                        {!! eval('?>' . Blade::compileString($image)) !!}
                    </div>
                @elseif(isset($item->{$imageField}))
                    @php
                        $src = $item->{$imageField};

                        if ((is_null($src) || strlen($src) < 1) && !is_null($imageFallback ?? null)) {
                            $src = $imageFallback;
                        }
                    @endphp

                    @if (!is_null($src))
                        <div class="-mx-1 relative rounded-t-sm overflow-hidden">
                            <img src="{{ $src }}" class="h-40 w-full object-cover" alt="" />
                        </div>
                    @endif
                @endisset

                <div class="px-2 py-2 flex flex-col gap-1">
                    @isset($meta)
                        <small class="block mb-1 opacity-50 text-xs">
                            {!! eval('?>' . Blade::compileString($meta)) !!}
                        </small>
                    @elseif(isset($metaField) && isset($item->{$metaField}) && $item->{$metaField} != null)
                        <small class="block -mt-0.5 mb-1 opacity-50 text-xs">
                            {{ $item->{$metaField} }}
                        </small>
                    @endisset

                    @isset($title)
                        <h5 class="text-sm font-medium">
                            {!! eval('?>' . Blade::compileString($title)) !!}
                        </h5>
                    @elseif(isset($item->{$titleField}) && $item->{$titleField} != null)
                        <h5 class="text-sm font-medium">
                            {{ $item->{$titleField} }}
                        </h5>
                    @elseif(!is_null($titleField))
                        <h5 class="text-sm font-medium">
                            &nbsp;
                        </h5>
                    @endisset

                    @isset($description)
                        <p class="text-xs leading-relaxed opacity-80 font-light">
                            {!! eval('?>' . Blade::compileString($description)) !!}
                        </p>
                    @elseif(isset($item->{$descriptionField}) && $item->{$descriptionField} != null)
                        <p class="text-xs leading-relaxed opacity-80 font-light">
                            {{ mb_strimwidth(strip_tags($item->{$descriptionField}), 0, 100, '...') }}
                        </p>
                    @endisset
                </div>
            </div>

            @if (!$lean && $showActions)
                <div class="absolute right-3 top-3">
                    <x-pier-action-buttons :model="$model ?? null" :row-id="$item->_id" />
                </div>
            @endif
        </div>
    @endforeach
</div>
