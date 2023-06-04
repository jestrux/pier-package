<link rel="stylesheet" href="{{ asset('pier/css/form.css') }}" />
<script src="https://cdn.tailwindcss.com"></script>

<style>
    :root {
        --primary-color: {{ env('APP_COLOR') ?? '#2c5282' }};
    }
</style>

@php
    $backUrl = env('PIER_FORM_REDIRECT_URL') ?? url('/admin/' . strtolower($model->name . 's'));
    
    if (isset($redirectTo)) {
        $backUrl = url($redirectTo);
    }
@endphp

@if ($plainForm ?? false)
    <div id="pierForm"></div>
@else
    <div class="bg-gray-200 h-screen overflow-y-auto">
        <div class="-mt-4 mb-6">
            <div class="bg-white shadow-sm fixed inset-x-0 py-3 z-10" style="top: 0">
                <div class="container mx-auto flex items-center">
                    <a href="{{ $backUrl }}"
                        class="inline-flex items-center text-xs uppercase tracking-wider border rounded pt-2 pb-1 px-3 hover:bg-gray-100">
                        <svg class="w-4 mr-3 mb-1" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>

                        Go Back
                    </a>

                    <div class="flex flex-1 pt-1 pr-48">
                        <span class="flex-1"></span>
                        <h2 class="text-xl font-medium">
                            {{ isset($id) ? "Edit $model->name Details" : "Add New $model->name" }}
                        </h2>
                        <span class="flex-1"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto py-16">
            <div class="bg-white rounded-xl shadow my-4 pt-2 pb-5 px-6 mx-auto" style="width: 600px">
                <div id="pierForm"></div>
            </div>
        </div>
    </div>
@endif

<script>
    (() => {
        const pierFormId = "{{ $formId ?? 'pierForm' }}";
        const pierModel = {
            ...({!! $model !!}),
            fields: {!! $model->fields !!},
            settings: {!! $model->settings !!}
        }
        let pierModelValues;

        @if ($values != null)
            pierModelValues = {!! collect($values)->toJson() !!};
        @endif

        if (window.PierFormLoaded)
            window.loadPierForm({
                pierFormId,
                pierModel,
                pierModelValues
            });
        else {
            window.addEventListener("PierForm:loaded", () => {
                window.loadPierForm = function({
                    pierFormId,
                    pierModel,
                    pierModelValues
                } = {}) {
                    PierForm(`#${pierFormId}`, {
                        pierModel,
                        pierModelValues,
                        "appName": "{{ env('APP_NAME') }}",
                        "unsplashClientId": "{{ env('PIER_UNSPLASH_CLIENT_ID') }}",
                        "fileUploadUrl": "{{ env('PIER_UPLOAD_DIR') != null && strlen(env('PIER_UPLOAD_DIR')) > 0 ? url('api/' . env('PIER_UPLOAD_DIR') . '/upload_file') : null }}",
                        "s3": {
                            bucketName: "{{ env('PIER_S3_BUCKET') }}",
                            region: "{{ env('PIER_S3_REGION') }}",
                            accessKeyId: "{{ env('PIER_S3_ACCESS_KEY_ID') }}",
                            secretAccessKey: "{{ env('PIER_S3_SECRET_ACCESS_KEY') }}",
                        },
                    });
                }

                window.PierFormLoaded = true;

                window.loadPierForm({
                    pierFormId,
                    pierModel,
                    pierModelValues
                });
            });
        }
    })()
</script>

<script src="{{ asset('pier/js/pier-form.js') }}"></script>
