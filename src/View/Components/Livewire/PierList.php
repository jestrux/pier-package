<?php

namespace Jestrux\Pier\View\Components\Livewire;

use Livewire\Component;

class PierList extends Component
{
    public $instanceId;

    public $model;
    public $filters;
    public $data;
    public $fields;

    public $image = "image";
    public $title = "title";
    public $description = "description";
    public $sortBy;

    protected function mapData($data)
    {
        $this->data = pierDataToViewData(data: $data, fields: ['title' => $this->title, 'description' => $this->description, 'image' => $this->image]);
    }

    protected function fetchData()
    {
        $res = pierData(
            model: $this->model,
            filters: $this->filters,
        );

        $data = $res['data'];
        $this->fields = $res['model']->fields;

        $sortable = $this->sortBy ?? null != null && count($data) > 0 && isset($data[0]->{$this->sortBy});

        if (!$sortable) return $this->mapData($data);

        [$field, $direction] = array_merge(explode(",", $this->sortBy), ["asc"]);

        $this->mapData(($direction == "asc" ? $data->sortBy($field) : $data->sortByDesc($field))->values()->all());
    }

    public function mount()
    {
        $this->instanceId = bin2hex(random_bytes(6));

        if (!$this->data) {
            $this->fetchData();
        }
    }

    public function updated()
    {
        $this->fetchData();
    }

    public function render()
    {
        return view('pier::components.livewire.pier-list');
    }
}
