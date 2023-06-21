<?php

namespace Jestrux\Pier\View\Components;

use Illuminate\View\Component;

class FormField extends Component
{
    public $instanceId;

    public function __construct(public $type = "text", public $label = null, public $name = null, public $required = null, public $min = null, public $max = null, public $meta = [], public $value = "", public $onChange = "", public $model = null, public $rowId = null)
    {
        $this->instanceId = "pierFormField" . bin2hex(random_bytes(6));
    }

    public function render()
    {
        return view('pier::components.form-field');
    }
}
