<?php

namespace Jestrux\Pier\View\Components\Livewire;

use Livewire\Component;

class DataTable extends Component
{
    public $modelDetails;

    public $model;

    public $q = "";

    public $filters = [];

    public $fields;

    public $data;

    public $page = 1;

    public $perPage;

    public $pagination = [];

    public function mount()
    {
        $res = pierData(
            model: $this->model,
            filters: [
                'q' => $this->q,
                ...$this->pagination,
                'page' => $this->page,
                'perPage' => $this->perPage,
            ]
        );

        $this->data = $res['data'];
        $this->pagination = $res['pagination'];
        $this->modelDetails = $res['model'];
        $this->fields = $res['fields'];
    }

    public function updated()
    {
        $res = pierData(
            model: $this->model,
            filters: [
                "q" => $this->q,
                "page" => $this->page,
                "perPage" => $this->perPage,
            ]
        );

        $this->data = $res['data'];
        $this->pagination = $res['pagination'];
    }

    public function render()
    {
        return view('pier::components.livewire.data-table');
    }
}
