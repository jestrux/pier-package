<?php

namespace Jestrux\Pier\View\Components\Livewire;

use Livewire\Component;
use Jestrux\Pier\PierMigration;

class Table extends Component
{
    public $model;
    public $q = "";
    public $page = 1;
    public $perPage = 25;
    public $fields;
    public $rows;

    public function mount()
    {
        $res = PierMigration::browse_model($this->model, [
            'q' => $this->q,
            'page' => $this->page,
            'perPage' => $this->perPage,
        ]);
        $model = $res['model'];
        $data = $res['data'];

        $centeredFields = [
            "image",
            "phone",
            "email",
            "video",
            "rating",
            "boolean",
            "date"
        ];

        $this->fields = collect(json_decode($model->fields))->map(function ($field) use ($centeredFields) {
            $fieldType = $field->type;

            if ($fieldType == 'reference' && $field->meta?->type ?? null) {
                $fieldType = $field->meta?->type;
            }

            $field->centered = in_array($fieldType, $centeredFields);
            return $field;
        });

        $this->rows = $data['data'];
    }

    public function updated()
    {
        $res = PierMigration::browse($this->model, [
            'q' => $this->q,
            'page' => $this->page,
            'perPage' => $this->perPage,
        ]);

        $this->rows = $res['data'];
    }

    public function render()
    {
        return view('pier::components.livewire.table.index');
    }
}
