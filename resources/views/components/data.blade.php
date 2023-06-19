@php
    $model = $modelDetails;
    $data = $modelData;
    $compiledSlot = Blade::compileString($slot);
    $plain = !is_null($plain) ? $plain : !(str_contains($compiledSlot, 'pier-ref') || str_contains($compiledSlot, 'Jestrux\Pier\View\Components'));
    $plain = filter_var($plain, FILTER_VALIDATE_BOOLEAN);
@endphp

@if ($plain)
    {!! eval('?>' . $compiledSlot) !!}
@else
    @include('pier::utils')

    <div id="pierComponent{{ $instanceId }}" pier-data-component="{{ $instanceId }}" x-data="pierComponent{{ $instanceId }}"
        x-init="init()" {{ $attributes }}>

        {!! eval('?>' . $compiledSlot) !!}

        <div class="pier-upsert-modal-wrapper" modal-id="pierModal{{ $instanceId }}"
            @update-pier-modal-form.window="updateModalForm">
            <x-pier-modal id="pierModal{{ $instanceId }}" title="Edit {{ $model->name }}" placement="right"
                width="700px" />
        </div>
    </div>

    <template id="slotTemplate">
        {{ $slot }}
    </template>

    <style>
        :root {
            --primary-color: {{ env('APP_COLOR') ?? '#2c5282' }};
        }
    </style>

    <script>
        appendAlpineJS();

        document.addEventListener('alpine:init', () => {
            Alpine.data("pierComponent{{ $instanceId }}", () => ({
                ref: "pierComponent{{ $instanceId }}",
                filters: {!! collect($filters)->toJson() !!},
                fetchingModalContent: false,
                openUpsertModal(e) {
                    const el = this.$el;
                    let modal = el.closest("[pier-data-component]");
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
                    const form = this.$el.closest("[pier-data-component]").querySelector(
                        `#${detail.modalId} .PierFormWrapper`);

                    if (form) {
                        let values = {};

                        if (detail.rowId) {
                            this.fetchingModalContent = true;
                            values = await fetch(`/api/{{ $model->name }}/${detail.rowId}`).then(
                                res => res.json());
                            this.fetchingModalContent = false;
                        }

                        form.dispatchEvent(new CustomEvent('update-form-values', {
                            detail: values
                        }));

                        window.showPierModal(detail.modalId, {
                            title: `${values?._id ? 'Edit' : 'Add'} {{ $model->name }}`
                        });
                    } else {
                        window.loadPierModalContent(detail.modalId, {
                            url: `/admin/upsertModel/{{ $model->name }}/${detail.rowId}?plain=true`,
                            title: `${detail.rowId ? 'Edit' : 'Add'} {{ $model->name }}`
                        });
                    }
                },
                async updatePierComponentContent() {
                    const parentEl = this.$el;
                    const activeEl = document.activeElement;
                    const activeElementIsWithinBounds = parentEl.contains(activeEl);

                    const model = "{{ $model->name }}";
                    // exclude filters with empty, null or undefined values
                    let filters = Object.fromEntries(Object.entries(this.filters).filter(([key,
                        value
                    ]) => value !== null && value !== undefined && value.length));
                    filters = Object.fromEntries(Object.entries(filters).map(([key, value], i) => {
                        key = i == 0 ? key : key.replace("where", "andWhere");
                        return [key, value];
                    }));

                    filters = {
                        ...filters,
                        orderBy: "{{ $orderBy }}",
                        groupBy: "{{ $groupBy }}",
                        limit: "{{ $limit }}",
                        pluck: "{{ $pluck }}",
                    }

                    const res = await fetch("/data-refetch", {
                        method: "POST",
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            model,
                            filters,
                            imageField: "{{ $imageField ?? null }}",
                            metaField: "{{ $metaField ?? null }}",
                            titleField: "{{ $titleField ?? null }}",
                            descriptionField: "{{ $descriptionField ?? null }}",
                            view: document.querySelector("#slotTemplate").innerHTML,
                        })
                    });

                    const newContent = await res.text();
                    document.querySelector("#pierComponent{{ $instanceId }}").innerHTML =
                        newContent;

                    if (activeElementIsWithinBounds) {
                        const activeElPierRef = activeEl.getAttribute("pier-ref");
                        setTimeout(() => {
                            if (activeElPierRef && activeElPierRef.length) {
                                this.$nextTick(() => {
                                    const newActiveElementInstance = this.$el
                                        .querySelector(
                                            `[pier-ref="${activeElPierRef}"]`);
                                    if (newActiveElementInstance)
                                        newActiveElementInstance.focus();
                                });
                            }
                        }, 100);
                    }
                },
                init() {
                    this.$watch('filters', _ => {
                        this.updatePierComponentContent();
                    });
                }
            }));
        });
    </script>
@endif
