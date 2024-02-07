@props(['model', 'value' => null, 'displayField' => 'label', 'xModel'])

@php
    $inputClass = $attributes->get('class');
    if (!$inputClass || strlen($inputClass) < 1) {
        $inputClass = 'flex items-center justify-between gap-2 w-full px-2.5 h-10 bg-card border-2 border-stroke rounded-md focus:outline-none focus:ring-0 placeholder:text-content/20';
    }
@endphp

@assets()
    <style>
        .pier-combobox {
            background: rgb(var(--card-color));
            color: rgb(var(--content-color)/0.5);
            position: absolute;
            z-index: 10;
            max-height: 15rem;
            margin-top: 0.3rem;
            width: inherit;
            transform-origin: top right;
            overflow: auto;
            border-radius: 0.375rem;
            border-width: 2px;
            --tw-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --tw-shadow-colored: 0 4px 6px -1px var(--tw-shadow-color), 0 2px 4px -2px var(--tw-shadow-color);
            box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
            outline: 2px solid transparent;
            outline-offset: 2px;
        }

        .pier-combobox .empty,
        .pier-combobox [role="option"] {
            display: flex;
            width: 100%;
            cursor: default;
            align-items: center;
            gap: 0.5rem;
            padding-left: 1rem;
            padding-right: 1rem;
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
            cursor: pointer;
        }

        .pier-combobox [role="option"][aria-selected="true"],
        .pier-combobox [role="option"]:hover {
            background-color: rgb(var(--content-color)/0.1);
            color: rgb(var(--content-color));
        }

        .pier-combobox .empty {
            opacity: 0.5;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/autocompleter"></script>
@endassets

<div class="relative" @if ($xModel ?? null) x-modelable="value" x-model.debounce="{{ $xModel }}" @endif
    x-data='{
        xmodelSet: {{ json_encode($xModel ?? null != null) }},
        displayField: "{{ $displayField }}",
        modelName: "{{ $model }}",
        value: {{ json_encode($value) }},
        get selected() {
            return this.value;
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
        handleClear(){
            this.value = null;
            this.$nextTick(() => this.$refs.input.focus());
        },
        init() {
            this.setup();

            if(!this.xmodelSet) {
                this.$watch("value", value => {
                    this.$dispatch("input", value);
                });
            }
        },
        setup(){
            autocomplete({
                onSelect: (value, input) => {
                    this.value = value;
                    input.blur();
                    input.value = "";
                },
                input: this.$refs.input,
                minLength: 0,
                showOnFocus: true,
                emptyMsg: "No results found",
                render: (item, currentValue) => {
                    var div = document.createElement("div");
                    div.textContent = item[this.displayField];
                    return div;
                },
                className: "pier-combobox",
                fetch: (text, callback, trigger, cursorPos) => {
                    this.search(text).then(callback);
                },
            });
        },
    }'>

    <div class="relative" x-on:input="e => e.stopPropagation()">
        <input x-ref="input" class="{{ $inputClass }} placeholder:text-content/20" placeholder="Type to search..."
            x-bind:style="{ opacity: selected ? 0 : 1 }"
            x-on:keydown="e => {if(e.key == 'Escape') e.target.value = '';}" />
    </div>

    <div class="{{ $inputClass }} absolute inset-0 !flex items-center justify-between"
        x-bind:class="{ 'opacity-0 pointer-events-none': !selected }">
        <span class="truncate" x-text="selected?.[displayField]"></span>
        <button type="button" x-bind:tabindex="!selected ? -1 : ''"
            class="flex-shrink-0 h-full px-1 text-primary text-xs uppercase tracking-wide font-medium"
            x-on:click="handleClear">Change</button>
    </div>
</div>
