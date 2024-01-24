@php
    $data = $tableData;

    if ($data && ($model || $fields || count($data) > 0)) {
        if (!$fields) {
            $fieldNames = array_keys((array) $data[0]);
            $fields = $model ? pierModelFields($model) : collect($fieldNames)->map(fn($label) => pierField($label));
        }
    } else {
        extract(pierData($model, ['perPage' => $perPage]));
    }
@endphp

<livewire:pier-table :$data :$fields />
{{-- <livewire:pier-datatable :$model :$page :$perPage /> --}}
