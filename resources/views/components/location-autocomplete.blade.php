@props(['meta' => null, 'value' => null, 'xModel'])

@php
    $inputClass = $attributes->get('class');
    if (!$inputClass || strlen($inputClass) < 1) {
        $inputClass =
            'flex items-center justify-between gap-2 w-full px-2.5 h-10 bg-card border-2 border-stroke rounded-md focus:outline-none focus:ring-0 placeholder:text-content/20';
    }

    $countries = ($meta ?? (object) [])->countries ?? '';
@endphp

<div class="relative" @if ($xModel ?? null) x-modelable="value" x-model.debounce="{{ $xModel }}" @endif
    x-data='{
        xmodelSet: {{ json_encode($xModel ?? null != null) }},
        value: {{ $value }},
        countries: "{{ $countries }}",
        preview(value) {
            if(!value) return null;

            return {
                title: value.name,
            };
        },
        async search(query) {
            const countryFilter = this.countries?.length ? "&countrycodes="+this.countries : "";
            const results = await fetch(`https://nominatim.openstreetmap.org/search?q=${query}&format=json&limit=10&addressdetails=1${countryFilter}`).then(res => res.json());
        
            return results.map(({display_name, lon, lat}) => ({
                name: display_name,
                coords: [lon, lat]
            }));
        },
        init() {
            if(!this.xmodelSet) {
                this.$watch("value", value => {
                    this.$dispatch("input", value);
                });
            }
        },
    }'>

    <x-pier::combobox class="{{ $inputClass }}" :min-characters="3" x-model="value" />
</div>
