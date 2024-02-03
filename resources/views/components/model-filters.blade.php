@aware(['model'])

@php
    $fields = $model->fields;

    try {
        $fields = json_decode($fields);
    } catch (\Throwable $th) {
        //throw $th;
    }

    $filters = collect($fields)->filter(fn($field) => collect(['status', 'boolean', 'reference'])->contains($field->type));
@endphp

@if ($filters->count() > 0)
    <div class="flex items-center gap-3 border-r border-content/20 mr-6 pr-6">
        <x-pier::popover>
            <x-pier::popover.button
                class="flex items-center gap-1 rounded-lg border-2 border-stroke pl-3 pr-2 py-1 text-content/50 text-sm">
                <svg class="-ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z" />
                </svg>

                <div>Filters</div>

                <div x-cloak x-show="Object.keys($wire.filters ?? {}).length"
                    class="h-4 w-4 rounded-full bg-blue-500 text-white text-[10px] font-bold tracking-tighter flex items-center justify-center"
                    x-text="Object.keys($wire.filters ?? {}).length">
                </div>

                <svg class="w-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </x-pier::popover.button>

            <x-pier::popover.panel position="bottom-end"
                class="border-2 border-stroke shadow-xl z-10 w-96 overflow-hidden">
                <div class="p-3 flex flex-col gap-4">
                    @foreach ($filters as $field)
                        <div class="ModelFilterField flex items-center justify-between gap-3">
                            <span class="inline-block first-letter:uppercase flex-1">
                                {{ $field->label }}
                            </span>
                            <div key={field._id} class="flex-shrink-0" style="width: 220px">
                                @if ($field->type == 'reference')
                                    <x-pier::combobox :model="$field->meta->model" value="$wire.filters?.['{{ $field->label }}']"
                                        on-change="e => $wire.setFilter('{{ $field->label }}', e.detail)">
                                    </x-pier::combobox>
                                @endif

                                @if ($field->type == 'boolean')
                                    @php
                                        $choices = collect([['label' => 'All', 'value' => ''], ['label' => '✅', 'value' => '1'], ['label' => '❌', 'value' => '0']]);
                                    @endphp

                                    <x-pier::radio :choices="$choices" :value="$wire->filters->{$field->label} ?? ''"
                                        on-change="e => $wire.setFilter('{{ $field->label }}', e.detail)">
                                    </x-pier::radio>
                                @endif

                                @if ($field->type == 'status')
                                    <select class="pl-2 bg-transparent border-2 border-stroke p-1 rounded w-full"
                                        x-bind:value="$wire.filters?.['{{ $field->label }}']"
                                        x-on:change="$wire.setFilter('{{ $field->label }}', $event.target.value)">
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

                <div class="h-8 flex items-center px-3 bg-content/5 rounded-b-md">
                    <button class="underline text-xs font-semibold"
                        x-on:click="$dispatch('reset-filters'); $wire.resetFilters()">
                        Reset filters
                    </button>
                </div>
            </x-pier::popover.panel>
        </x-pier::popover>
    </div>
@endif
