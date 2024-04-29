@aware(['rowActions', 'onEdit', 'model'])

@php
    if (!is_null($rowActions ?? null)) {
        $actions = $rowActions;
    }

    $model = $model ?? null;

    if (!is_null($buttonsModel)) {
        $model = $buttonsModel;
    }

    $editLink = is_null($model) ? '#' : url('/admin/upsertModel/' . $model . '/' . $rowId);
@endphp

<x-pier::menu>
    <x-pier::menu.button class="rounded hover:bg-content/5 p-1">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M6.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM12.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM18.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
        </svg>
    </x-pier::menu.button>

    <x-pier::menu.items class="!min-w-52">
        <x-pier::menu.close>
            @if (!is_null($onEdit ?? null))
                <x-pier::menu.item onclick="{{ $onEdit($rowId) }}">
                    Edit
                </x-pier::menu.item>
            @else
                <x-pier::menu.item href="{{ $editLink }}" data-row-id="{{ $rowId }}"
                    x-on:click="openUpsertModal">
                    Edit
                </x-pier::menu.item>
            @endif

            @isset($actions)
                @foreach ($rowActions as $action)
                    @if (isset($action['href']))
                        <x-pier::menu.item href="{{ $action['href']($rowId) }}">
                            {{ $action['label'] }}
                        </x-pier::menu.item>
                    @elseif (isset($action['onClick']))
                        <x-pier::menu.item onclick="{{ $action['onClick']($rowId) }}">
                            {{ $action['label'] }}
                        </x-pier::menu.item>
                    @endif
                @endforeach
            @endisset

            @if (!is_null($model))
                <x-pier::menu.item onclick="deleteEntry('{{ $model }}', '{{ $rowId }}')">
                    <span class="text-red-600">Delete</span>
                </x-pier::menu.item>
            @endif
        </x-pier::menu.close>
    </x-pier::menu.items>
</x-pier::menu>

{{-- <div x-data="{
    open: false,
    toggle() {
        if (this.open) {
            return this.close()
        }

        this.$refs.button.focus()

        this.open = true
    },
    close(focusAfter) {
        if (!this.open) return

        this.open = false

        focusAfter && focusAfter.focus()
    }
}" class="opacity-0 group-hover:opacity-100" :class="{ 'opacity-100 relative z-10': open }">
    <div class="flex justify-center">
        <div x-on:keydown.escape.prevent.stop="close($refs.button)"
            x-on:focusin.window="! $refs.panel.contains($event.target) && close()" x-id="['dropdown-button']">
            <button x-ref="button" x-on:click="toggle()" :aria-expanded="open" :aria-controls="$id('dropdown-button')"
                type="button"
                class="cursor-pointer flex items-center justify-center w-7 h-7 bg-card rounded-full focus:outline-none border border-transparent focus:border-content/5">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM12.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM18.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                </svg>
            </button>

            <div x-ref="panel" x-show="open" x-transition.origin.top.left x-on:click.outside="close($refs.button)"
                :id="$id('dropdown-button')" style="display: none;"
                class="absolute right-1 w-40 rounded-md bg-white border shadow-md" x-on:click="close($refs.button)">

                @if (!is_null($onEdit ?? null))
                    <button onclick="{{ $onEdit($rowId) }}"
                        class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-neutral-100 disabled:text-gray-500">
                        Edit
                    </button>
                @else
                    <a href="{{ $editLink }}"
                        class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-neutral-100 disabled:text-gray-500"
                        data-row-id="{{ $rowId }}" x-on:click="openUpsertModal">
                        Edit
                    </a>
                @endif

                @isset($actions)
                    @foreach ($actions as $action)
                        @if (isset($action['href']))
                            <a href="{{ $action['href']($rowId) }}"
                                class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-neutral-100 disabled:text-gray-500">
                                {{ $action['label'] }}
                            </a>
                        @elseif (isset($action['onClick']))
                            <button onclick="{{ $action['onClick']($rowId) }}"
                                class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-neutral-100 disabled:text-gray-500">
                                {{ $action['label'] }}
                            </button>
                        @endif
                    @endforeach
                @endisset

                @if (!is_null($model))
                    <button
                        class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-neutral-100 disabled:text-gray-500"
                        onclick="deleteEntry('{{ $model }}', '{{ $rowId }}')">
                        <span class="text-red-600">Delete</span>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div> --}}
