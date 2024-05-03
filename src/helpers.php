<?php

use Jestrux\Pier\PierMigration;

function pierRandomId()
{
    return bin2hex(random_bytes(6));
}

function pierInsertRow($model, $data = [])
{
    return PierMigration::insertRow($model, $data);
}

function pierTruncateModel($model)
{
    return PierMigration::truncate($model);
}

function pierBulkInsert($model, $data = [])
{
    return collect($data)->each(fn ($row) => pierInsertRow($model, $row));
}

function pierUpdateRow($model, $rowId, $data = [])
{
    return PierMigration::updateRow($model, $rowId, $data);
}

function pierDataToViewData($data, $fields)
{
    return collect($data)->map(function ($row) use ($fields) {
        $entry = (array) $row;

        foreach ($fields as $key => $value) {
            if ($value instanceof \Closure)
                $entry[$key] = $value($row);
            else if (in_array($value, ["null", null]))
                $entry[$key] = null;
            else if ($row->{$value} ?? null != null)
                $entry[$key] = $row->{$value};
        }

        return (object) $entry;
    });
}

function pierLocationImage($location, $width = 500, $height = 500)
{
    if (!env('PIER_MAPQUEST_API_KEY')) return null;

    $url = 'https://www.mapquestapi.com/staticmap/v5/map?zoom=8&marker-7B0099';
    $size = "&size=" . collect([$width, $height])->join(",");
    $key = '&key=' . env('PIER_MAPQUEST_API_KEY');
    $center = '&center=' . implode(',', array_reverse($location->coords));

    return $url . $size . $key . $center;
}

function pierRow($model, $rowId, $filters = [])
{
    return PierMigration::detail($model, $rowId, $filters);
}

function pierData($model, $filters = null, $paginated = false)
{
    $filters = $filters ?? [];
    if (!$paginated && !isset($filters['page']) && !isset($filters['per_page'])) {
        $data = PierMigration::browse($model, $filters);
        $modelRes = PierMigration::describe($model);

        $modelRes->mainField = $modelRes->display_field;
        $modelRes->fields = collect(json_decode($modelRes->fields));
        $modelRes->settings = collect(json_decode($modelRes->settings));

        return [
            "data" => $data,
            "model" => $modelRes,
            "pagination" => (object) [
                "page" => 1
            ]
        ];
    }

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
    $field = []
) {
    $props = [
        'type' => 'text',
        'required' => false,
        ...$field
    ];

    $props['meta'] = (object) ($props['meta'] ?? []);

    return (object) $props;
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
