<?php

namespace Jestrux\Pier\View\Components\Livewire;

use Livewire\Component;

class Form extends Component
{
    public $modelName;
    public $model;
    public $fields;
    public $values = [];
    public $rowId = null;
    public $redirectTo = null;
    public $successMessage = null;
    public $onSave = null;

    public function mount()
    {
        $model = pierModel($this->modelName);
        $this->fields = $model->fields;
        $this->model = $model;
    }

    public function render()
    {
        return view('pier::components.livewire.form.index');
    }
}
