<?php

namespace Jestrux\Pier\View\Components\Livewire;

use Livewire\Component;
use Livewire\Attributes\Reactive;

class Table extends Component
{
    public $model;

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
        // "date",
        "status",
        "color",
    ];

    public function render()
    {
        return view('pier::components.livewire.table.index');
    }
}
