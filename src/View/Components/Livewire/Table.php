<?php

namespace Jestrux\Pier\View\Components\Livewire;

use Livewire\Component;
use Livewire\Attributes\Reactive;

class Table extends Component
{
    #[Reactive]
    public $fields;

    #[Reactive]
    public $data;

    public $centeredFields = [
        "image",
        // "phone",
        // "email",
        // "video",
        "rating",
        "boolean",
        // "date"
    ];

    public function render()
    {
        return view('pier::components.livewire.table.index');
    }
}
