<?php

namespace Jestrux\Pier\View\Components;

use Illuminate\View\Component;

class ActionButtons extends Component
{
    public $buttonsModel;
    public function __construct(public $rowId, public $model = null)
    {
        $this->buttonsModel = $model;
    }

    public function render()
    {
        return view('pier::components.action-buttons');
    }
}
