@props(['value' => null, 'minCharacters' => 0, 'xModel'])

@php
    $inputClass = $attributes->get('class');
    if (!$inputClass || strlen($inputClass) < 1) {
        $inputClass =
            'flex items-center justify-between gap-2 w-full px-2.5 h-10 bg-card border-2 border-stroke rounded-md focus:outline-none focus:ring-0 placeholder:text-content/20';
    }
@endphp

{{-- @assets() --}}
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
{{-- @endassets --}}

<div class="relative" @if ($xModel ?? null) x-model="{{ $xModel }}" x-modelable="value" @endif
    x-data='{
        xmodelSet: {{ json_encode($xModel ?? null != null) }},
        onChange(newValue){
            this.value = newValue;
            if(!this.xmodelSet) {
                this.$dispatch("input", newValue);
                this.$nextTick(() => this.$refs.input.focus());
            }
        },
        handleClear(){
            this.onChange(null);
            this.$nextTick(() => this.$refs.input.focus());
        },
        get selected() {
            if(typeof this.preview != "function") return null;

            return this.preview(this.value);
        },
        init() {
            autocomplete({
                onSelect: (value, input) => {
                    input.blur();
                    input.value = "";

                    this.onChange(value);
                },
                input: this.$refs.input,
                minLength: "{{ $minCharacters }}",
                showOnFocus: true,
                emptyMsg: "No results found",
                render: (item, currentValue) => {
                    if(typeof this.preview != "function") return null;

                    const row = this.preview(item, true) ?? {};
                    const wrapper = document.createElement("div");
                    const title = document.createElement("div");
                    title.textContent = row.title;

                    wrapper.className = "flex items-center gap-2";
                    title.className = "line-clamp-1";

                    if(row.image) {
                        const image = document.createElement("img");
                        let className = "-ml-1.5 h-6 object-cover bg-content/5 flex-shrink-0 ";
                        className += row?.meta?.face ? "aspect-square rounded-full" : "aspect-[1.4/1] rounded";
                        image.className = className;
                        image.src = row.image;

                        wrapper.appendChild(image);
                    }

                    wrapper.appendChild(title);
                    
                    return wrapper;
                    
                },
                className: "pier-combobox",
                fetch: (text, callback, trigger, cursorPos) => {
                    this.search(text).then(callback);
                },
            });
        },
    }'>

    <div class="relative" x-on:input="e => e.stopPropagation()">
        <input x-ref="input" x-bind:autofocus="!preview" class="{{ $inputClass }} placeholder:text-content/20"
            x-bind:style="{ opacity: value ? 0 : 1 }" placeholder="Type to search..."
            x-on:keydown="e => {if(e.key == 'Escape') e.target.value = '';}" />
    </div>

    <div class="{{ $inputClass }} absolute inset-0 !flex items-center justify-between"
        x-bind:class="{ 'opacity-0 pointer-events-none': !value }">
        <div class="min-h-full flex items-center gap-2">
            <img x-bind:class="!selected?.image && 'hidden'" class="-ml-1.5 h-8 object-cover bg-content/5 flex-shrink-0"
                x-bind:style="selected?.meta?.face ? 'aspect-ratio:1/1; border-radius: 50%;' :
                    'aspect-ratio:1.4/1; border-radius: 4px;'"
                x-bind:src="selected?.image" />

            <span class="line-clamp-1" x-text="selected?.title"></span>
        </div>

        <button type="button" x-bind:tabindex="!preview ? -1 : ''"
            class="flex-shrink-0 h-full px-1 text-primary text-xs uppercase tracking-wide font-medium"
            x-on:click="handleClear">Change</button>
    </div>
</div>
