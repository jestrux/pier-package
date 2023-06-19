@aware(['model'])

@php
    $model = !is_null($buttonModel) ? $buttonModel : $model ?? null;
@endphp

@unless (is_null($model))
    <a href="{{ url('/admin/upsertModel/' . $model) }}"
        {{ $attributes->merge([
            'type' => 'button',
            'class' =>
                'h-9 inline-flex items-center focus:outline-none px-2 leading-none hover:bg-black/5 transition-colors duration-300 border border-neutral-300 uppercase text-xs tracking-wide font-semibold bg-transparent rounded',
        ]) }}
        x-on:click="openUpsertModal"
    >

        <svg class="w-4 ml-0.5 mr-1 mb-px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span class="mr-1">
            @if (isset($slot) && strlen($slot) > 0)
                {{ $slot }}
            @else
                New {{ $model }}
            @endif
        </span>
    </a>
@endisset
