<?php

namespace Jestrux\Pier\View\Components;

use Illuminate\View\Component;

class Modal extends Component
{
    public function __construct(
        public $id = null,
        public $title = '',
        public $open = false,
        public $placement = 'center',
        public $noPadding = false,
        public $width = '550px',
    ) {
        if (is_null($this->id)) $this->id = "pierModal" . bin2hex(random_bytes(6));
    }

    public function render()
    {
        return view('pier::components.modal');
    }
}
