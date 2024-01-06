<div class="flex flex-col bg-card text-content/80">
    {{-- @if ($model)
        <div class="flex justify-between mb-8">
            <h3></h3>

            <input type="search" class="border rounded h-10 px-3 min-w-[300px]" placeholder="Type to search..."
                wire:model.live="q" />
        </div>
    @endif --}}

    <table class="min-w-full table-fixed divide-y divide-content/10 text-content/80">
        <thead>
            <tr>
                @foreach ($fields as $field)
                    @php
                        $type = $field->meta?->type ?? $field->type;
                        $centered = in_array($type, $centeredFields);
                    @endphp

                    <th
                        class="{{ $centered ? 'text-center' : 'text-left' }} capitalize p-3 text-sm font-semibold">
                        <div>{{ str_replace('_', ' ', $field->label) }}</div>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-content/5">
            @foreach ($rows as $row)
                <tr>
                    @foreach ($fields as $field)
                        @include('pier::components.livewire.table.column')
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
