@aware(['model', 'modelData', 'imageField', 'metaField', 'titleField', 'descriptionField', 'sortField' => 'index'])

@php
    if (!is_null($stackFields['imageField'])) {
        $imageField = $stackFields['imageField'];
    }

    if (!is_null($stackFields['metaField'])) {
        $metaField = $stackFields['metaField'];
    }

    if (!is_null($stackFields['titleField'])) {
        $titleField = $stackFields['titleField'];
    }

    if (!is_null($stackFields['descriptionField'])) {
        $descriptionField = $stackFields['descriptionField'];
    }

    if (!is_null($stackModel)) {
        $model = $stackModel;
    }

    $data = !is_null($stackData) ? $stackData : $modelData ?? null;

    if (is_null($data)) {
        return;
    }

    $sortable = count($data) > 0 && isset($data[0]->{$sortField});

    if ($sortable) {
        $sorted = $data->sortBy($sortField);
        $data = $sorted->values()->all();
    }
@endphp

<div id="{{ $instanceId }}" class="flex flex-col gap-2">
    @foreach ($data as $item)
        <div id="item{{ $item->_id }}" data-id="{{ $item->_id }}"
            class="group relative bg-white group p-2 w-full flex items-center gap-3 focus:outline-none border rounded-md shadow-sm">
            @if ($sortable)
                <div style="cursor: -webkit-grabbing"
                    class="stack-handle cursor-move ml-1 flex-shrink-0 sopacity-0 group-hover:opacity-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </div>
            @endif

            @isset($image)
                {!! eval('?>' . Blade::compileString($image)) !!}
            @elseif(isset($item->{$imageField}) && $item->{$imageField} != null)
                <img style="width: 100px; aspect-ratio: 2/1.25; object-fit: cover"
                    class="flex-shrink-0 md:rounded-md inset-0 w-full object-cover"
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

                <div class="absolute right-3 top-3">
                    <x-pier-action-buttons :model="$model ?? null" :row-id="$item->_id" />
                </div>
            </div>
        </div>
    @endforeach
</div>

@if ($sortable)
    <script>
        window.appendSortable().then(() => {
            if (!window.sortableStack) window.sortableStack = {}

            setTimeout(() => {
                window.sortableStack["{{ $instanceId }}"] = new window.Sortable(document.querySelector(
                    "#{{ $instanceId }}"), {
                    handle: '.stack-handle',
                    onSort: e => {
                        window.sortableStack["{{ $instanceId }}"].toArray().forEach((rowId, index) => {
                            window.updatePierRow("{{ $model }}", rowId, {
                                "{{ $sortField }}": index
                            });
                        });
                    }
                });
            }, 500);
        })
    </script>
@endif
