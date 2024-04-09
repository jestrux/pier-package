@props([
    'model' => null,
    'rowId' => null,
    'fields' => [],
    'values' => [],
    'successMessage' => null,
    'onSave' => null,
    'onSuccess' => null,
])

<livewire:pier-form :model-name="$model" :$rowId :$fields :$values :$successMessage :$onSave :$onSuccess />
