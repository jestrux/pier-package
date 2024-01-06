@assets()
<meta name="csrf_token" value="{{ csrf_token() }}" />

<style>
    :root {
        --border-color: #e2e8f0;
        --canvas-color: 229 229 229;
        --card-color: 255 255 255;
        --content-color: 0 0 0;
        --primary-color: {{ join(' ', sscanf($appColor, '#%02x%02x%02x')) }};
    }

    @media (prefers-color-scheme: dark) {
        :root {
            --border-color: rgba(255, 255, 255, 0.16);
            --canvas-color: 24 24 24;
            --card-color: 37 37 37;
            --content-color: 255 255 255;
        }

        input[type="date"] {
            color-scheme: dark;
        }
    }

    * {
        border-color: var(--border-color);
    }

    .flex>* {
        min-width: 0;
    }
</style>

<script>
    window.tailwind.config = {
        theme: {
            extend: {
                colors: {
                    canvas: "rgb(var(--canvas-color) / <alpha-value>)",
                    card: "rgb(var(--card-color) / <alpha-value>)",
                    content: "rgb(var(--content-color) / <alpha-value>)",
                    primary: "rgb(var(--primary-color) / <alpha-value>)",
                },
            },
        },
    };
</script>
@endassets()


<div class="h-screen overflow-hidden flex bg-canvas text-content">
    <aside class="h-screen overflow-y-auto w-72 flex-shrink-0 shadow bg-card">
        <div class="flex flex-col gap-3 p-6 pt-20 bg-primary text-white">
            @if ($appLogo)
                <div class="w-16 h-16 p-3.5 bg-white rounded-full overflow-hidden flex items-center justify-center">
                    <img src="{{ asset($appLogo) }}" alt="" class="w-full max-h-full" />
                </div>
            @endif

            <span class="text-xl font-bold">{{ join(' ', [$appName, 'CMS']) }}</span>
        </div>

        <ul class="py-3">
            @foreach ($models as $model)
                <li wire:key="{{ $model->_id }}">
                    <a wire:navigate href="{{ url('pier-cms/' . $model->name) }}"
                        class="{{ $model->_id == $currentModel->_id ? 'bg-content/5 text-primary border-current' : 'border-transparent opacity-75' }} hover:bg-content/5 capitalize px-4 h-12 flex items-center border-l-[3px] text-base font-medium">
                        {{ str($model->name)->plural() }}
                    </a>
                </li>
            @endforeach
        </ul>
    </aside>


    <main class="flex-1 h-screen flex flex-col relative">
        <header class="flex items-center justify-between px-8 h-16 pl-11 bg-card flex-shrink-0 shadow-sm">
            <span class="mr-3 my-3 font-bold capitalize text-xl">
                {{ str($currentModel->name)->plural() }}
            </span>
            <a href="#/{{ $currentModel->name }}/list/add"
                class="border border-current flex font-semibold gap-1 items-center rounded-full text-primary text-sm leading-none hover:bg-neutral-200/50"
                style="padding: 0.4rem 1rem;">
                <svg height="18px" fill="currentColor" viewBox="0 0 24 24" class="-ml-1">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path>
                </svg>
                <span class="lowercase first-letter:uppercase inline-block">New entry</span>
            </a>

            <span class="flex-1"></span>

            <div class="mr-8 flex gap-4 items-center">
                <div class="relative">
                    <button id="showFiltersButton"
                        class="rounded-md border-2 py-1 px-2 flex items-center focus:outline-none"
                        style="display: flex;"><svg fill="#888" width="24" height="24" viewBox="0 0 24 24">
                            <path
                                d="M7,6h10l-5.01,6.3L7,6z M4.25,5.61C6.27,8.2,10,13,10,13v6c0,0.55,0.45,1,1,1h2c0.55,0,1-0.45,1-1v-6 c0,0,3.72-4.8,5.74-7.39C20.25,4.95,19.78,4,18.95,4H5.04C4.21,4,3.74,4.95,4.25,5.61z">
                            </path>
                        </svg><svg viewBox="0 0 24 24" class="h-4 w-4 ml-1">
                            <path d="M16.59 8.59L12 13.17 7.41 8.59 6 10l6 6 6-6-1.41-1.41z"></path>
                        </svg>
                    </button>

                    <div id="filterDropdown" class="popover-custom shadow-md rounded-md" x-placement="bottom"
                        style="display: none; position: absolute; will-change: top, left; top: 36px; left: 32px;">
                        <div class="p-3 flex flex-col gap-2">
                            <div class="ModelFilterField flex items-center justify-between gap-3">
                                <span class="inline-block first-letter:uppercase flex-1">type</span>
                                <div class="flex-shrink-0" style="width: 180px;"><select
                                        class="border p-1 rounded w-full">
                                        <option value="">All</option>
                                        <option value="Presidential">Presidential</option>
                                        <option value="Standard">Standard</option>
                                        <option value="Economy">Economy</option>
                                    </select></div>
                            </div>
                            <div class="ModelFilterField flex items-center justify-between gap-3">
                                <span class="inline-block first-letter:uppercase flex-1">complex</span>
                                <div class="flex-shrink-0" style="width: 180px;">
                                    <div>
                                        <div>
                                            <div>
                                                <div data-position="below" class="autocomplete"
                                                    style="position: relative;"><input role="combobox"
                                                        autocomplete="off" autocapitalize="off" autocorrect="off"
                                                        spellcheck="false" aria-autocomplete="list"
                                                        aria-haspopup="listbox" aria-owns="autocomplete-result-list-2"
                                                        aria-expanded="false" aria-activedescendant=""
                                                        placeholder="Type to search..." class="autocomplete-input">
                                                    <ul id="autocomplete-result-list-2" role="listbox"
                                                        class="autocomplete-result-list"
                                                        style="position: absolute; z-index: 1; width: 100%; visibility: hidden; pointer-events: none; top: 100%;">
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="h-8 flex items-center px-3 bg-neutral-100 rounded-b-md"><button
                                class="underline text-xs font-semibold">Reset filters</button></div>
                    </div>
                </div>
                <div class="relative rounded-full">
                    <svg fill="currentColor"
                        class="absolute inset-y-0 ml-3 my-auto inset-left-0 w-6 h-6 text-content/50">
                        <path
                            d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                    </svg>

                    <input wire:model.live="q" type="search"
                        placeholder="Search {{ str($currentModel->name)->plural()->lower() }}..."
                        class="min-w-[250px] rounded-full py-1 pl-10 pr-3 bg-transparent border border-content/20 placeholder:text-content/30 outline-none" />
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto">
            <div class="p-12">
                {{-- @piermodel(['model' => $currentModel->name, 'q' => $q])
                    <livewire:table :fields="$model->fields" :rows="$data" />
                @endpiermodel --}}
                <livewire:table :key="$currentModel->name . $q" :model="$currentModel->name" :q="$q" />
            </div>
        </div>
    </main>

</div>
