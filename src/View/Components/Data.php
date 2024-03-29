<?php

namespace Jestrux\Pier\View\Components;

use Illuminate\View\Component;
use Jestrux\Pier\PierMigration;

class Data extends Component
{
    public $model;
    public $rowId;
    public $filters;
    public $modelDetails;
    public $modelData;
    public $instanceId;

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
        $rowId = null,
        $filters = [],
        public $orderBy = "",
        public $groupBy = "",
        public $limit = null,
        public $pluck = null,
        public $q = null,
        public $plain = null,
        public $page = null,
        public $perPage = null,
        public $imageField = null,
        public $metaField = null,
        public $titleField = null,
        public $descriptionField = null,
        public $sortField = "index",
        public $metaData = null,
    ) {
        $this->model = $model;
        $this->rowId = $rowId;
        $this->filters = $filters;

        if (count($filters) > 0)
            $this->filters = $this->modifiedFilters(collect($filters));

        $params = $this->filtersWithAnd($this->filters);
        $params["orderBy"] = $this->orderBy;
        $params["groupBy"] = $this->groupBy;

        if ($page != null) $params["page"] = $page;

        if ($perPage != null) $params["perPage"] = $perPage;

        if ($limit != null) $params["limit"] = $limit;

        if ($pluck != null) $params["pluck"] = $pluck;

        if ($q != null) $params["q"] = $q;

        $modelDetails = PierMigration::describe($this->model);
        $modelDetails->fields = collect(json_decode($modelDetails->fields));
        $modelDetails->settings = collect(json_decode($modelDetails->settings));
        $this->modelDetails = $modelDetails;

        if ($rowId == null)
            $this->modelData = PierMigration::browse($this->model, $params);
        else
            $this->modelData = PierMigration::detail($this->model, $rowId, $params);

        // dd($params);
        $bytes = random_bytes(6);
        $this->instanceId = bin2hex($bytes);
    }

    public function render()
    {
        return view('pier::components.data');
    }
}
