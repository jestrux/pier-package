@props(['model', 'value' => null, 'onChange' => 'console.log'])

@php
    $inputClass = $attributes->get('class');
    if (!$inputClass || strlen($inputClass) < 1) {
        $inputClass = 'flex items-center justify-between gap-2 w-full px-2.5 h-10 bg-card border-2 border-stroke rounded-md focus:outline-none focus:ring-0 placeholder:text-content/20';
    }
@endphp

@assets()
    <style>
        .pier-combobox ul {
            background: rgb(var(--card-color));
            position: fixed;
            z-index: 10;
            max-height: 15rem;
            margin-top: 0.3rem;
            width: inherit;
            transform-origin: top right;
            overflow: auto;
            border-radius: 0.375rem;
            border-width: 2px;
            --tw-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --tw-shadow-colored: 0 4px 6px -1px var(--tw-shadow-color), 0 2px 4px -2px var(--tw-shadow-color);
            box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
            outline: 2px solid transparent;
            outline-offset: 2px;
        }

        .pier-combobox li {
            display: flex;
            width: 100%;
            cursor: default;
            align-items: center;
            gap: 0.5rem;
            padding-left: 1rem;
            padding-right: 1rem;
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
            cursor: pointer;
        }

        .pier-combobox li:not([aria-selected="true"]):not([aria-selected="true"]):hover {
            opacity: 50;
        }

        .pier-combobox li[aria-selected="true"],
        .pier-combobox li:hover {
            background-color: rgb(var(--content-color)/0.1);
            color: rgb(var(--content-color));
        }
    </style>
    {{-- <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@tarekraafat/autocomplete.js@10.2.7/dist/css/autoComplete.min.css" /> --}}
    <script src="https://cdn.jsdelivr.net/npm/@tarekraafat/autocomplete.js@10.2.7/dist/autoComplete.min.js"></script>
@endassets

<div class="pier-combobox relative"
    x-data='{
    ctx: null,
    defaultValue: "{{ $value }}",
    modelName: "{{ $model }}",
    value: "{{ $value }}",
    selected: null,
    reset() {
        this.handleChange(null);
    },
    init() {
        console.log("Init: ", this.value);
        this.search("").then(res => {
            if (this.value) {
                const selected = res.find(({ _id }) => _id == this.value);
                console.log(res, selected);
                this.selected = selected;
            }
        });
    },
    async search(query) {
        try {
            return await fetch(
                `{{ url('/api') }}/${this.modelName}/search?q=${query}`
            ).then(res => res.json());
        } catch (error) {
            console.log("Failed to search.", error);
        }
    },
    handleChange(value) {
        this.selected = value;
        this.value = value?._id;

        const combobox = this.$el.closest(".pier-combobox");

        combobox.dispatchEvent(
            new CustomEvent("change", {
                detail: value?._id ?? "",
            })
        );

        if(value == null) {
            this.$nextTick(() => {
                combobox.querySelector("input").focus();
            });
        }
    }
    }'
    x-on:change="{{ $onChange }}" x-on:reset-filters.window="reset()">

    <template x-if="!selected">
        <div x-bind:class="{ 'absolute opacity-0 pointer-events-none': selected }"
            x-data='{
                destroy() {
                    console.log("On destroy..", this.ctx);
                },
                init() {
                    console.log("Init inner...", this.ctx, this.ctx?.start);
                    if(this.ctx) this.ctx.start();
                    else this.setup();
                },
                setup(){
                    const input = this.$el.querySelector("input");
                    const ctx = new autoComplete({
                        selector: () => input,
                        placeHolder: "Type to search...",
                        data: {
                            src: async () => await this.search(input.value),
                            keys: ["label"],
                            cache: true,
                        },
                        threshold: 0,
                        resultItem: {
                            highlight: true
                        },
                        events: {
                            input: {
                                focus: () => {
                                    input.dispatchEvent(new Event("input", { bubbles: true }));
                                    ctx.list.style.width = this.$el.getBoundingClientRect().width + "px";
                                },
                                keyup: (e) => {
                                    if(e.key == "Escape") {
                                        input.dispatchEvent(new Event("input", { bubbles: true }));
                                    }
                                },
                                selection: (event) => {
                                    const value = event.detail.selection.value;
                                    input.value = value.label;
                                    this.handleChange(value);
                                }
                            }
                        }
                    });

                    this.ctx = ctx;
                },
            }'>

            <input class="{{ $inputClass }} placeholder:text-content/20" placeholder="Type to search..."
                x-bind:style="{ opacity: selected ? 0 : 1 }" />
        </div>
    </template>

    <div class="{{ $inputClass }} !flex items-center justify-between"
        x-bind:class="{ 'absolute opacity-0 pointer-events-none': !selected }">
        <span class="truncate" x-text="selected?.label"></span>
        <button type="button"
            class="flex-shrink-0 h-full px-1 text-primary text-xs uppercase tracking-wide font-medium"
            x-on:click="reset()">Change</button>
    </div>
</div>

{{-- @assets()
    <script defer src="https://unpkg.com/@alpinejs/ui@3.13.3-beta.4/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/@alpinejs/focus@3.13.3/dist/cdn.min.js"></script>
@endassets

<div x-data="{
    modelName: '{{ $model }}',
    query: '',
    width: '', //'220px',
    defaultValue: '{{ $value }}',
    _selected: null,
    selected: null,
    results: [],
    reset() {
        this.query = null;
        this.selected = null;
        this.$el.querySelector('input').value = '';
    },
    async search() {
        try {
            const res = await fetch(
                `{{ url('/api') }}/${this.modelName}/search?q=${this.query}`
            ).then(res => res.json());

            this.results = res;

            return res;
        } catch (error) {
            console.log('Failed to search.', error);
        }
    },
    init() {
        this.search().then(res => {
            if (this.defaultValue) {
                const value = res.find(({ _id }) => _id == this.defaultValue);
                this.selected = value;
                this._selected = value;
            }
        });
        this.$watch('query', _ => this.search);
        this.$watch('selected', newValue => {
            if (this._selected?._id == newValue?._id) return;

            this.$el.dispatchEvent(
                new CustomEvent('change', {
                    detail: newValue?._id,
                })
            );

            this._selected = newValue;
        });
    }
}" x-on:change="{{ $onChange }}" x-on:reset-filters.window="reset()">
    <div x-combobox x-model="selected" nullable>
        <div class="mt-1 flex flex-col items-end relative rounded-md focus-within:ring-2 focus-within:ring-blue-500">
            <div
                {{ $attributes->merge([
                    'class' => 'flex items-center justify-between gap-2 w-full px-2.5 h-10 bg-card border-2 border-stroke rounded-md',
                ]) }}>
                <input x-combobox:input x-bind:display-value="res => res?.label"
                    x-on:change="query = $event.target.value;"
                    class="bg-transparent border-none p-0 focus:outline-none focus:ring-0 placeholder:text-content/20"
                    placeholder="Search..." autocomplete="off" />
                <button x-combobox:button class="absolute inset-y-0 right-0 flex items-center pr-2.5">
                    <!-- Heroicons up/down -->
                    <svg class="shrink-0 w-5 h-5 opacity-50" viewBox="0 0 20 20" fill="none" stroke="currentColor">
                        <path d="M7 7l3-3 3 3m0 6l-3 3-3-3" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </button>
            </div>

            <div x-combobox:options x-cloak
                class="fixed max-w-xs w-full max-h-60 mt-12 z-10 origin-top-right overflow-auto bg-card border-2 border-stroke rounded-md shadow-md outline-none"
                x-transition.out.opacity x-bind:style="{ width }">
                <ul class="divide-y divide-stroke">
                    <template x-for="result in results" x-bind:key="result._id" hidden>
                        <li x-combobox:option x-bind:value="result"
                            x-bind:class="{
                                'bg-content/10': $comboboxOption.isActive,
                                'text-content/60': !$comboboxOption.isActive,
                                'opacity-50 cursor-not-allowed': $comboboxOption.isDisabled,
                            }"
                            class="flex items-center cursor-default justify-between gap-2 w-full px-4 py-2 text-sm">

                            <span x-text="result.label"></span>

                            <span x-show="$comboboxOption.isSelected" class="font-bold">&check;</span>
                        </li>
                    </template>
                </ul>

                <p x-show="results.length == 0" class="px-4 py-2 text-sm text-content/50">
                    No results match your query.
                </p>
            </div>
        </div>
    </div>
</div> --}}
