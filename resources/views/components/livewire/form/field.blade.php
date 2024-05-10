@props(['field' => (object) [], 'required' => null, 'value' => null, 'formId' => '', 'onChange' => 'console.log'])

@php
    $type = $field->type ?? 'text';
    $label = $field->label ?? '';
    $name = $field->name ?? $label;
    $value = $value ?? ($field->value ?? null);
    $onChange = $onChange ?? ($field->onChange ?? '');
    $cleanLabel = $field->cleanLabel ?? '';
    $hideLabel = false;
    $meta = $field->meta ?? null;
    $required = $field->required ?? $required;
    $width = $field->width ?? 'full';
    $stretch = $field->stretch ?? 'full';
    $hiddenOrAuth = collect(['hidden', 'auth'])->contains($type);
@endphp

@if ($type == 'group')
    <div class="col-span-12 border-b border-neutral-500 mt-4" {{ $hiddenOrAuth ? 'style="position: absolute"' : '' }}>
        <h3 class="mb-2 text-lg leading-none font-semibold">
            {{ $cleanLabel ? $cleanLabel : $label }}
        </h3>
    </div>
@else
    @php
        $widthClass = fn($width) => ['half' => 'col-span-6', 'third' => 'col-span-4'][$width ?? ''] ?? 'col-span-12';
    @endphp

    <div x-data='{ 
        value: {{ ($type == 'location' ? $value ?? null : !is_scalar($value)) ? json_encode($value) : '"' . $value . '"' }},
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
        {{ $hiddenOrAuth ? 'style="position: absolute"' : '' }}>
        <div class="{{ $widthClass($stretch) }}" {{ $hiddenOrAuth ? 'style="position: absolute"' : '' }}>
            <div class="pier-form-field relative {{ $required ? 'required' : '' }}" x-model="value">
                @unless ($hideLabel)
                    <label class="pier-label first-letter:uppercase"
                        for="{{ $label }}">{{ str_replace(['_', '-'], ' ', $label) }}</label>
                @endunless

                @php
                    $specialFields = collect([
                        'hidden',
                        'auth',
                        'image',
                        'file',
                        'rating',
                        'boolean',
                        'status',
                        'radio',
                        'location',
                        'reference',
                        'multi-reference',
                    ]);
                @endphp

                @if (collect(['image', 'file'])->contains($type))
                    <x-pier::file-uploader :value="$value" :is-face="$meta->face ?? null" />
                @elseif ($type == 'reference')
                    <x-pier::reference-autocomplete class="pier-input" :$meta :$value />
                @elseif ($type == 'multi-reference')
                    <x-pier::multi-reference-autocomplete class="pier-input" :$meta :$value />
                @elseif ($type == 'location')
                    <x-pier::location-autocomplete class="pier-input" :$meta :$value />
                @elseif ($type == 'rating')
                    <x-pier::multi-range :max="$meta->outOf ?? 5" :value="$value ?? 0" />
                @elseif ($type == 'boolean')
                    <x-pier::switch :checked="$value == '1'" />
                    {{-- @php
                        $choices = collect([['label' => '✅', 'value' => '1'], ['label' => '❌', 'value' => '0']]);
                    @endphp

                    <x-pier::radio :choices="$choices" :value="$value" /> --}}
                @elseif ($type == 'status')
                    @php
                        $choices = collect($meta->availableStatuses ?? [])->map(
                            fn($status) => [
                                'label' => $status->name,
                                'value' => $status->name,
                            ],
                        );
                    @endphp

                    <x-pier::radio :$choice :$value />
                @elseif($type == 'radio')
                    @php
                        $choices = collect($meta->choices ?? [])->map(
                            fn($choice) => [
                                'label' => $choice->label ?? $choice,
                                'value' => $choice->value ?? $choice,
                            ],
                        );
                    @endphp

                    <div x-model="value">
                        <x-pier::radio :$choices :$value />
                    </div>
                @endif

                @if ($specialFields->contains($type))
                    <input tabindex="-1" {{ $required ? 'required' : '' }} form="{{ $formId }}" type="text"
                        class="bg-transparent absolute -bottom-0 inset-x-0 opacity-0 pointer-events-none"
                        name="{{ $name }}"
                        x-bind:value="{{ $type == 'reference' ? 'value?._id' : 'value' }}" />
                @elseif($type == 'select')
                    @php
                        $choices = collect($meta->choices ?? [])->map(
                            fn($choice) => [
                                'label' => $choice->label ?? $choice,
                                'value' => $choice->value ?? $choice,
                            ],
                        );
                    @endphp

                    <div
                        class="relative after:content-['\2304'] after:text-xl after:absolute after:right-3.5 after:bottom-3.5
                    after:pointer-events-none">
                        <select id="{{ $label }}" form="{{ $formId }}"
                            class="pier-select appearance-none" name="{{ $name }}"
                            {{ $required ? 'required' : '' }} x-model="value">
                            <option value="">Choose One</option>

                            @foreach ($choices as $choice)
                                <option value="{{ $choice['value'] }}">{{ $choice['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                @elseif($type == 'long text')
                    <textarea id="{{ $label }}" form="{{ $formId }}" class="pier-textarea" name="{{ $name }}"
                        {{ $required ? 'required' : '' }} type="{{ $type }}" rows="1" x-model="value"></textarea>
                @else
                    <input id="{{ $label }}" form="{{ $formId }}" class="pier-input"
                        name="{{ $name }}" {{ $required ? 'required' : '' }} type="{{ $type }}"
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
