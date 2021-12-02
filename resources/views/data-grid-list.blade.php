<div class="grid lg:grid-cols-2 xl:grid-cols-3 gap-5">
    @foreach ($data as $item)
        <div id="item{{$item->_id}}" class="group {{ $lean || !$showActions ? '' : 'pb-10' }} relative">
            <div class="h-full py-3 px-4 relative rounded overflow-hidden w-full bg-white shadow border border-gray-200">
                @isset($image)
                    <div class="-mx-1 relative">
                        <img src="{!! eval('?>'.Blade::compileString($image)) !!}" class="h-40 mb-3 w-full object-cover" alt="" />
                    </div>
                @elseif(isset($item->{$imageField}) && $item->{$imageField} != null)
                    <div class="-mx-1 relative">
                        <img src="{{ $item->{$imageField} }}" class="h-40 mb-3 w-full object-cover" alt="" />
                    </div>
                @endisset

                @isset($meta)
                    <small class="block mb-1 opacity-50 text-sm">
                        {!! eval('?>'.Blade::compileString($meta)) !!}
                    </small>
                @elseif(isset($item->{$metaField}) && $item->{$metaField} != null)
                    <small class="block -mt-0.5 mb-1 opacity-50 text-sm">
                        {{ $item->{$metaField} }}
                    </small>
                @endisset

                <h5 class="text-lg font-medium">
                    @isset($title)
                        {!! eval('?>'.Blade::compileString($title)) !!}
                    @elseif(isset($item->{$titleField}) && $item->{$titleField} != null)
                        {{ $item->{$titleField} }}
                    @else
                        ...
                    @endisset
                </h5>

                @isset($description)
                    <p class="opacity-80 font-light">
                        {!! eval('?>'.Blade::compileString($description)) !!}
                    </p>
                @elseif(isset($item->{$descriptionField}) && $item->{$descriptionField} != null)
                    <p class="opacity-80 font-light">
                        {{ mb_strimwidth($item->{$descriptionField}, 0, 100, '...') }}
                    </p>
                @endisset
            </div>

            @if (!$lean && $showActions)
                <div class="absolute hidden group-hover:flex hover:flex items-center mt-2.5">
                    <button type="button" class="focus:outline-none px-3 leading-none py-2 mr-2 border-2 border-black uppercase text-xs tracking-wide font-semibold bg-transparent text-black rounded-full"
                        onclick="deleteEntry('{{$item->_id}}')"
                    >
                        Delete
                    </button>
                    <a href="{{url('/admin/upsertModel/' .$model. '/' . $item->_id)}}" class="focus:outline-none px-6 leading-none py-2 mr-4 border-2 border-black uppercase text-xs tracking-wide font-semibold bg-black text-white rounded-full">
                        Edit
                    </a>
                </div>
            @endif
        </div>
    @endforeach
</div>