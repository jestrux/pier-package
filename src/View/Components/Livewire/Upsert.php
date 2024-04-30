<?php

namespace Jestrux\Pier\View\Components\Livewire;

use Livewire\Component;
use Livewire\Attributes\Reactive;

class Upsert extends Component
{
    public $model = null;
    public $rowId = null;
    public $fields = [];
    public $values = [];
    public $successMessage = null;
    public $onSave = null;
    public $onSuccess = null;

    public function render()
    {
        return view('pier::components.livewire.upsert');
    }
}
