@props([
    'field' => null,
    'value' => null,
])

@include('pier::theme')

<div class="pier-form-fields">
    <x-pier::livewire.form.field :$field />
    {{-- <livewire:pier-form-field :$field /> --}}
</div>
