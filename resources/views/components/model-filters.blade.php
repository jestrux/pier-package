@props(['model'])

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
    <div class="flex items-center gap-3 mr-5 pr-5 relative">
        <span class="absolute inset-y-1.5 right-0 border-r-2 border-stroke"></span>

        <x-pier::popover>
            <x-pier::popover.button
                class="flex items-center gap-1 rounded-md border-2 border-stroke pl-3 pr-1.5 py-1.5 text-content/50 text-sm">
                <svg class="-ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z" />
                </svg>

                <div>Filters</div>

                <div x-cloak x-show="Object.keys($wire.filters ?? {}).length"
                    class="h-4 w-4 rounded-full bg-blue-500 text-white text-[10px] font-bold tracking-tighter flex items-center justify-center"
                    x-text="Object.keys($wire.filters ?? {}).length">
                </div>

                <svg class="ml-1.5 w-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </x-pier::popover.button>

            <x-pier::popover.panel position="bottom-end"
                class="border-2 border-stroke shadow-xl z-10 w-96 overflow-hidden">
                <div class="p-3 flex flex-col gap-4">
                    @foreach ($filters as $field)
                        <div class="ModelFilterField flex items-center justify-between gap-3">
                            <span class="pl-1 inline-block first-letter:uppercase flex-1">
                                {{ $field->label }}
                            </span>
                            <div key={field._id} class="flex-shrink-0" style="width: 220px">
                                @if ($field->type == 'reference')
                                    <x-pier::combobox :model="$field->meta->model" :value="$wire->filters->{$field->label} ?? ''"
                                        on-change="e => $wire.set('filters.{{ $field->label }}', e.detail)">
                                    </x-pier::combobox>
                                @endif

                                @if ($field->type == 'rating')
                                    <x-pier::multi-range :max="$field->meta->outOf" :value="$wire->filters->{$field->label} ?? 0"
                                        on-change="e => $wire.set('filters.{{ $field->label }}IsGreaterThanOrEqual', e.detail)">
                                    </x-pier::multi-range>

                                    {{-- <div class="flex items-center gap-2">
                                        <input class="w-full" type="range" step="0.5" x-bind:min="0"
                                            x-bind:max="{{ $field->meta->outOf }}"
                                            x-on:change="$wire.set('filters.{{ $field->label }}IsGreaterThanOrEqual', $event.target.value)"
                                            x-bind:value="$wire.filters?.['{{ $field->label }}IsGreaterThanOrEqual'] ?? 0" />

                                        <span
                                            class="h-7 w-10 flex items-center justify-center border-2 border-stroke rounded text-xs font-bold"
                                            x-text="$wire.filters?.['{{ $field->label }}IsGreaterThanOrEqual'] ?? 0"></span>
                                    </div> --}}
                                @endif

                                @if ($field->type == 'boolean')
                                    @php
                                        $choices = collect([['label' => 'All', 'value' => ''], ['label' => '✅', 'value' => '1'], ['label' => '❌', 'value' => '0']]);
                                    @endphp

                                    <x-pier::radio :choices="$choices" :value="$wire->filters->{$field->label} ?? ''"
                                        on-change="e => $wire.set('filters.{{ $field->label }}', e.detail)">
                                    </x-pier::radio>
                                @endif

                                @if ($field->type == 'status')
                                    <select
                                        class="bg-transparent border-2 border-stroke focus:border-stroke pl-3 py-0 h-10 rounded-md w-full focus:ring-1 focus:ring-blue-500"
                                        x-bind:value="$wire.filters?.['{{ $field->label }}']"
                                        x-on:change="$wire.set('filters.{{ $field->label }}', $event.target.value)">
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
                    <x-pier::popover.close>
                        <button class="underline opacity-45 hover:opacity-60 text-xs font-semibold"
                            x-on:click="$dispatch('reset-filters'); $wire.resetFilters()">
                            Reset filters
                        </button>
                    </x-pier::popover.close>
                </div>
            </x-pier::popover.panel>
        </x-pier::popover>
    </div>
@endif
