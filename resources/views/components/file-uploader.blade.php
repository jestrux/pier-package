@props(['value' => '', 'onChange' => 'console.log'])

@assets()
    <script src="{{ asset('pier/js/file-uploader.umd.cjs') }}"></script>
@endassets

@php
    $config = pierConfig();
@endphp

<div id="pierFileUploader" class="group relative rounded-md overflow-hidden"
    x-data='{
        preview: null,
        value: "{{ $value }}",
        _value: "{{ $value }}",
        error: "There was an error when uploading the file.",
        reset() {},
        init() {
            this.reset = FileUploader($el, {
                uploadUrl: "{{ $config->fileUploadUrl }}",
                s3: {!! json_encode($config->s3) !!},
                onChange: (data) => {
                    this.progress = data.progress;
                    this.preview = data.preview;
                    this.value = data.src;
                    this.error = data.error ?? "There was an error when uploading the file.";
                }
            });

            this.$watch("value", newValue => {
                if (this._value == newValue) return;
    
                this.$el.dispatchEvent(
                    new CustomEvent("change", {
                        detail: newValue,
                    })
                );
    
                this._value = newValue;
            });
        }
    }'
    x-on:change="{{ $onChange }}">
    <div class="absolute inset-0 z-20" x-cloak x-show="value || preview">
        <img class="w-full h-full object-cover object-center" x-bind:src="value || preview" alt="" />

        <button type="button" x-on:click="reset()"
            class="absolute z-10 right-1 top-1 w-8 h-8 rounded-full hover:opacity-90 transition flex items-center justify-center">
            <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="currentColor"
                style="filter: drop-shadow(0 0 2px rgba(0,0,0,0.3));">
                <path fill-rule="evenodd"
                    d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z"
                    clip-rule="evenodd" />
            </svg>
        </button>
    </div>

    <div class="relative">
        <div id="fileUploaderDrop"
            class="border-4 border-dashed border-transparent group-data-[dragover=true]:border-content/10 bg-[--input-bg-color] transition text-center text-sm relative flex flex-col items-center justify-center p-4"
            style="min-height: 120px;"></div>

        <div class="absolute inset-0 flex flex-col gap-1 items-center justify-center">
            <span class="opacity-40 group-data-[dragover=true]:opacity-80 transition">
                Drop your file here to upload it.
            </span>

            <label class="cusror-pointer relative text-sm/none text-primary px-1.5 py-2">
                or select a file
                <input type="file" hidden />
            </label>
        </div>
    </div>

    <div id="fileUploaderLoader"
        class="group-data-[status=loading]:block hidden bg-card absolute inset-0 z-10 transition">
        <div class="relative bg-[--input-bg-color] h-full flex flex-col gap-4 items-center justify-center">
            <svg class="animate-spin h-7 w-7" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>

            <div class="text-sm uppercase tracking-widest">
                Uploading... 22%
            </div>
        </div>
    </div>

    <div
        class="group-data-[status=error]:flex hidden flex-col gap-3 items-center justify-center absolute inset-0 text-center bg-red-100 text-red-900">
        <div class="h-10 w-10 bg-black/10 rounded-full flex items-center justify-center">
            <svg class="h-5 mb-0.5" viewBox="0 0 24 24" fill="currentColor">
                <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
            </svg>
        </div>
        <p class="text-black/70" x-text="error">There was an error when uploading the file.</p>
        <button type="button" x-on:click="reset()"
            class="absolute z-10 right-1 top-1 w-8 h-8 rounded-full opacity-30 hover:opacity-50 transition flex items-center justify-center">
            <svg class="w-6 h-6 text-black" viewBox="0 0 24 24" fill="currentColor"
                style="filter: drop-shadow(0 0 2px rgba(0,0,0,0.3));">
                <path fill-rule="evenodd"
                    d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z"
                    clip-rule="evenodd" />
            </svg>
        </button>
    </div>
</div>
