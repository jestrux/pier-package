@props(['choices' => [], 'value' => ''])

@assets()
    <script defer src="https://unpkg.com/@alpinejs/ui@3.13.3-beta.4/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/@alpinejs/focus@3.13.3/dist/cdn.min.js"></script>
@endassets

<div x-data="{
    value: '{{ $value ?? '' }}',
    choices: {!! $choices !!},
    init() {
        this.$watch('value', newValue => {
            this.$dispatch('input', newValue);
        });
    }
}" x-radio x-model="value" class="w-full">
    <!-- Radio Label -->
    {{-- <label x-radio:label class="sr-only">Backend framework: <span x-text="value"></span></label> --}}

    <div class="flex items-center flex-wrap gap-2 -mx-1.5">
        <template x-for="(choice, index) in choices" x-bind:key="index">
            <div x-radio:option x-bind:value="choice.value"
                class="flex items-center gap-2 cursor-pointer px-2 py-1 rounded focus:outline-none focus:ring-1 ring-content/20">
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
