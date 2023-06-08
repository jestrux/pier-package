<?php

namespace Jestrux\Pier\View\Components;

use Illuminate\View\Component;
use Jestrux\Pier\PierMigration;

class Form extends Component
{
    public $model;
    public $rowId;
    public $redirectTo;
    public $successMessage;
    public $values;

    public function __construct($model, $rowId = null)
    {
        $this->rowId = $rowId;
        $this->model = PierMigration::describe($model);
        $this->values = $rowId == null ? null : PierMigration::detail($model, $rowId);
    }

    public function render()
    {
        return view('pier::components.form', [
            "model" => $this->model,
            "values" => $this->values,
            "redirectTo" => $this->redirectTo ?? null,
            "successMessage" => $this->successMessage ?? null,
        ]);
    }
}
