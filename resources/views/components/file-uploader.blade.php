@props(['value' => '', 'xModel', 'isFace' => false])

{{-- @assets() --}}
<script src="{{ asset('pier/js/file-uploader.umd.cjs') }}"></script>
{{-- @endassets --}}

@php
    $config = pierConfig();
@endphp

<div @if ($xModel ?? null) x-modelable="value" x-model="{{ $xModel }}" @endif
    class="group relative rounded-md overflow-hidden"
    x-data='{
        _preview: null,
        value: "{{ $value }}",
        isFace: {{ json_encode($isFace) }},
        uploadPercent: 0,
        error: "There was an error when uploading the file.",
        get preview() {
            return this.value || this._preview;
        },
        reset() {},
        init() {
            this.reset = FileUploader($el, {
                uploadUrl: "{{ $config->fileUploadUrl }}",
                s3: {!! json_encode($config->s3) !!},
                onChange: (data) => {
                    this.uploadPercent = data.progress;
                    this._preview = data.preview;
                    this.value = data.src;
                    this.error = data.error ?? "There was an error when uploading the file.";
                }
            });

            this.$watch("value", newValue => {
                $dispatch("input", newValue);
            });
        }
    }'>

    <div class="relative" x-show="!preview" x-on:input="e => e.stopPropagation()">
        <div class="border-4 border-dashed border-transparent group-data-[dragover=true]:border-content/10 bg-[--input-bg-color] transition text-center text-sm relative flex flex-col items-center justify-center p-4"
            style="min-height: 82px;"></div>

        <div class="absolute inset-0 flex flex-col items-center justify-center">
            <span class="opacity-40 group-data-[dragover=true]:opacity-80 transition text-sm">
                Drop your file here to upload it.
            </span>

            <label class="cusror-pointer relative text-sm/none text-primary px-1.5 py-2">
                or select a file
                <input type="file" hidden />
            </label>
        </div>
    </div>

    <div class="group-data-[status=loading]:block hidden bg-card absolute inset-0 z-10 transition">
        <div class="relative bg-[--input-bg-color] h-full flex flex-col gap-4 items-center justify-center">
            <svg class="animate-spin h-7 w-7" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>

            <div class="text-sm uppercase tracking-widest" x-text="`Uploading... ${uploadPercent}%`">
                Uploading...
            </div>
        </div>
    </div>

    <div
        class="group-data-[status=error]:flex hidden flex-col gap-1.5 items-center justify-center absolute inset-0 text-center bg-red-100 text-red-900">
        <div class="size-8 bg-black/10 rounded-full flex items-center justify-center">
            <svg class="size-4 mb-0.5" viewBox="0 0 24 24" fill="currentColor">
                <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
            </svg>
        </div>
        <p class="text-black/70 text-sm" x-text="error">There was an error when uploading the file.</p>
        <button type="button" x-on:click="reset()"
            class="absolute z-10 right-1 top-1 size-8 rounded-full opacity-50 hover:opacity-80 transition flex items-center justify-center">
            <svg class="w-6 h-6 text-black" viewBox="0 0 24 24" fill="currentColor"
                style="filter: drop-shadow(0 0 2px rgba(0,0,0,0.3));">
                <path fill-rule="evenodd"
                    d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z"
                    clip-rule="evenodd" />
            </svg>
        </button>
    </div>

    <template x-if="preview">
        <div class="relative bg-[--input-bg-color] p-3 rounded-lg border border-[--input-border-color)]">
            <a x-bind:href="preview" target="_blank" x-bind:title="preview"
                class="overflow-hidden flex items-center">
                <div class="h-14 flex-shrink-0 relative overflow-hidden mr-3 flex items-center justify-center"
                    x-bind:class="isFace ? 'aspect-square rounded-full' : 'aspect-[2/1.2] rounded'">
                    <img x-bind:src="preview" alt="" class="absolute inset-0 size-full object-cover" />
                </div>
                <div class="min-w-0">
                    <h3 class="text-lg/none truncate" x-text="preview">
                    </h3>
                    <p class="mt-2 text-sm truncate opacity-50">
                        ( Click to see image )
                    </p>
                </div>
            </a>

            <button type="button" x-on:click="reset()"
                class="absolute z-10 right-1 top-1 opacity-50 hover:opacity-80 transition flex items-center justify-center">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z"
                        clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </template>
</div>
