@assets()
    <style>
        @media only screen and (max-width: 680px) {
            .pier-form-fields .grid {
                display: flex;
                flex-direction: column;
            }
        }
    </style>
@endassets


<div x-data="{ values: $wire.values ?? [] }">
    @php
        $formId = 'pierForm' . bin2hex(random_bytes(6));
    @endphp

    <form id="{{ $formId }}" action="#" method="POST"
        x-on:submit.prevent="console.log(Object.fromEntries(new FormData($event.target)))"></form>

    <div class="pier-form-fields flex flex-col md:grid grid-cols-12 gap-5">
        @foreach ($fields as $_field)
            @php
                $field = (object) $_field;
                $value = $values[$field->label] ?? '';
            @endphp

            <x-pier::livewire.form.field :$field :$value :$formId
                on-change="e => values['{{ $field->label }}'] = e.detail" />
        @endforeach
    </div>

    <div class="my-7 flex justify-end">
        <button form="{{ $formId }}" type="submit"
            class="w-full px-12 py-0 h-12 bg-primary text-white border-primary text-sm uppercase tracking-wide font-bold focus:outline-none rounded-lg hover:opacity-90">
            SUBMIT
        </button>
    </div>
</div>
