<?php

namespace Jestrux\Pier\View\Components;

use Illuminate\View\Component;

class Modal extends Component
{
    public function __construct(
        public $id = '',
        public $title = '',
        public $open = false,
        public $placement = 'center',
        public $noPadding = false,
        public $width = '550px',
    ) {
    }

    public function render()
    {
        return view('pier::components.modal');
    }
}
