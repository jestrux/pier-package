<?php

use Jestrux\Pier\PierMigration;

function pierData($model, $filters = null)
{
    $filters = $filters ?? [];
    if (!isset($filters['page'])) $filters['page'] = 1;
    $res = PierMigration::browse_model($model, $filters);

    $model = $res['model'];
    $model->mainField = $model->display_field;
    $model->fields = collect(json_decode($model->fields));
    $model->settings = collect(json_decode($model->settings));
    $pagination = $res['data'];
    $data = $res['data']['data'];

    return [
        'data' => $data,
        'model' => $model,
        'fields' => $model->fields,
        'pagination' => (object) [
            'perPage' => $pagination['per_page'],
            'page' => $pagination['current_page'],
            'lastPage' => $pagination['last_page'],
            'totalRows' => $pagination['total_rows'],
            'hasMorePages' => $pagination['has_more_pages'],
        ]
    ];
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
