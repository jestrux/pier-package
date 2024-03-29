<?php

namespace Jestrux\Pier\View\Components;

use Illuminate\View\Component;

class Stack extends Component
{
    public $stackData;
    public $stackFields;
    public $stackModel;
    public $instanceId;

    public function __construct(
        public $data = null,
        public $model = null,
        public $imageField = null,
        public $metaField = null,
        public $titleField = null,
        public $descriptionField = null,
        public $rowActions = null,
        public $sortField = "index",
    ) {
        $this->instanceId = "pierStack" . bin2hex(random_bytes(6));
        $this->stackData = $data;
        $this->stackModel = $model;
        $this->stackFields = [
            'imageField' => $imageField,
            'metaField' => $metaField,
            'titleField' => $titleField,
            'titleField' => $titleField,
            'descriptionField' => $descriptionField,
        ];
    }

    public function render()
    {
        return view('pier::components.stack');
    }
}
