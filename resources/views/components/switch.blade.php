@props(['checked' => null, 'xModel'])

<label x-data="{
    xmodelSet: {{ json_encode($xModel ?? null != null) }},
    checked: {{ json_encode($checked ?? null != null) }},
    init() {
        if (!this.xmodelSet) {
            this.$watch('checked', value => {
                this.$dispatch('input', value);
            });
        }
    }
}"
    class="cursor-pointer flex justify-start items-center first-letter:uppercase text-content/10" x-modelable="checked"
    x-model="{{ $xModel ?? null ? $xModel : 'checked' }}" x-on:click="checked = !checked">
    <span
        class="transition-colors w-10 bg-current rounded-full overflow-hidden relative flex items-center border border-current p-px"
        x-bind:class="checked ? 'text-primary' : 'text-content/10'">
        <span class="block rounded-full bg-white size-5 transition-transform"
            x-bind:class="checked && 'translate-x-4'"></span>
    </span>

    {{-- <span class="ml-3">
        {{ $label }}
    </span> --}}
</label>
