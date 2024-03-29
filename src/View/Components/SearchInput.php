<?php

namespace Jestrux\Pier\View\Components;

use Illuminate\View\Component;

class SearchInput extends Component
{
    public $debounce;

    public function __construct($debounce = 300)
    {
        $this->debounce = $debounce;
    }

    public function render()
    {
        return view('pier::components.search-input');
    }
}
