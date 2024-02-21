<x-pier::menu>
    <x-pier::menu.button class="rounded hover:bg-content/5 p-1">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M6.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM12.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM18.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
        </svg>
    </x-pier::menu.button>

    <x-pier::menu.items>
        <x-pier::menu.close>
            <x-pier::menu.item href="{{ url('cms/' . $model) }}/upsert/{{ $row->_id }}">
                Edit
            </x-pier::menu.item>
            <x-pier::menu.item wire:click="deleteRow({{ $row->_id }})"
                wire:confirm="Are you sure you want to delete this row?">
                Delete
            </x-pier::menu.item>
        </x-pier::menu.close>
    </x-pier::menu.items>
</x-pier::menu>
