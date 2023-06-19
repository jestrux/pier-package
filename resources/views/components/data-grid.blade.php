@if (!$noCss)
    <link href="{{ asset('pier/css/data-grid.css') }}" rel="stylesheet" />
@endif

<style>
    .group:hover .group-hover\:block {
        display: block;
    }

    .group:hover .group-hover\:flex {
        display: flex;
    }
</style>

<div x-data="pierComponent{{ $instanceId }}" x-init="init()">
    <div class="container max-w-6xl mx-auto">
        @if (!$lean && ($showAddButton || $showSearch))
            <div class="mb-5 flex items-center justify-between">
                @if ($showAddButton)
                    <x-pier-add-button :model="$model" />
                @endif

                @if ($showSearch)
                    <x-pier-search-input
                        class="border border-gray-300 w-72 px-3 h-10 rounded placeholder:text-black/20 focus:outline-none"
                        placeholder="Type to search..." />
                @endif
            </div>
        @endif

        <div id="pierComponent{{ $instanceId }}" pier-data-component="{{ $instanceId }}">
            @include("pier::data-grid-list.$template")
        </div>
    </div>
</div>

@if (!$lean && $showSearch)
    @include('pier::utils')

    <script>
        appendAlpineJS();

        document.addEventListener('alpine:init', () => {
            Alpine.data("pierComponent{{ $instanceId }}", () => ({
                ref: "pierComponent{{ $instanceId }}",
                filters: {!! collect($filters)->toJson() !!},
                async updatePierComponentContent() {
                    const parentEl = this.$el;
                    const activeEl = document.activeElement;
                    const activeElementIsWithinBounds = parentEl.contains(activeEl);

                    // exclude filters with empty, null or undefined values
                    let filters = Object.fromEntries(Object.entries(this.filters).filter(([key,
                        value
                    ]) => value !== null && value !== undefined && value.length));
                    filters = Object.fromEntries(Object.entries(filters).map(([key, value], i) => {
                        key = i == 0 ? key : key.replace("where", "andWhere");
                        return [key, value];
                    }));

                    let body = {
                        model: `{{ $model }}`,
                        template: `{{ $template }}`,
                        imageField: `{{ $imageField }}`,
                        metaField: `{{ $metaField }}`,
                        titleField: `{{ $titleField }}`,
                        descriptionField: `{{ $descriptionField }}`,
                        image: `{{ isset($image) ? $image : null }}`,
                        meta: `{{ isset($meta) ? $meta : null }}`,
                        title: `{{ isset($title) ? $title : null }}`,
                        description: `{{ isset($description) ? $description : null }}`
                    };

                    body = Object.fromEntries(Object.entries(body).filter(([key, value]) => {
                        return value !== null && value !== undefined && value.length;
                    }));

                    // These variables cause an error if user hasn't defined them
                    body.lean = "{{ $lean }}";
                    body.redirectTo = "{{ $redirectTo }}";
                    body.showActions = "{{ $showActions }}";

                    const res = await fetch("/data-grid-refetch", {
                        method: "POST",
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            ...body,
                            filters,
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

@if (!$lean && $showActions)
    @include('pier::delete-entry')
@endif
