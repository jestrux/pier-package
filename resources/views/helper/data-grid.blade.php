@extends('pier::helper.layout.index')

@section('content')
    <div class="p-4 flex items-start">
        @include('pier::helper.query-editor')

        <div class="ml-6 flex-1 min-w-0">
            <div class="p-4 bg-white shadow border rounded overflow-hidden">
                <div class="px-5 pt-5 pb-3.5 mb-5 rounded bg-gray-100s border border-gray-400/70 text-sm">
                    <h3 class="text-2xl leading-none font-semibold mb-4">Map fields to template</h3>
                    <div class="flex flex-wrap">
                        <div class="mr-2 mb-2 flex border border-gray-400 rounded-full overflow-hidden">
                            <span class="flex items-center pl-3 pr-2 border-r bg-gray-100">Image</span>
                            <select x-model="dataGridFieldMap.image" class="text-sm border-none">
                                <option value="">Choose field</option>

                                <template x-for="field in selectedModel?.fields">
                                    <option :value="field.label" :selected="dataGridFieldMap.image == field.label"
                                        x-text="field.label">
                                    </option>
                                </template>
                            </select>
                        </div>
                        <div class="mr-2 mb-2 flex border border-gray-400 rounded-full overflow-hidden">
                            <span class="flex items-center pl-3 pr-2 border-r bg-gray-100">Meta</span>
                            <select x-model="dataGridFieldMap.meta" class="text-sm border-none">
                                <option value="">Choose field</option>

                                <template x-for="field in selectedModel?.fields">
                                    <option :value="field.label" :selected="dataGridFieldMap.meta == field.label"
                                        x-text="field.label">
                                    </option>
                                </template>
                            </select>
                        </div>
                        <div class="mr-2 mb-2 flex border border-gray-400 rounded-full overflow-hidden">
                            <span class="flex items-center pl-3 pr-2 border-r bg-gray-100">Title</span>
                            <select x-model="dataGridFieldMap.title" class="text-sm border-none">
                                <option value="">Choose field</option>

                                <template x-for="field in selectedModel?.fields">
                                    <option :value="field.label" :selected="dataGridFieldMap.title == field.label"
                                        x-text="field.label">
                                    </option>
                                </template>
                            </select>
                        </div>
                        <div class="mr-2 mb-2 flex border border-gray-400 rounded-full overflow-hidden">
                            <span class="flex items-center pl-3 pr-2 border-r bg-gray-100">Description</span>
                            <select x-model="dataGridFieldMap.description" class="text-sm border-none">
                                <option value="">Choose field</option>

                                <template x-for="field in selectedModel?.fields">
                                    <option :value="field.label" :selected="dataGridFieldMap.description == field.label"
                                        x-text="field.label">
                                    </option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="-mx-4" />

                <div class="pt-5 pb-7 -mb-4 max-h-[600px] overflow-y-auto" x-html="dataGridContent"></div>
            </div>
        </div>
    </div>
@endsection
