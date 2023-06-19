@aware(['model', 'modelData'])

@php
    if (!is_null($stackModel)) {
        $model = $stackModel;
    }

    $data = !is_null($stackData) ? $stackData : $modelData ?? null;

    if (is_null($data)) {
        return;
    }
@endphp

<div id="{{ $instanceId }}" class="flex flex-col gap-2">
    @foreach ($data as $item)
        <div id="item{{ $item->_id }}" data-id="{{ $item->_id }}" style="cursor: -webkit-grabbing"
            class="bg-white group p-2 w-full flex items-center gap-4 focus:outline-none border rounded-md shadow-sm cursor-move">
            @isset($image)
                {!! eval('?>' . Blade::compileString($image)) !!}
            @elseif(isset($item->{$imageField}) && $item->{$imageField} != null)
                <img style="width: 100px; aspect-ratio: 2/1.25; object-fit: cover"
                    class="flex-shrink-0 md:rounded-md bg-gray-500 inset-0 w-full object-cover"
                    src="{{ $item->{$imageField} }}" />
            @endisset

            <div class="px-2 py-2 flex flex-col gap-1">
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
            </div>

            <div class="ml-auto mr-2 opacity-0 group-hover:opacity-100">
                <span class="my-handle">::</span>
            </div>
        </div>
    @endforeach
</div>


<script>
    window.appendSortable().then(() => {
        if (!window.sortableStack) window.sortableStack = {}

        window.sortableStack["{{ $instanceId }}"] = new Sortable(document.querySelector(
            "#{{ $instanceId }}"), {
            onSort: e => {
                window.sortableStack["{{ $instanceId }}"].toArray().forEach((rowId, index) => {
                    window.updatePierRow("{{ $model }}", rowId, {
                        "{{ $sortField }}": index
                    });
                });
            }
        });
    })
</script>
