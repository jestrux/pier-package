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
    
    $plainForm = $plainForm ?? false;
    $successMessage = $successMessage ?? "$model->name created";
    $formId = $formId ?? 'pierForm';
@endphp

<div id="{{ $formId }}"></div>

<script>
    (() => {
        const pierFormId = "{{ $formId }}";
        const pierModel = {
            ...({!! $model !!}),
            fields: {!! $model->fields !!},
            settings: {!! $model->settings !!}
        }
        let pierModelValues;

        @if ($values ?? null != null)
            pierModelValues = {!! collect($values)->toJson() !!};
        @endif

        document.addEventListener("pier-form-success", function({
            detail: {
                data,
                el
            }
        }) {
            el.querySelectorAll("input, textarea").forEach(node => node
                .value = "");
        }, false);

        if (window.PierFormLoaded) {
            window.loadPierForm({
                pierFormId,
                pierModel,
                pierModelValues,
                onPierFormSuccess: "{!! $onSave ?? null !!}",
            });
        }

        window.addEventListener("PierForm:loaded", () => {
            window.loadPierForm = function({
                pierFormId,
                pierModel,
                pierModelValues,
                ...rest
            } = {}) {
                PierForm(`#${pierFormId}`, {
                    pierModel,
                    pierModelValues,
                    "redirectTo": "{{ $redirectTo }}",
                    "successMessage": "{{ $successMessage }}",
                    "appName": "{{ env('APP_NAME') }}",
                    "unsplashClientId": "{{ env('PIER_UNSPLASH_CLIENT_ID') }}",
                    "fileUploadUrl": "{{ env('PIER_UPLOAD_DIR') != null && strlen(env('PIER_UPLOAD_DIR')) > 0 ? url('api/' . env('PIER_UPLOAD_DIR') . '/upload_file') : null }}",
                    "s3": {
                        bucketName: "{{ env('PIER_S3_BUCKET') }}",
                        region: "{{ env('PIER_S3_REGION') }}",
                        accessKeyId: "{{ env('PIER_S3_ACCESS_KEY_ID') }}",
                        secretAccessKey: "{{ env('PIER_S3_SECRET_ACCESS_KEY') }}",
                    },
                    'pierForm': "{{ env('PIER_FORM_TOKEN') }}",
                    "authUser": "{{Auth::check() ? Auth::id() : null}}",
                    ...rest,
                });
            }

            window.PierFormLoaded = true;

            window.loadPierForm({
                pierFormId,
                pierModel,
                pierModelValues,
                onPierFormSuccess: "{!! $onSave ?? null !!}",
            });
        });
    })()
</script>

@if ($formId != 'pierReferenceForm')
    <x-pier-modal id="addReferenceModal" />

    <script src="{{ asset('pier/js/pier-form.js') }}"></script>

    <script>
        window.addEventListener("load", function() {
            MicroModal.init();
        });

        window.fetchReferenceForm = async function(model, rowId) {
            let url = `{{ url('model') }}/${model}`;
            url += rowId ? `/browse/${rowId}` : '/describe';

            const res = await fetch(url, {
                method: "GET",
                headers: {
                    'Content-Type': 'application/json'
                },
            });

            return await res.json();
        }

        window.openPierReferenceModalForm = () => window.showPierModal('addReferenceModal');

        window.showPierReferenceModalForm = async function(model = "Intermediary Partner") {
            const pierReferenceModal = document.querySelector("#addReferenceModal");
            const pierReferenceModalContent = pierReferenceModal.querySelector("main");
            pierReferenceModalContent.innerHTML = "<div id='pierReferenceForm'>Loading form, please wait...</div>";

            openPierReferenceModalForm();

            const modelDetails = await fetchReferenceForm(model);
            window.loadPierForm({
                pierFormId: "pierReferenceForm",
                pierModel: modelDetails,
                onPierFormSuccess(data, el) {
                    if (typeof window.pierReferenceModalFormResolver == "function") {
                        window.pierReferenceModalFormResolver(data);
                        window.pierReferenceModalFormResolver = null;
                        setTimeout(hidePierReferenceModalForm);
                    }
                }
            });

            return new Promise(res => {
                window.pierReferenceModalFormResolver = res;
            })
        }

        window.hidePierReferenceModalForm = () => window.hidePierModal('addReferenceModal');
    </script>
@endif
