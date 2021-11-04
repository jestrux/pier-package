<?php

namespace Jestrux\Pier\View\Components;

use Illuminate\View\Component;
use Jestrux\Pier\PierMigration;

class Data extends Component
{
    public $model;
    public $rowId;
    public $filters;
    public $data;
    public $instanceId;

    function modifiedFilters($filters){
        return $filters->keys()->reduce(function($agg, $key) use($filters){
            $agg['where'.$key] = $filters[$key];
            return $agg;
        }, []);
    }

    function filtersWithAnd($filters){
        $newFilters = [];
        $keys = collect($filters)->keys();
        for ($i=0; $i < count($keys); $i++) { 
            $key = $keys[$i];
            $shortKey = $i == 0 ? $key :  str_replace("where", "andWhere", $key);
            
            $newFilters["andWhere$shortKey"] = $filters[$key];
        }
        
        return $newFilters;
    }

    public function __construct($model, $rowId = null, $filters = [])
    {
        $this->model = $model;
        $this->filters = $filters;

        if(count($filters) > 0)
            $this->filters = $this->modifiedFilters(collect($filters));

        if($rowId == null)
            $this->data = PierMigration::browse($this->model, $this->filtersWithAnd($this->filters));
        else
            $this->data = PierMigration::detail($this->model, $rowId, $this->filtersWithAnd($this->filters));

        $bytes = random_bytes(6);
        $this->instanceId = bin2hex($bytes);
    }

    public function render()
    {
        return view('pier::components.data', [
            "data" => $this->data,
            "instanceId" => $this->instanceId,
        ]);
    }
}
