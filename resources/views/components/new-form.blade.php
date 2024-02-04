@props(['model' => null, 'fields' => [], 'rowId' => null])

@php
    $values = $model && $rowId ? (array) pierRow($model, $rowId, ['flat' => true]) : [];
    // $fields = [pierField('name'), pierField('phone'), pierField(label: 'image', type: 'image', meta: ['face' => true])];
@endphp

<livewire:pier-form :model-name="$model" :$values :$fields />
