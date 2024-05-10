<meta name="csrf_token" value="{{ csrf_token() }}" />

@include('pier::theme')

@php
    $formId = 'pierForm' . pierRandomId();
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

        if (typeof $wire.submit == 'function') return await $wire.submit(data);

        return data;
    },
    showToast(message) {
        if (this.toastTimeout)
            clearTimeout(toastTimeout);
        if (message) this.message = message;
        this.showMessage = true;
        this.toastTimeout = setTimeout(() => {
            this.showMessage = false;
        }, 3000);
    },
    async onSubmit(e) {
        const form = e.target;
        const formFields = document.querySelectorAll(`[form='{{ $formId }}']`);

        if (!this.cachedValues) {
            this.cachedValues = Object.fromEntries(new FormData(form));
        }

        if (window.pierFormTimeout) {
            clearTimeout(window.pierFormTimeout);
            window.pierFormTimeout = null;
        }

        this.saving = true;

        window.pierFormTimeout = setTimeout(async () => {
            try {
                const res = await this.handleSave(Object.fromEntries(Object.entries(this.cachedValues)));

                if (!res) return;

                if (formFields)
                    formFields.forEach(node => node.value = '');

                if (this.successMessage?.length)
                    this.showToast(this.successMessage);
            } catch (error) {
                console.log('Error: ', error);
                this.showToast('An unkown error occured');
            } finally {
                this.cachedValues = null;
                this.saving = false;
            }
        }, 50);
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

            <x-pier::livewire.form.field :$field :$value :$formId :required="$mostFieldsRequired"
                on-change="e => values['{{ $field->label }}'] = e.detail" />
        @endforeach
    </div>

    <div class="my-7 flex">
        <button form="{{ $formId }}" type="submit"
            class="px-10 py-0 h-11 bg-primary text-white border-primary text-sm uppercase tracking-wide font-bold focus:outline-none rounded-lg hover:opacity-90"
            x-bind:class="{
                'pointer-events-none opacity-50': saving || uploadingFile,
            }"
            x-text="saving ? 'Please wait...' : 'SUBMIT'">
            SUBMIT
        </button>
    </div>
</div>
