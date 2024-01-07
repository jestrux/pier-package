<?php

use Jestrux\Pier\PierData;
use Jestrux\Pier\PierMigration;

function pierData($model, $filters = null)
{
    $res = PierData::browse(
        model: $model,
        filters: $filters
    );

    if ($filters && $filters['page']) return $res;

    return $res['data'];
}

function pierModelWithData($model, $filters = null)
{
    $res = PierData::model(
        model: $model,
        filters: $filters
    );

    $data = $res['data'];

    $model = $res['model'];

    $name = $model->name;
    $mainField = $model->display_field;
    $fields = $model->fields;
    $settings = $model->settings;

    return compact(["data", "model", "name", "mainField", "fields", "settings"]);
}

function pierModelFields($model)
{
    return PierMigration::model_fields($model);
}

function pierField(
    $label,
    $type = 'text',
    $meta = [],
    $required = false,
) {
    return (object) [
        'label' => $label,
        'type' => $type,
        'meta' => (object) ($meta ?? []),
        'required' => $required,
    ];
}
