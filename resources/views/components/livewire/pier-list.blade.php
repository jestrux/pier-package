{{-- @assets() --}}
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/sort@3.x.x/dist/cdn.min.js"></script>
{{-- @endassets --}}

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data("pierList{{ $instanceId }}", () => ({
            fetchingModalContent: false,
            openUpsertModal(e) {
                const el = this.$el;
                let modal = el.closest("[pier-list-component]");
                modal = !modal ? null : modal.querySelector('.pier-upsert-modal-wrapper');

                if (modal) {
                    e.preventDefault();
                    this.updateModalForm({
                        detail: {
                            modalId: modal.getAttribute("modal-id"),
                            rowId: el.getAttribute("data-row-id")
                        }
                    });
                }
            },
            async updateModalForm({
                detail
            }) {
                const form = this.$root.querySelector(`.piermodal .PierFormWrapper`);

                let modalResponse;

                window.closePierEditModal = function(e) {
                    window.hidePierModal(detail.modalId, e);
                }

                if (form) {
                    let values = {};

                    if (detail.rowId) {
                        this.fetchingModalContent = true;
                        values = await fetch(`/api/{{ $model }}/${detail.rowId}`).then(
                            res => res.json());
                        this.fetchingModalContent = false;
                    }

                    form.dispatchEvent(new CustomEvent('update-form-values', {
                        detail: values
                    }));

                    modalResponse = await window.showPierModal(detail.modalId, {
                        title: `${values?._id ? 'Edit' : 'Add'} {{ $model }}`
                    });
                } else {
                    modalResponse = await window.loadPierModalContent(detail.modalId, {
                        url: `/admin/upsertModel/{{ $model }}/${detail.rowId}?plain=true&onSave=e => window.closePierEditModal(e)`,
                        title: `${detail.rowId ? 'Edit' : 'Add'} {{ $model }}`
                    });
                }

                if (modalResponse?._id) window.location.reload();
            },
        }));
    });
</script>

<div>
    <div id="pierComponent{{ $instanceId }}" pier-list-component="{{ $instanceId }}" x-data="pierList{{ $instanceId }}">
        <div class="flex flex-col gap-2" @if ($sortBy) x-sort @endif>
            @foreach ($data as $row)
                <div @if ($sortBy) x-sort:item @endif
                    class="group relative bg-card p-3 w-full flex items-center gap-2 focus:outline-none border rounded-md shadow-sm">

                    @if ($sortBy)
                        <div style="cursor: -webkit-grabbing"
                            class="stack-handle cursor-move ml-1 flex-shrink-0 sopacity-0 group-hover:opacity-100">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </div>
                    @endif

                    @if ($row->image ?? null != null)
                        @php
                            $imageField = $fields->firstWhere('label', $image);
                            $isFace = ($imageField->meta ?? (object) [])->face ?? null;
                        @endphp

                        <img style="{{ $isFace ? 'aspect-ratio:1/1; border-radius: 50%;' : 'aspect-ratio:1.4/1; border-radius: 4px;' }} height: 40px; object-fit: cover"
                            class="bg-content/5 flex-shrink-0 inset-0 object-cover" src="{{ $row->image }}" />
                    @endif

                    <div class="pr-10">
                        @if ($row->title ?? null != null)
                            <h5 class="line-clamp-1 text-sm font-medium">
                                {{ $row->title }}
                            </h5>
                        @else
                            <h5 class="line-clamp-1 text-sm font-medium">
                                &nbsp;
                            </h5>
                        @endif

                        @if ($row->description ?? null != null)
                            <p class="line-clamp-1 text-xs leading-relaxed opacity-80 font-light">
                                {{ mb_strimwidth(strip_tags($row->description), 0, 100, '...') }}
                            </p>
                        @endif

                        <div class="absolute inset-y-3 right-3 flex items-center">
                            <x-pier-action-buttons :model="$model ?? null" :row-id="$row->_id" />
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pier-upsert-modal-wrapper" modal-id="pierModal{{ $instanceId }}"
            @update-pier-modal-form.window="updateModalForm">
            <x-pier-modal id="pierModal{{ $instanceId }}" title="Edit {{ $model }}" placement="right"
                width="700px" />
        </div>
    </div>

    @include('pier::delete-entry')
</div>
