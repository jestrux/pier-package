<?php

namespace Jestrux\Pier\View\Components;

use Illuminate\View\Component;

class Grid extends Component
{
    public $gridData;
    public $upsertModalId;

    public function __construct(
        public $data = null,
        public $template = "default",
        public $imageField = null,
        public $metaField = null,
        public $titleField = null,
        public $descriptionField = null,
        public $gap = "12px",
        public $lean = false,
        public $showActions = true,
        public $rowActions = null,
    ) {
        $this->gridData = $data;
    }

    public function render()
    {
        return <<<'blade'
            @aware(['modelData'])

            @unless(is_null($gridData ?? $modelData ?? null))
                @include("pier::data-grid-list.$template", ['data' => $gridData ?? $modelData])
            @endunless

            @if (!$lean && $showActions)
                @include('pier::delete-entry')
            @endif
        blade;
    }
}
