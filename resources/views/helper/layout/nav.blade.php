<div class="bg-white shadow px-6 flex items-center">
    <h1 class="text-xl leading-none font-semibold">Pier Helper</h1>

    <div class="ml-8 flex rounded overflow-hidden border border-gray-300">
        <span class="flex items-center px-3 bg-gray-200/70 text-sm leading-none border">
            Model
        </span>
        <select class="w-40 border-none focus:outline-none" x-model="selectedModelId">
            <option value="">Choose One</option>

            <template x-for="model in models" :key="model._id">
                <option :value="model._id" :selected="selectedModelId == model._id" x-text="model.name">
                </option>
            </template>
        </select>
    </div>

    <div id="navItems" class="ml-8 space-x-3 flex h-16">
        <a href="{{ url('pier-helper') }}" class="px-2 flex items-center border-b-2 border-blue-500">Pier API</a>
        {{-- <a href="#" class="px-2 flex items-center border-b-2 border-transparent">Pier Data</a> --}}
        <a href="{{ url('pier-helper/data-grid') }}" class="px-2 flex items-center border-b-2 border-transparent">Pier
            Data Grid</a>
        {{-- <a href="#" class="px-2 flex items-center border-b-2 border-transparent">Pier CMS</a> --}}
        {{-- <a href="#" class="px-2 flex items-center border-b-2 border-transparent">Pier Form</a> --}}
    </div>
</div>

<script>
    document.querySelectorAll('#navItems a').forEach(node => {
        if (node.getAttribute("href") == window.location.href) {
            node.classList.add("border-blue-500");
            node.classList.remove("border-transparent");
        } else {
            node.classList.remove("border-blue-500");
            node.classList.add("border-transparent");
        }
    });
</script>
