<?php

namespace Jestrux\Pier\View\Components;

use Illuminate\View\Component;
use Jestrux\Pier\PierMigration;

class Form extends Component
{
    public $values;
    public function __construct(
        public $model,
        public $rowId = null,
        public $redirectTo = null,
        public $successMessage = null,
        public $onSave = null,
    ) {
        $this->model = PierMigration::describe($model);
        $this->values = $rowId == null ? null : PierMigration::detail($model, $rowId);
    }

    public function render()
    {
        return view('pier::components.form');
    }
}
