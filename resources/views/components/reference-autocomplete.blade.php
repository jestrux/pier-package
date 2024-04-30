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
        xmodelSet: {{ json_encode($xModel ?? null != null) }},
        displayField: "{{ $mainField }}",
        modelName: "{{ $model }}",
        imageField: {{ json_encode($image == null ? null : $image) }},
        imageIsFace: {{ json_encode($meta->face ?? null != null) }},
        value: {{ json_encode($value) }},
        preview(value) {
            const title = value?.[this.displayField] ?? null;
            const image = value?.[this.imageField] ?? null;

            if(!title) return null;

            return {title, image, meta: {
                face: this.imageIsFace
            } };
        },
        async search(query) {
            try {
                return await fetch(
                    `{{ url('/api') }}/${this.modelName}/search?q=${query}`
                ).then(res => res.json());
            } catch (error) {
                console.log("Failed to search.", error);
            }
        },
        init() {
            if(!this.xmodelSet) {
                this.$watch("value", value => {
                    this.$dispatch("input", value);
                });
            }
        },
    }'>

    <x-pier::combobox class="{{ $inputClass }}" x-model="value" />
</div>
