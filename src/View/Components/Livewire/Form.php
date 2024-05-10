<?php

namespace Jestrux\Pier\View\Components\Livewire;

use Livewire\Component;

class Form extends Component
{
    public $modelName;
    public $model;
    public $fields;
    public $values = [];
    public $mostFieldsRequired = true;
    public $rowId = null;
    public $redirectTo = null;
    public $successMessage = null;
    public $onSave = null;
    public $onSuccess = null;

    public function mount()
    {
        if ($this->modelName) {
            $model = pierModel($this->modelName);
            $this->fields = $model->fields;
            $this->model = $model;

            if ($this->rowId) {
                $this->values = $model && $this->rowId ? (array) pierRow($this->modelName, $this->rowId) : [];
            }
        }
    }

    public function submit($data)
    {
        if ($this->rowId)
            return pierUpdateRow($this->modelName, $this->rowId, $data);

        return pierInsertRow($this->modelName, $data);
    }

    public function render()
    {
        return view('pier::components.livewire.form.index');
    }
}
