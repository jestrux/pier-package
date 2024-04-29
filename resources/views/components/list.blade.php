@props([
    'model' => null,
    'image' => 'image',
    'title' => 'title',
    'description' => 'description',
    'sortBy' => null,
    'filters' => null,
])

@if ($image instanceof \Closure || $title instanceof \Closure || $description instanceof \Closure)
    @php
        $res = pierData(model: $model, filters: $filters);
        $data = pierDataToViewData(data: $res['data'], fields: compact('title', 'description', 'image'));
    @endphp

    <livewire:pier-list :$data />
@else
    <livewire:pier-list :$model :imageField="$image" :titleField="$title" :descriptionField="$description" :$sortBy :$filters />
@endif
