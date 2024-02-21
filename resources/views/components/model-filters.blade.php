@props(['model', 'onChange' => 'console.log'])

@php
    $fields = $model->fields;

    try {
        $fields = json_decode($fields);
    } catch (\Throwable $th) {
        //throw $th;
    }

    $supportedFilters = ['status', 'boolean', 'reference', 'rating'];
    $filters = collect($fields)->filter(fn($field) => collect($supportedFilters)->contains($field->type));
@endphp

@if ($filters->count() > 0)
    <div class="flex items-center gap-3 mr-5 pr-5 relative" x-data="{
        filters: {},
        tempFilters: {},
        init() {
            this.$watch('tempFilters', (newValue) => {
                const entries = Object.entries(newValue).reduce((agg, [key, value]) => {
                    if (typeof value === 'object') value = value?._id;
    
                    if ([null, undefined, ''].includes(value)) return agg;
    
                    return [...agg, [key, value]];
                }, []);
    
                this.filters = Object.fromEntries(entries);
                this.$wire.set('filters', this.filters);
            });
        },
        resetFilters() {
            this.tempFilters = {};
        },
    }">
        <span class="absolute inset-y-1.5 right-0 border-r-2 border-stroke"></span>

        <x-pier::popover>
            <x-pier::popover.button
                class="flex items-center gap-1 rounded-md border-2 border-stroke pl-3 pr-1.5 py-1.5 text-content/50 text-sm">
                <svg class="-ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z" />
                </svg>

                <div>Filters</div>

                <div x-cloak x-show="Object.keys(filters ?? {}).length"
                    class="h-4 w-4 rounded-full bg-blue-500 text-white text-[10px] font-bold tracking-tighter flex items-center justify-center"
                    x-text="Object.keys(filters ?? {}).length">
                </div>

                <svg class="ml-1.5 w-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </x-pier::popover.button>

            <x-pier::popover.panel position="bottom-end" class="border-2 border-stroke shadow-xl z-10 w-96">
                <div class="p-3 flex flex-col gap-4">
                    @foreach ($filters as $field)
                        <div class="ModelFilterField flex items-center justify-between gap-3">
                            <span class="pl-1 inline-block first-letter:uppercase flex-1">
                                {{ $field->label }}
                            </span>
                            <div key={field._id} class="flex-shrink-0" style="width: 220px"
                                x-model="tempFilters['{{ $field->label . ($field->type == 'rating' ? 'IsGreaterThanOrEqual' : '') }}']">
                                @if ($field->type == 'reference')
                                    <x-pier::combobox :model="$field->meta->model" x-model="tempFilters['{{ $field->label }}']" />
                                @endif

                                @if ($field->type == 'rating')
                                    <x-pier::multi-range :max="$field->meta->outOf"
                                        x-model="tempFilters['{{ $field->label }}IsGreaterThanOrEqual']" />
                                @endif

                                @if ($field->type == 'boolean')
                                    @php
                                        $choices = collect([['label' => 'All', 'value' => ''], ['label' => '✅', 'value' => '1'], ['label' => '❌', 'value' => '0']]);
                                    @endphp

                                    <x-pier::radio :choices="$choices" x-model="tempFilters['{{ $field->label }}']" />
                                @endif

                                @if ($field->type == 'status')
                                    <select
                                        class="bg-transparent border-2 border-stroke focus:border-stroke pl-3 py-0 h-10 rounded-md w-full focus:ring-1 focus:ring-blue-500"
                                        x-model="tempFilters['{{ $field->label }}']">
                                        <option value="">All</option>
                                        @foreach ($field->meta->availableStatuses as $status)
                                            <option>{{ $status->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="h-8 flex items-center px-4 bg-content/5 rounded-b-md">
                    {{-- <x-pier::popover.close> --}}
                    <button class="text-xs font-semibold"
                        x-bind:class="Object.keys(filters).length ? 'underline opacity-45 hover:opacity-60' :
                            'opacity-20 pointer-events-none'"
                        x-on:click="resetFilters">
                        Reset filters
                    </button>
                    {{-- </x-pier::popover.close> --}}
                </div>
            </x-pier::popover.panel>
        </x-pier::popover>
    </div>
@endif
