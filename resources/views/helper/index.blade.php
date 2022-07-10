@extends('pier::helper.layout.index')

@section('content')
    <div class="p-4 flex items-start">
        @include('pier::helper.query-editor')

        <div class="ml-6 flex-1 min-w-0">
            <div>
                <div class="bg-white shadow border rounded overflow-hidden">
                    <div class="pt-5">
                        <div class="px-4">
                            <h3 class="mb-1 text-lg leading-none font-medium">URL</h3>
                            <div class="relative">
                                <input type="text" :value="apiUrl"
                                    class="w-full text-xl rounded border-2 border-gray-300 pointer-events-none" />
                                <button
                                    class="absolute inset-y-0.5 right-0.5 bg-gray-200 px-3 flex items-center justify-center text-sm leading-none">
                                    <svg class="w-7 h-7 -ml-1.5" viewBox="0 0 32 32" fill="none" stroke="currentColor"
                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                        aria-hidden="true">
                                        <path
                                            d="M13 10.75h-1.25a2 2 0 0 0-2 2v8.5a2 2 0 0 0 2 2h8.5a2 2 0 0 0 2-2v-8.5a2 2 0 0 0-2-2H19">
                                        </path>
                                        <path
                                            d="M18 12.25h-4a1 1 0 0 1-1-1v-1.5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v1.5a1 1 0 0 1-1 1ZM13.75 16.25h4.5M13.75 19.25h4.5">
                                        </path>
                                    </svg>

                                    Copy
                                </button>
                            </div>
                        </div>

                        <div class="mt-4 -mb-2 overflow-x-auto">
                            <pre class="max-h-[630px] overflow-y-auto"><code class="language-javascript" x-text="apiResponse"></code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
