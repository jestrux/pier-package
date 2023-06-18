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
        public $onEdit = null,
    ) {
        $this->upsertModalId = "pierModal" . bin2hex(random_bytes(6));
        $this->gridData = $data;

        if ($onEdit == null) {
            $this->onEdit = fn ($rowId) => "window.dispatchEvent(new CustomEvent('update-pier-modal-form', {detail:{ modalId: '$this->upsertModalId', rowId: '$rowId' } }))";
        }
    }

    public function render()
    {
        return <<<'blade'
            @aware(['model', 'modelData'])

            @isset($model)
                <div class="pier-upsert-modal-wrapper" modal-id="{{$upsertModalId}}" @update-pier-modal-form.window="updateModalForm">
                    <x-pier-modal id="{{$upsertModalId}}" title="Edit {{$model ?? 'Details'}}" placement="right" width="700px">
                        <div x-show="fetchingModalContent">Loading, please wait...</div>
                        <div x-show="!fetchingModalContent">
                            <x-pier-form :model="$model" success-message="Success, we'll get back to you" />
                        </div>
                    </x-pier-modal>
                </div>
            @endisset()

            @unless(is_null($gridData ?? $modelData ?? null))
                @include("pier::data-grid-list.$template", ['data' => $gridData ?? $modelData])
            @endunless

            @if (!$lean && $showActions)
                @include('pier::delete-entry')
            @endif
        blade;
    }
}
