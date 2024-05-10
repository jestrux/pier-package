@props([
    'model' => null,
    'rowId' => null,
    'fields' => [],
    'values' => [],
    'successMessage' => null,
    'mostFieldsRequired',
    'onSave' => null,
    'onSuccess' => null,
])

@php
    $mostFieldsRequired = $mostFieldsRequired ?? true;
@endphp

<livewire:pier-form :model-name="$model" :$rowId :$fields :$values :$successMessage :$onSave :$onSuccess
    :$mostFieldsRequired />
