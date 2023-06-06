<div class="bg-gray-200 h-screen overflow-y-auto">
    <div class="-mt-4 mb-6">
        <div class="bg-white shadow-sm fixed inset-x-0 py-3 z-10" style="top: 0">
            <div class="container mx-auto flex items-center">
                <button onclick="history.back"
                    class="inline-flex items-center text-xs uppercase tracking-wider border rounded pt-2 pb-1 px-3 hover:bg-gray-100">
                    <svg class="w-4 mr-3 mb-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>

                    Go Back
                </button>

                <div class="flex flex-1 pt-1 pr-48">
                    <span class="flex-1"></span>
                    <h2 class="text-xl font-medium">
                        {{ isset($id) ? "Edit $model Details" : "Add New $model" }}
                    </h2>
                    <span class="flex-1"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto py-16">
        <div class="bg-white rounded-xl shadow my-4 pt-2 pb-5 px-6 mx-auto w-full" style="max-width: 680px">
            <x-pier-form :model="$model" :row-id="$rowId ?? null" />
        </div>
    </div>
</div>
