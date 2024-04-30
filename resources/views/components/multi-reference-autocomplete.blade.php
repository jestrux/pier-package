@props(['model' => null, 'meta' => null, 'value' => null, 'xModel'])

@php
    $inputClass = $attributes->get('class');
    if (!$inputClass || strlen($inputClass) < 1) {
        $inputClass =
            'flex items-center justify-between gap-2 w-full px-2.5 h-10 bg-card border-2 border-stroke rounded-md focus:outline-none focus:ring-0 placeholder:text-content/20';
    }

    $meta = $meta ?? (object) [];
    $model = $model ?? ($meta->model ?? '');
    $mainField = $meta->mainField ?? 'label';
    $image = null;

    if (!in_array(null, [$meta->type ?? null, $meta->field ?? null])) {
        if ($meta->type == 'image') {
            $image = $meta->field;
        }
    }
@endphp

<div class="relative" @if ($xModel ?? null) x-modelable="value" x-model.debounce="{{ $xModel }}" @endif
    x-data='{
        newValue: "",
        xmodelSet: {{ json_encode($xModel ?? null != null) }},
        displayField: "{{ $mainField }}",
        modelName: "{{ $model }}",
        imageField: {{ json_encode($image == null ? null : $image) }},
        imageIsFace: {{ json_encode($meta->face ?? null != null) }},
        defaultValue: {{ json_encode($value) }},
        allValues: [],
        addItem(e) {
            e.stopPropagation();
            if(e.detail)
                this.allValues.push(e.detail);
        },
        removeItem(itemId) {
            this.allValues = this.allValues.filter(item => item._id != itemId);
        },
        preview(value) {
            const title = value?.[this.displayField] ?? null;
            const image = value?.[this.imageField] ?? null;

            if(!title) return null;

            return {_id: value._id, title, image, meta: {
                face: this.imageIsFace
            } };
        },
        get values() {
            if(!this.allValues) return [];

            return this.allValues.map(e => {
                return this.preview(e);
            }).filter(v => v);
        },
        async search(query) {
            const existingValueIds = this.allValues.map(e => e?._id).filter(v => v);

            try {
                return await fetch(
                    `{{ url('/api') }}/${this.modelName}/search?q=${query}`
                ).then(res => res.json()).then(res => res.filter(e => !existingValueIds.includes(e._id)));
            } catch (error) {
                console.log("Failed to search.", error);
            }
        },
        init() {
            if(this.defaultValue) {
                this.allValues = [...this.defaultValue];
            }

            if(!this.xmodelSet) {
                {{-- this.$watch("allValues", value => {
                    this.$dispatch("input", value);
                }); --}}
            }
        },
    }'
    x-on:input="addItem($event)">

    <div class="pier-input">
        <div class="flex items-center gap-2 flex-wrap">
            <template x-for="(item) in values">
                <div
                    class="mr-1 mb-px max-w-32 relative flex items-center rounded-full bg-content/5 border border-stroke px-3 h-7">
                    <span class="absolute inset-y-0 -left-1 aspect-square" x-bind:class="!item.image && 'hidden'">
                        <img x-bind:src="item.image"
                            class="relative inline-block size-full rounded-full object-cover object-center" />
                    </span>

                    <span class="pl-4 flex-1 text-xs/none font-medium line-clamp-1" x-text="item.title">
                    </span>

                    <button class="ml-1 -mr-1" type="button" x-on:click="removeItem(item._id)">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </template>

            <div x-data="{ value: null }" class="relative flex-1 !min-w-[200px]" x-on:input="value = null">
                <x-pier::combobox
                    class="w-full bg-transparent focus:outline-none focus:ring-0 placeholder:text-content/20" />
            </div>
        </div>
    </div>
</div>
