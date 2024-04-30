<meta name="csrf_token" value="{{ csrf_token() }}" />

@include('pier::theme', compact('appColor'))

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
                    <a href="{{ url('cms/' . $model->name) }}"
                        class="{{ $model->_id == $currentModel->_id ? 'bg-content/5 text-primary border-current' : 'border-transparent opacity-75' }} hover:bg-content/5 capitalize px-4 h-12 flex items-center border-l-[3px] text-base font-medium">
                        {{ str($model->name)->plural() }}
                    </a>
                </li>
            @endforeach
        </ul>
    </aside>


    <main class="flex-1 h-screen flex flex-col relative">
        <header class="flex items-center justify-between px-8 h-16 bg-card flex-shrink-0 border shadow-sm">

            @if ($upsert)
                <a href="{{ url('cms/' . $currentModel->name) }}"
                    class="flex items-center gap-2 my-3 font-bold capitalize text-xl">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        aria-hidden="true" data-slot="icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"></path>
                    </svg>

                    {{ str($currentModel->name)->plural() }}
                </a>

                <span class="mx-3 opacity-50">/</span>

                <span class="lowercase first-letter:uppercase inline-block">
                    {{ $rowId ? 'Edit Entry' : 'New entry' }}
                </span>
            @else
                <span class="mr-3 my-3 font-bold capitalize text-xl">
                    {{ str($currentModel->name)->plural() }}
                </span>

                <a href="{{ url('cms/' . $currentModel->name) }}/upsert"
                    class="border border-current flex font-semibold gap-1 items-center rounded-full text-primary text-sm leading-none hover:bg-neutral-200/50"
                    style="padding: 0.4rem 1rem;">
                    <svg height="18px" fill="currentColor" viewBox="0 0 24 24" class="-ml-1">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path>
                    </svg>
                    <span class="lowercase first-letter:uppercase inline-block">New entry</span>
                </a>
            @endif

            <span class="flex-1"></span>

            @unless ($upsert)
                <div class="flex items-center">
                    <x-pier::model-filters :model="$currentModel" />

                    <div class="relative rounded-full">
                        <svg fill="currentColor"
                            class="absolute inset-y-0 ml-3 my-auto inset-left-0 w-6 h-6 text-content/15">
                            <path
                                d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                        </svg>

                        <input wire:model.live="q" type="search"
                            placeholder="Search {{ str($currentModel->name)->plural()->lower() }}..."
                            class="min-w-[250px] rounded-full h-10 pl-10 pr-3 bg-transparent border-2 border-stroke placeholder:text-content/20 outline-none" />
                    </div>
                </div>
            @endunless
        </header>

        <div class="flex-1 overflow-y-auto">
            <div class="px-8 py-4">
                @if ($upsert)
                    <div
                        class="rounded-md bg-card shadow-sm border border-stroke max-w-3xl mx-auto pt-6 px-8 mt-8 mb-12">
                        <livewire:pier-form :modelName="$currentModel->name" :$rowId />
                    </div>
                @else
                    <div class="bg-card text-content/80">
                        @pierdata(['model' => $currentModel->name, 'q' => $q, ...$this->getFilters() ?? []])
                            <x-pier-table :$model :$fields :$data />
                        @endpierdata
                    </div>
                @endif
            </div>
        </div>
    </main>

</div>
