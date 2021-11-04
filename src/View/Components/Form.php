<?php

namespace Jestrux\Pier\View\Components;

use Illuminate\View\Component;
use Jestrux\Pier\PierMigration;

class Form extends Component
{
    public $model;
    public $rowId;
    public $redirectTo;
    public $values;

    public function __construct($model, $rowId = null, $redirectTo = null)
    {
        $this->model = PierMigration::describe($model);
        if($rowId != null)
            $this->values = PierMigration::detail($model, $rowId);
        else
            $this->values = null;

        $this->redirectTo = $redirectTo;
    }

    public function render()
    {
        return view('pier::components.form', [
            "model" => $this->model, 
            "values" => $this->values,
            "redirectTo" => $this->redirectTo
        ]);
    }
}
