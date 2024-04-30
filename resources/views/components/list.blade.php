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
        $fields = $res['model']->fields;
        $data = pierDataToViewData(data: $res['data'], fields: compact('title', 'description', 'image'));

        $imageField = !$image || $image instanceof \Closure ? null : $image;
    @endphp

    <livewire:pier-list :$model :$data :$fields :image="$imageField" />
@else
    <livewire:pier-list :$model :$image :$title :$description :$sortBy :$filters />
@endif
