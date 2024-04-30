@props(['choices' => [], 'value' => '', 'xModel'])

{{-- @assets() --}}
<script defer src="https://unpkg.com/@alpinejs/ui@3.13.3-beta.4/dist/cdn.min.js"></script>
<script defer src="https://unpkg.com/@alpinejs/focus@3.13.3/dist/cdn.min.js"></script>
{{-- @endassets --}}

@php
    $fieldId = 'pierForm' . pierRandomId();
@endphp

<div x-data="{
    xmodelSet: {{ json_encode($xModel ?? null != null) }},
    value: '{{ $value ?? '' }}',
    choices: {!! $choices !!},
    init() {
        if (!this.xmodelSet) {
            this.$watch('value', value => {
                this.$dispatch('input', value);
            });
        }
    }
}" x-radio @if (!($xModel ?? null))
    x-model="value"
@else
    x-modelable="value" x-model="{{ $xModel }}"
    @endif class="w-full">
    <div class="flex items-center flex-wrap gap-2 -mx-1.5">
        @foreach ($choices as $choice)
            <div x-radio:option value="{{ $choice['value'] }}"
                class="flex items-center gap-2 cursor-pointer px-2 py-1 rounded focus:outline-none focus:ring-1 ring-content/20">
                <span x-bind:class="$radioOption.isChecked ? 'border-primary' : 'border-content/50'"
                    class="inline-flex p-0.5 h-4 w-4 shrink-0 items-center justify-center rounded-full border"
                    aria-hidden="true">

                    <span x-bind:class="{ 'bg-primary': $radioOption.isChecked }"
                        class="w-full h-full rounded-full"></span>
                </span>

                <span>
                    <p x-radio:label>
                        {{ $choice['label'] }}
                    </p>
                </span>
            </div>
        @endforeach
    </div>
</div>
