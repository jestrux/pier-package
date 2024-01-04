<div class="flex flex-col gap-8">
    <div class="flex justify-between">
        <h3></h3>

        <input type="search" class="border rounded h-10 px-3 min-w-[300px]" placeholder="Type to search {{ $model }}..."
            wire:model.live="q" />
    </div>
    <table class="min-w-full table-fixed divide-y divide-gray-300 text-gray-800">
        <thead>
            <tr>
                @foreach ($fields as $field)
                    <th
                        class="{{ $field->centered ? 'text-center' : 'text-left' }} capitalize p-3 text-sm font-semibold text-gray-900">
                        <div>{{ str_replace('_', ' ', $field->label) }}</div>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white text-gray-700">
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
