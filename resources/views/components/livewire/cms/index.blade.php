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
                <div
                    class="-mt-10 w-16 h-16 p-3.5 bg-white rounded-full overflow-hidden flex items-center justify-center">
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

            <div class="mr-8 flex items-center">
                <x-pier::model-filters :model="$currentModel" />

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
            <div class="p-8">
                @pierdata(['model' => $currentModel->name, 'q' => $q, ...$filters ?? []])
                    <div class="bg-card text-content/80">
                        <x-pier-table :$fields :$data />
                    </div>
                @endpierdata
                {{-- <livewire:pier-table :model="$currentModel->name" :q="$q" /> --}}
            </div>
        </div>
    </main>

</div>
