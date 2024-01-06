<?php

namespace Jestrux\Pier\View\Components\Livewire;

use Livewire\Component;
use Jestrux\Pier\PierData;

class Table extends Component
{
    public $modelDetails;
    public $model;
    public $q = "";
    public $page = 1;
    public $perPage;
    public $fields;
    public $rows;
    public $pagination;

    public $centeredFields = [
        // "image",
        // "phone",
        // "email",
        // "video",
        "rating",
        "boolean",
        // "date"
    ];

    public function mount()
    {
        if (!$this->model) return;

        $res = PierData::model(
            model: $this->model,
            filters: [
                'q' => $this->q,
                'page' => $this->page,
                'perPage' => $this->perPage,
            ]
        );

        $this->rows = $res['data'];
        $this->pagination = $res['pagination'];
        $this->modelDetails = $res['model'];
        $this->fields = $res['model']['fields'];
    }

    public function updated()
    {
        if (!$this->model) return;

        $res = PierData::browse(
            model: $this->model,
            filters: [
                "q" => $this->q,
                "page" => $this->page,
                "perPage" => $this->perPage,
            ]
        );

        $this->rows = $res['data'];
        $this->pagination = $res['pagination'];
    }

    public function render()
    {
        return view('pier::components.livewire.table.index');
    }
}
