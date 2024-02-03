@props(['choices' => [], 'value' => '', 'onChange' => 'console.log'])

<div x-data="{
    value: '{{ $value ?? '' }}',
    _value: '{{ $value ?? '' }}',
    choices: {!!$choices!!},
    reset() {
        this.value = '';
        this._value = '';
    },
    init() {
        console.log({!!$choices!!});
        this.$watch('value', newValue => {
            if (this._value == newValue) return;

            this.$el.dispatchEvent(
                new CustomEvent('change', {
                    detail: newValue,
                })
            );

            this._value = newValue;
        });
    }
}" x-radio x-model="value" x-on:reset-filters.window="reset()" class="w-full"
    x-on:change="{{ $onChange }}">
    <!-- Radio Label -->
    {{-- <label x-radio:label class="sr-only">Backend framework: <span x-text="value"></span></label> --}}

    <div class="flex items-center flex-wrap gap-4 -mx-1.5">
        <template x-for="(choice, index) in choices" x-bind:key="index">
            <div x-radio:option x-bind:value="choice.value"
                class="flex items-center gap-2 cursor-pointer px-2 py-1 rounded focus:outline-none focus:ring-1 ring-content/20"
                :class="{ 'shadow': $radioOption.isChecked }">
                <span :class="$radioOption.isChecked ? 'border-primary' : 'border-content/50'"
                    class="inline-flex p-0.5 h-4 w-4 shrink-0 items-center justify-center rounded-full border"
                    aria-hidden="true">

                    <span :class="{ 'bg-primary': $radioOption.isChecked }" class="w-full h-full rounded-full"></span>
                </span>

                <span>
                    <p x-radio:label x-text="choice.label"></p>
                    {{-- <span x-radio:description>Some</span> --}}
                </span>
            </div>
        </template>
    </div>
</div>
