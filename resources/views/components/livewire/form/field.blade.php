@props(['field' => (object) [], 'value' => '', 'formId' => '', 'onChange' => 'console.log'])

@assets()
    <style>
        :root {
            --input-bg-color: rgba(0, 0, 0, 0.05);
            --input-border-color: rgba(0, 0, 0, 0.02);
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --input-bg-color: rgba(255, 255, 255, 0.08);
                --input-border-color: rgba(255, 255, 255, 0.02);
            }
        }

        .pier-form-field .pier-label {
            font-size: 0.95rem;
            opacity: 0.9;
            margin-bottom: 0.1rem;
        }

        .pier-form-field .pier-input,
        .pier-form-field .pier-textarea,
        .pier-form-field .pier-select {
            font-size: 20px;
            width: 100%;
        }

        .pier-form-field .pier-label,
        .pier-form-field .pier-select,
        .pier-form-field .pier-textarea,
        .pier-form-field .pier-input {
            display: block;
            width: 100%;
        }

        .pier-form-field .pier-select,
        .pier-form-field .pier-textarea,
        .pier-form-field .pier-input {
            background-color: var(--input-bg-color);
            border: 1px solid var(--input-border-color);
            border-radius: 0.35rem;
            font-size: 1rem;
            line-height: 1.5rem;
            padding: 0.5rem 0.75rem;
            outline: none;
            /* box-shadow: inset 0 0 3px rgba(0, 0, 0, 0.3); */
        }

        .pier-form-field .pier-select,
        .pier-form-field .pier-input,
        .pier-form-field .pier-textarea {
            min-height: 3rem;
            font-size: 1.1rem;
            padding: 0.65rem 0.8rem;
        }

        .pier-form-field>.mt-6 {
            margin-top: -0.1rem !important;
        }

        #qlEditorWrapper .ql-container,
        #qlEditorWrapper .ql-editor {
            height: auto !important;
        }

        #qlEditorWrapper .ql-toolbar {
            border-radius: 0.35rem 0.35rem 0 0 !important;
        }

        #qlEditorWrapper .ql-container {
            border-radius: 0 0 0.35rem 0.35rem !important;
        }

        .PierTelInput .vti__dropdown {
            pointer-events: none;
        }

        @media only screen and (max-width: 680px) {
            .pier-form-fields .grid {
                display: flex;
                flex-direction: column;
            }
        }
    </style>
@endassets

@php
    $type = $field->type ?? 'text';
    $name = $field->name ?? '';
    $label = $field->label ?? '';
    $cleanLabel = $field->cleanLabel ?? '';
    $hideLabel = false;
    $meta = $field->meta ?? null;
    $required = $field->required ?? false;
    $width = $field->width ?? 'full';
    $stretch = $field->stretch ?? 'full';
    $hiddenOrAuth = collect(['hidden', 'auth'])->contains($type);
@endphp

@if ($type == 'group')
    <div class="col-span-12 border-b border-neutral-500 mt-4" style="position: {{ $hiddenOrAuth ? 'absolute' : '' }}">
        <h3 class="mb-2 text-lg leading-none font-semibold">
            {{ $cleanLabel ? $cleanLabel : $label }}
        </h3>
    </div>
@else
    @php
        $widthClass = fn($width) => ['col-span-6' => 'half', 'col-span-4' => 'third'][$width ?? ''] ?? 'col-span-12';
    @endphp

    <div x-data='{ 
        value: {{ !is_scalar($value) ? json_encode($value) : '"' . $value . '"' }},
        init() {
          this.$watch("value", newValue => {
            this.$el.dispatchEvent(
                new CustomEvent("change", {
                  detail: newValue,
                })
            );
          });
        }
      }'
        x-on:change.stop="{{ $onChange }}" class="flex flex-col md:grid grid-cols-12 {{ $widthClass($width) }}"
        style="position: {{ $hiddenOrAuth ? 'absolute' : '' }}">
        <div class="{{ $widthClass($stretch) }}" style="position: {{ $hiddenOrAuth ? 'absolute' : '' }}">
            <div class="pier-form-field relative" x-model="value">
                @unless ($hideLabel)
                    <label class="pier-label first-letter:uppercase" for="{{ $label }}">
                        {{ $label }}
                    </label>
                @endunless

                @php
                    $specialFields = collect(['hidden', 'auth', 'image', 'file', 'rating', 'boolean', 'status', 'reference']);
                @endphp

                @if (collect(['image', 'file'])->contains($type))
                    <x-pier::file-uploader :value="$value" :is-face="$meta->face" />
                @elseif ($type == 'reference')
                    <x-pier::combobox class="pier-input" :model="$meta->model" :value="$value" :displayField="$meta->mainField ?? 'label'" />
                @elseif ($type == 'rating')
                    <x-pier::multi-range :max="$meta->outOf" :value="$value ?? 0" />
                @elseif ($type == 'boolean')
                    @php
                        $choices = collect([['label' => '✅', 'value' => '1'], ['label' => '❌', 'value' => '0']]);
                    @endphp

                    <x-pier::radio :choices="$choices" :value="$value" />
                @elseif ($type == 'status')
                    @php
                        $choices = collect($meta->availableStatuses)->map(
                            fn($status) => [
                                'label' => $status->name,
                                'value' => $status->name,
                            ],
                        );
                    @endphp

                    <x-pier::radio :choices="$choices" :value="$value" />
                @endif

                @if ($specialFields->contains($type))
                    <input tabindex="-1" required="{{ $required }}" form="{{ $formId }}" type="text"
                        class="bg-transparent absolute -bottom-0 inset-x-0 opacity-0 pointer-events-none"
                        name="{{ $label }}"
                        x-bind:value="{{ $type == 'reference' ? 'value?._id' : 'value' }}" />
                @else
                    <input id="{{ $label }}" form="{{ $formId }}" class="pier-input"
                        name="{{ $label }}" required="{{ $required }}" type="{{ $type }}"
                        x-model="value" />
                @endif
            </div>
        </div>
    </div>
@endif

{{-- <template v-if="field">
      <input type="hidden" :name="field.label" :value="val" />
      
      <label
        v-if="!hideLabel"
        class="inline-block first-letter:uppercase"
        :for="field.label"
      >{{ (field.cleanLabel ? field.cleanLabel : field.label).replace(/_/g, ' ') }}</label>


      <bc-image-field
        v-if="field.type == 'image'"
        :label="field.cleanLabel ? field.cleanLabel : field.label"
        v-model="val"
        :url="val"
        :is-dp="field.meta.face"
        :required="field.required"
        :meta="field.meta"
      />
      
      <FileField
        v-else-if="field.type == 'file'"
        :label="field.cleanLabel ? field.cleanLabel : field.label"
        v-model="val"
        :url="val"
        :required="field.required"
      />

      <SwitchField
        v-else-if="field.type == 'boolean'"
        :label="field.cleanLabel ? field.cleanLabel : field.label"
        v-model="val"
        :required="field.required"
      />
      
      <StatusField v-else-if="field.type == 'status'"
        :label="field.cleanLabel ? field.cleanLabel : field.label"
        :meta="field.meta"
        v-model="val"
        :required="field.required"
      />
      
      <DateField
        v-else-if="field.type == 'date'"
        :field="field"
        v-model="val"
        :required="field.required"
      />
      
      <ReferenceField
        v-else-if="field.type == 'reference'"
        :label="field.cleanLabel ? field.cleanLabel : field.label"
        :reference-model="field.meta.model"
        :reference-model-main-field="field.meta.mainField"
        :add-reference-inline="field.meta.addInline"
        v-model="val"
        :required="field.required"
      />
      
      <MultiReferenceField
        v-else-if="field.type == 'multi-reference'"
        :label="field.cleanLabel ? field.cleanLabel : field.label"
        :field="field"
        v-model="val"
        :required="field.required"
      />
      
      <LinkField
        v-else-if="field.type == 'link'"
        :label="field.cleanLabel ? field.cleanLabel : field.label"
        show-preview
        v-model="val"
        :required="field.required"
      />
      
      <bc-youtube-field
        v-else-if="field.type == 'video'"
        :label="field.cleanLabel ? field.cleanLabel : field.label"
        v-model="val"
        :url="val"
        :required="field.required"
      />

      <vue-star-rating
        v-else-if="field.type === 'rating'"
        active-color="#e9b531"
        :increment="0.5"
        :max-rating="parseInt(field.meta.outOf)"
        :star-size="28"
        v-model="val"
        :required="field.required"
      />

      <PasswordField
        v-else-if="field.type == 'password'"
        :field="field"
        v-model="val"
        :required="field.required"
      />

      <PhoneField
        v-else-if="field.type == 'phone'"
        :label="field.cleanLabel ? field.cleanLabel : field.label"
        v-model="val"
        :required="field.required"
      />

      <LocationField
        v-else-if="field.type == 'location'"
        :label="field.cleanLabel ? field.cleanLabel : field.label"
        v-model="val"
        :meta="field.meta"
        :required="field.required"
      />

      <textarea 
        v-else-if="field.type === 'long text' && !field.meta.wysiwyg" 
        ref="longTextInput" 
        rows="1" 
        v-model="val" 
        :required="field.required"
      />

      <div id="qlEditorWrapper" v-else-if="field.type === 'long text' && field.meta.wysiwyg">
        <vue-editor
          :editorToolbar="wysiwygToolbar"
          v-model="val"
        />

        <placeholder-input :required="field.required" :value="val" />
      </div>

      <input
        v-else-if="field.type === 'number'"
        :id="field.label"
        :name="field.label"
        :required="field.required"
        type="number"
        v-model="val"
      />
      
      <input
        v-else-if="field.type != 'auth'"
        :id="field.label"
        :name="field.label"
        :required="field.required"
        :type="field.type"
        v-model="val"
      />
    </template> --}}
