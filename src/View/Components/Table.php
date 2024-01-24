<?php

namespace Jestrux\Pier\View\Components;

use Illuminate\View\Component;

class Table extends Component
{
    public $tableData;

    public function __construct(
        public $model = null,
        public $fields = null,
        public $data = null,
        public $page = 1,
        public $perPage = 1000,
    ) {
        $this->tableData = $data;
    }

    public function render()
    {
        return view('pier::components.table');
    }
}
