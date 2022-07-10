<div class="w-[400px] sticky top-4 flex-shrink-0 bg-white shadow border rounded">
    <div>
        <div class="flex items-center justify-between mt-3.5 mb-3 px-4 mx-0.5">
            <h3 class="font-semibold">Fields to be returned</h3>

            <a-switch :value="[0, selectedModel?.fields.length].includes(pluck.length)"
                @input="$event.target.value ? pluck = [] : null" label="All fields" />
        </div>

        <hr />

        <div class="p-3 pt-0.5 space-y-2 bg-gray-100 border-b">
            <template x-for="field in selectedModel?.fields">
                <button class="mr-1.5 border px-2 py-1 rounded-full text-sm"
                    :class="pluck.includes(field.label) ? 'bg-blue-500 border-blue-300 text-white' : 'border-gray-400'"
                    @click="!pluck.includes(field.label) ? pluck.push(field.label) : pluck = pluck.filter(f => (f) != field.label)"
                    x-text="field.label">
                </button>
            </template>
        </div>
    </div>

    <div>
        <div class="flex items-center justify-between mt-3 mb-3.5 px-4 mx-0.5">
            <h3 class="font-semibold">Filters</h3>

            <button class="text-blue-500 underline" @click="searchQuery = ''">
                Clear filters
            </button>
        </div>

        <hr />

        <div class="px-3 pt-2 pb-2 space-y-2 bg-gray-100 border-b">
            <div
                class="pl-3.5 pr-2 h-12 bg-white border shadow-sm rounded capitalize flex items-center justify-between">
                <span class="text-sm font-medium">Search query</span>
                <input class="w-56 py-1 px-2.5 border-gray-300 bg-gray-50 rounded-sm placeholder-gray-400/80"
                    type="search" placeholder="Type here to search..." x-model="searchQuery" />
            </div>

            <div
                class="pl-3.5 pr-2 h-12 bg-white border shadow-sm rounded capitalize flex items-center justify-between">
                <span class="text-sm font-medium">Status</span>
                <span class="bg-blue-100 text-blue-900 text-xs py-1 px-2.5 rounded-full">active</span>
            </div>
        </div>
    </div>

    <div>
        <div class="flex items-center justify-between mt-3 mb-3.5 px-4 mx-0.5">
            <h3 class="font-semibold">Pagination</h3>

            <a-switch :value="currentPage != null" @input="togglePagination" />
        </div>

        <hr />

        <div x-show="currentPage != null" class="px-3 pt-2 pb-2 space-y-2 bg-gray-100 border-b">
            <div
                class="pl-3.5 pr-2 h-12 bg-white border shadow-sm rounded capitalize flex items-center justify-between">
                <span class="text-sm font-medium">Results per page</span>
                <input class="w-12 py-0 h-8 border border-gray-400 rounded-sm py-1.5 px-3 placeholder-gray-400"
                    x-model="perPage" placeholder="--" />
            </div>

            <div
                class="pl-3.5 pr-2 h-12 bg-white border shadow-sm rounded capitalize flex items-center justify-between">
                <span class="text-sm font-medium">Current page</span>

                <select x-model="currentPage"
                    class="w-16 border border-gray-400 rounded-sm py-1.5 px-3 placeholder-gray-400">
                    <template x-for="page in paginationPages">
                        <option :value="page" :selected="page == currentPage" x-text="page"></option>
                    </template>
                </select>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between mt-2.5 mb-3 px-4 mx-0.5">
        <h3 class="font-semibold">Group by</h3>

        <div class="flex items-center">
            <select x-model="groupBy">
                <option value="">Choose field</option>

                <template x-for="field in selectedModel?.fields" :key="field.label">
                    <option :value="field.label" :selected="groupBy == field.label" x-text="field.label">
                    </option>
                </template>
            </select>
        </div>
    </div>

    <hr />

    <div class="flex items-center justify-between mt-2.5 mb-3 px-4 mx-0.5">
        <h3 class="font-semibold">Order by</h3>

        <div class="flex items-center">
            <select x-model="orderBy">
                <option value="">Choose field</option>

                <template x-for="field in selectedModel?.fields" :key="field.label">
                    <option :value="field.label" :selected="orderBy == field.label" x-text="field.label">
                    </option>
                </template>
            </select>

            <a-switch class="ml-3" x-model="orderByAscend" label="Asc" />
        </div>
    </div>

    <hr />

    <div class="flex items-center justify-between mt-2.5 mb-3 px-4 mx-0.5"
        :class="{ 'opacity-30 pointer-events-none': currentPage != null }">
        <h3 class="font-semibold">Limit number of results</h3>

        <input class="w-12 py-0 h-8 border border-gray-400 rounded-sm py-1.5 px-3 placeholder-gray-400" x-model="limit"
            placeholder="--" />
    </div>

    <hr />

    <div class="flex items-center justify-between mt-3.5 mb-3.5 px-4 mx-0.5">
        <h3 class="font-semibold">Randomize results</h3>

        <a-switch x-model="randomize" />
    </div>
</div>
