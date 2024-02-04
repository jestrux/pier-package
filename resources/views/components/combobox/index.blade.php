@props(['model', 'value' => null, 'onChange' => 'console.log'])

@assets()
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
</div>
