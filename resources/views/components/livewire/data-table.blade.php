<div class="flex flex-col gap-8">
    <div class="flex items-center justify-between text-content">
        <h3 class="text-xl/none font-bold">{{ $modelDetails->name }}</h3>

        <input type="search" class="bg-content/[0.01] border border-content/10 focus:bg-transparent focus:border-content/20 placeholder:text-content/20 rounded h-10 px-3 min-w-[300px] focus:outline-none" placeholder="Type to search..."
            wire:model.live="q" />
    </div>

    <div class="bg-card text-content/80">
        <livewire:pier-table :$fields :$data />
    </div>
</div>
