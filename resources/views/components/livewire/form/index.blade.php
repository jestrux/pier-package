@assets()
    <style>
        @media only screen and (max-width: 680px) {
            .pier-form-fields .grid {
                display: flex;
                flex-direction: column;
            }
        }

        .pier-toast-message {
            background: #333;
            color: white;
            font-size: 0.9rem;
            padding: 0.8rem 1rem;
            border-radius: 4px;
            pointer-events: none;

            display: inline-flex;
            align-items: center;

            position: fixed;
            top: 1rem;
            left: 50%;
            z-index: 9999;

            transform: translateX(-50%);
            transition: all 0.15s ease-in-out;
        }

        .pier-toast-message span {
            color: greenyellow;
        }

        .pier-toast-message svg {
            fill: currentColor;
        }

        .pier-toast-message:not(.show) {
            transform: translateX(-50%) translateY(-80%);
            opacity: 0;
        }
    </style>
@endassets

@php
    $formId = 'pierForm' . bin2hex(random_bytes(6));
@endphp

<div x-data="{
    saving: false,
    toastTimeout: null,
    successMessage: `{{ $successMessage ?? null }}`,
    message: '',
    showMessage: false,
    get uploadingFile() {
        return false;
    },
    values: {{ json_encode($values ?? []) }},
    onSave: `{{ $onSave ?? null }}`,
    async handleSave(data) {
        if (this.onSave) {
            const onSave = eval(this.onSave);
            return onSave.apply(null, [data, this.$el]);
        }
        {{-- return await $wire.submit(data); --}}
    },
    showToast(message) {
        if (this.toastTimeout) clearTimeout(toastTimeout);

        if (message) this.message = message;

        this.showMessage = true;

        this.toastTimeout = setTimeout(() => {
            this.showMessage = false;
        }, 3000);
    },
    async onSubmit(e) {
        const form = e.target;
        const formFields = document.querySelectorAll(`[form='{{ $formId }}']`);

        try {
            this.saving = true;
            const res = await this.handleSave(Object.fromEntries(new FormData(form)));

            if (!res) return;

            if (formFields)
                formFields.forEach(node => node.value = '');

            if (this.successMessage?.length)
                this.showToast(this.successMessage);
        } catch (error) {
            console.log('Error: ', error);
            this.showToast('An unkown error occured');
        } finally {
            this.saving = false;
        }
    }
}">
    <form id="{{ $formId }}" action="#" method="POST" x-on:submit.prevent="onSubmit($event)"></form>

    <div x-bind:class="{ 'show': showMessage }" class="pier-toast-message" x-text="message">
        We've received your message, we'll get back to you.
    </div>

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
            class="w-full px-12 py-0 h-12 bg-primary text-white border-primary text-sm uppercase tracking-wide font-bold focus:outline-none rounded-lg hover:opacity-90"
            x-bind:class="{
                'pointer-events-none opacity-50': saving || uploadingFile,
            }"
            x-text="saving ? 'Please wait...' : 'SUBMIT'">
            SUBMIT
        </button>
    </div>
</div>
