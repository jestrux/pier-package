<?php

namespace Jestrux\Pier\View\Components;

use Illuminate\View\Component;
use Jestrux\Pier\PierMigration;

class DataGrid extends Component
{
    public $model;
    public $noCss;
    public $template;
    public $redirectTo;
    public $imageField;
    public $metaField;
    public $titleField;
    public $descriptionField;
    public $filters;
    public $data;
    public $instanceId;
    public $lean;
    public $showActions;
    public $showAddButton = true;
    public $showSearch;
    public $rowActions;
    public $onEdit;

    function modifiedFilters($filters)
    {
        return $filters->keys()->reduce(function ($agg, $key) use ($filters) {
            $agg['where' . $key] = $filters[$key];
            return $agg;
        }, []);
    }

    function filtersWithAnd($filters)
    {
        $newFilters = [];
        $keys = collect($filters)->keys();
        for ($i = 0; $i < count($keys); $i++) {
            $key = $keys[$i];
            $shortKey = $i == 0 ? $key :  str_replace("where", "andWhere", $key);

            $newFilters["andWhere$shortKey"] = $filters[$key];
        }

        return $newFilters;
    }

    public function __construct(
        $model,
        $noCss = false,
        $template = "default",
        $redirectTo = null,
        $filters = [],
        $imageField = "image",
        $metaField = null,
        $titleField = "title",
        $descriptionField = "description",
        $lean = false,
        $showActions = true,
        $showSearch = true,
        $rowActions = null,
        $onEdit = null,
    ) {
        $this->rowActions = $rowActions;
        $this->onEdit = $onEdit;
        $this->model = $model;
        $this->noCss = $noCss;
        $this->template = $template;
        $this->redirectTo = $redirectTo;
        $this->imageField = $imageField;
        $this->metaField = $metaField;
        $this->titleField = $titleField;
        $this->descriptionField = $descriptionField;
        $this->lean = filter_var($lean, FILTER_VALIDATE_BOOLEAN);
        $this->showActions = filter_var($showActions, FILTER_VALIDATE_BOOLEAN);
        $this->showSearch = filter_var($showSearch, FILTER_VALIDATE_BOOLEAN);
        $this->filters = $filters;

        if (count($filters) > 0)
            $this->filters = $this->modifiedFilters(collect($filters));

        $this->data = PierMigration::browse($this->model, $this->filtersWithAnd($this->filters));

        $bytes = random_bytes(6);
        $this->instanceId = bin2hex($bytes);
    }

    public function render()
    {
        return view('pier::components.data-grid', [
            "data" => $this->data
        ]);
    }
}
