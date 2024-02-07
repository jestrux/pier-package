<?php

use Jestrux\Pier\PierMigration;

function pierRow($model, $rowId, $filters = [])
{
    return PierMigration::detail($model, $rowId, $filters);
}

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

function pierModel($model)
{
    $modelDetails = PierMigration::describe($model);

    $modelDetails->mainField = $modelDetails->display_field;
    $modelDetails->fields = collect(json_decode($modelDetails->fields));
    $modelDetails->settings = collect(json_decode($modelDetails->settings));

    return $modelDetails;
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

function pierConfig()
{
    $appLogo = env('APP_LOGO') ?? null;
    if (!is_null($appLogo)) $appLogo = asset($appLogo);

    $uploadUrl = env('PIER_UPLOAD_URL') ?? null;

    if (is_null($uploadUrl)) {
        $uploadDir = env('PIER_UPLOAD_DIR') ?? null;
        if (!is_null($uploadDir) && strlen($uploadDir) > 0) {
            $uploadUrl = url("api/$uploadDir/upload_file");
        }
    }

    return (object) [
        "appLogo" => $appLogo,
        "appName" => env('APP_NAME') ?? "",
        "appColor" => env('APP_COLOR') ?? '#2c5282',
        "unsplashClientId" => env('PIER_UNSPLASH_CLIENT_ID'),
        "fileUploadUrl" => $uploadUrl,
        "s3" => (object) [
            "bucketName" => env('PIER_S3_BUCKET'),
            "region" => env('PIER_S3_REGION'),
            "accessKeyId" => env('PIER_S3_ACCESS_KEY_ID'),
            "secretAccessKey" => env('PIER_S3_SECRET_ACCESS_KEY'),
        ],
        "authUser" => auth()->check() ? auth()->id() : null,
    ];
}
