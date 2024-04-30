@props(['value' => 0, 'max' => 5, 'xModel'])

{{-- @assets() --}}
    <script>
        window.pierDebouncer = (func, timeout = 300) => {
            let timer;
            return (...args) => {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    func.apply(this, args);
                }, timeout);
            };
        }
    </script>
{{-- @endassets --}}

<div @if ($xModel ?? null) x-modelable="value" x-model.debounce="{{ $xModel }}" @endif
    x-data='{
    xmodelSet: {{ json_encode($xModel ?? null != null) }},
    value: {{ $value }},
    _value: {{ $value }},
    max: {{ $max }},
    init() {
        this.$watch("_value", window.pierDebouncer((newValue) => {
            if((newValue ?? 0) !== (this.value ?? 0)) {
                this.xmodelSet ? this.value = newValue : this.$dispatch("input", newValue);
            }
        }));

        this.$watch("value", (newValue) => {
            if(newValue !== this._value) this._value = newValue ?? 0;
        });
    }
}'
    class="flex items-center gap-2">
    <input class="w-full" type="range" step="0.5" x-bind:min="0" x-bind:max="max"
        x-model.stop="_value" />

    <span class="h-7 w-10 flex items-center justify-center border-2 border-stroke rounded text-xs font-bold"
        x-text="_value"></span>
</div>

{{-- <style>
    .multirange-input::-webkit-slider-thumb {
        pointer-events: all;
        width: 24px;
        height: 24px;
        -webkit-appearance: none;
    }
</style>

<div class="flex justify-center items-center p-2">
    <div x-data="{
        minValue: 0,
        maxValue: 1,
        min: 0,
        max: 1,
        minthumb: 0,
        maxthumb: 0,
    
        mintrigger() {
            this.minValue = Math.min(this.minValue, this.maxValue - 0.5);
            this.minthumb = ((this.minValue - this.min) / (this.max - this.min)) * 100;
        },
    
        maxtrigger() {
            this.maxValue = Math.max(this.maxValue, this.minValue + 0.5);
            this.maxthumb = 100 - (((this.maxValue - this.min) / (this.max - this.min)) * 100);
        },
    
        reset() {
            this.minValue = 0;
            this.maxValue = 1;
            this.min = 0;
            this.max = 1;
            this.minthumb = 0;
            this.maxthumb = 0;
        },
    
        init() {
            this.mintrigger();
            this.maxtrigger();
        }
    }" x-on:reset-form.window="reset()" class="relative max-w-xl w-full">
        <div>
            <input type="range" step="0.1" x-bind:min="min" x-bind:max="max"
                x-on:input="mintrigger" x-model="minValue"
                class="multirange-input absolute pointer-events-none appearance-none z-20 h-2 w-full opacity-0 cursor-pointer">

            <input type="range" step="0.1" x-bind:min="min" x-bind:max="max"
                x-on:input="maxtrigger" x-model="maxValue"
                class="multirange-input absolute pointer-events-none appearance-none z-20 h-2 w-full opacity-0 cursor-pointer">

            <div class="relative z-10 h-2">
                <div class="absolute z-10 left-0 right-0 bottom-0 top-0 rounded-md bg-content/10"></div>

                <div class="absolute z-20 top-px bottom-0 rounded-md bg-primary"
                    x-bind:style="'right:' + maxthumb + '%; left:' + minthumb + '%'"></div>

                <div class="absolute z-30 w-4 h-4 top-1 left-0 bg-primary rounded-full -mt-2 -ml-1"
                    x-bind:style="'left: ' + minthumb + '%'"></div>

                <div class="absolute z-30 w-4 h-4 top-1 right-0 bg-primary rounded-full -mt-2 -mr-3"
                    x-bind:style="'right: ' + maxthumb + '%'"></div>
            </div>
        </div>

        <input type="hidden" x-bind:value="`${minValue},${maxValue}`" />

        <div class="flex justify-between items-center py-5">
            <div>
                <input type="text" maxlength="5" x-on:input="mintrigger" x-model="minValue"
                    class="flex items-center justify-center px-2 border border-stroke rounded h-10 w-24">
            </div>
            <div>
                <input type="text" maxlength="5" x-on:input="maxtrigger" x-model="maxValue"
                    class="flex items-center justify-center px-2 border border-stroke rounded h-10 w-24">
            </div>
        </div>
    </div>
</div> --}}
