<?php

namespace Jestrux\Pier\View\Components;

use Illuminate\View\Component;

class ActionButtons extends Component
{
    public function __construct(public $model, public $rowId)
    {
    }

    public function render()
    {
        return view('pier::components.action-buttons');
    }
}
