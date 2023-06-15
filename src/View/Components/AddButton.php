<?php

namespace Jestrux\Pier\View\Components;

use Illuminate\View\Component;

class AddButton extends Component
{
    public $buttonModel;
    public function __construct($model = null)
    {
        $this->buttonModel = $model;
    }

    public function render()
    {
        return view('pier::components.add-button');
    }
}
