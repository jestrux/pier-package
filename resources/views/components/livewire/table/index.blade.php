<table class="min-w-full table-fixed divide-y divide-content/10 text-content/80">
    <thead>
        <tr>
            @foreach ($fields as $field)
                @php
                    $type = $field->meta?->type ?? $field->type;
                    $centered = in_array($type, $centeredFields);
                @endphp

                <th class="{{ $centered ? 'text-center' : 'text-left' }} capitalize py-4 px-3 text-sm font-semibold">
                    <span class="{{ $loop->index == 0 ? 'pl-4' : '' }}">{{ str_replace('_', ' ', $field->label) }}</span>
                </th>
            @endforeach

            <th class="w-12 text-center">
                {{-- Actions --}}
            </th>
        </tr>
    </thead>
    <tbody class="divide-y divide-content/5">
        @foreach ($data as $row)
            <tr>
                @foreach ($fields as $field)
                    @include('pier::components.livewire.table.column')
                @endforeach

                <td class="w-12 text-center">
                    @if ($row->_id ?? null)
                        <span class="inline-flex">
                            @include('pier::components.livewire.table.column-dropdown')
                        </span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
