{{-- @props([
    'model' => null,
    'fields' => null,
    'data' => null,
    'page' => 1,
    'perPage' => 1000,
]) --}}

@php
    if ($data && ($model || $fields || count($data) > 0)) {
        if (!$fields) {
            $fieldNames = array_keys((array) $data[0]);
            $fields = $model ? pierModelFields($model) : collect($fieldNames)->map(fn($label) => pierField($label));
        }
    } else {
        extract(pierModelWithData($model, ['perPage' => $perPage]));
    }
@endphp

<livewire:pier-table :$data :$fields />
{{-- <livewire:pier-datatable :$model :$page :$perPage /> --}}
