<?php

namespace Jestrux\Pier\View\Components;

use Illuminate\View\Component;

class FormField extends Component
{
    public $instanceId;

    public function __construct(public $field)
    {
        $this->instanceId = "pierFormField" . bin2hex(random_bytes(6));
    }

    public function render()
    {
        return view('pier::components.form-field');
    }
}
