<?php

namespace Jestrux\Pier;

use Jestrux\Pier\PierMigration;

class PierData
{
    static public function row(
        $model,
        $rowId,
    ) {
        $res = PierMigration::detail($model, $rowId);

        return [
            'data' => $res,
        ];
    }

    static public function browse(
        $model,
        $filters = [],
    ) {
        $filters = $filters ?? [];
        if (!isset($filters['page'])) $filters['page'] = 1;

        $res = PierMigration::browse($model, $filters);
        $pagination = $res;

        return [
            'data' => $res,
            'pagination' => [
                'perPage' => $pagination['per_page'],
                'page' => $pagination['current_page'],
                'lastPage' => $pagination['last_page'],
                'totalRows' => $pagination['total_rows'],
                'hasMorePages' => $pagination['has_more_pages'],
            ]
        ];
    }

    static public function model(
        $model,
        $filters = [],
    ) {
        $filters = $filters ?? [];
        if (!isset($filters['page'])) $filters['page'] = 1;
        $res = PierMigration::browse_model($model, $filters);

        $model = $res['model'];
        $model->fields = collect(json_decode($model->fields));
        $pagination = $res['data'];
        $data = $res['data']['data'];

        return [
            'data' => $data,
            'model' => $model,
            'pagination' => [
                'perPage' => $pagination['per_page'],
                'page' => $pagination['current_page'],
                'lastPage' => $pagination['last_page'],
                'totalRows' => $pagination['total_rows'],
                'hasMorePages' => $pagination['has_more_pages'],
            ]
        ];
    }
}
