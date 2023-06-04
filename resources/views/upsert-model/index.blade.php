<link rel="stylesheet" href="{{ asset('pier/css/form.css') }}" />
<script src="https://cdn.tailwindcss.com"></script>
<style>
    :root {
        --primary-color: {{ env('APP_COLOR') ?? '#2c5282' }};
    }

    .modal {
        font-family: -apple-system, BlinkMacSystemFont, avenir next, avenir, helvetica neue, helvetica, ubuntu, roboto, noto, segoe ui, arial, sans-serif;
    }

    .modal__overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        justify-content: center;
        align-items: flex-start;
        z-index: 999;
        padding-top: 3rem;
    }

    .modal__container {
        background-color: #fff;
        padding: 30px;
        max-width: 700px;
        max-height: 100vh;
        border-radius: 4px;
        overflow-y: auto;
        box-sizing: border-box;
    }

    .modal__header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal__title {
        margin-top: 0;
        margin-bottom: 0;
        font-weight: 600;
        font-size: 1.25rem;
        line-height: 1.25;
        color: #00449e;
        box-sizing: border-box;
    }

    .modal__close {
        background: transparent;
        border: 0;
    }

    .modal__header .modal__close:before {
        content: "\2715";
    }

    .modal__content {
        margin-top: 2rem;
        margin-bottom: 2rem;
        line-height: 1.5;
        color: rgba(0, 0, 0, .8);
    }

    .modal__btn {
        font-size: .875rem;
        padding-left: 1rem;
        padding-right: 1rem;
        padding-top: .5rem;
        padding-bottom: .5rem;
        background-color: #e6e6e6;
        color: rgba(0, 0, 0, .8);
        border-radius: .25rem;
        border-style: none;
        border-width: 0;
        cursor: pointer;
        -webkit-appearance: button;
        text-transform: none;
        overflow: visible;
        line-height: 1.15;
        margin: 0;
        will-change: transform;
        -moz-osx-font-smoothing: grayscale;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        -webkit-transform: translateZ(0);
        transform: translateZ(0);
        transition: -webkit-transform .25s ease-out;
        transition: transform .25s ease-out;
        transition: transform .25s ease-out, -webkit-transform .25s ease-out;
    }

    .modal__btn:focus,
    .modal__btn:hover {
        -webkit-transform: scale(1.05);
        transform: scale(1.05);
    }

    .modal__btn-primary {
        background-color: #00449e;
        color: #fff;
    }

    @keyframes mmfadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes mmfadeOut {
        from {
            opacity: 1;
        }

        to {
            opacity: 0;
        }
    }

    @keyframes mmslideIn {
        from {
            transform: translateY(15%);
        }

        to {
            transform: translateY(0);
        }
    }

    @keyframes mmslideOut {
        from {
            transform: translateY(0);
        }

        to {
            transform: translateY(-10%);
        }
    }

    @keyframes mmswipeIn {
        from {
            transform: translateX(110%);
        }

        to {
            transform: translateX(0);
        }
    }

    @keyframes mmswipeOut {
        from {
            transform: translateX(0);
        }

        to {
            transform: translateX(110%);
        }
    }

    .micromodal-swipe,
    .micromodal-slide {
        display: none;
    }

    .micromodal-swipe.is-open,
    .micromodal-slide.is-open {
        display: block;
    }

    .micromodal-swipe[aria-hidden="false"] .modal__overlay,
    .micromodal-slide[aria-hidden="false"] .modal__overlay {
        animation: mmfadeIn .3s cubic-bezier(0.0, 0.0, 0.2, 1);
    }

    .micromodal-slide[aria-hidden="false"] .modal__container,
    .micromodal-slide[aria-hidden="false"] .modal__card {
        animation: mmslideIn .3s cubic-bezier(0, 0, .2, 1);
    }

    .micromodal-swipe[aria-hidden="false"] .modal__container,
    .micromodal-swipe[aria-hidden="false"] .modal__card {
        animation: mmswipeIn .3s cubic-bezier(0, 0, .2, 1);
    }

    .micromodal-swipe[aria-hidden="true"] .modal__overlay,
    .micromodal-slide[aria-hidden="true"] .modal__overlay {
        animation: mmfadeOut .3s cubic-bezier(0.0, 0.0, 0.2, 1);
    }

    .micromodal-slide[aria-hidden="true"] .modal__container,
    .micromodal-slide[aria-hidden="true"] .modal__card {
        animation: mmslideOut .3s cubic-bezier(0, 0, .2, 1);
    }

    .micromodal-swipe[aria-hidden="true"] .modal__container,
    .micromodal-swipe[aria-hidden="true"] .modal__card {
        animation: mmswipeOut .3s cubic-bezier(0, 0, .2, 1);
    }

    .micromodal-slide .modal__card,
    .micromodal-slide .modal__container,
    .micromodal-slide .modal__overlay {
        will-change: transform;
    }
</style>

@php
    $backUrl = env('PIER_FORM_REDIRECT_URL') ?? url('/admin/' . strtolower($model->name . 's'));
    
    if (isset($redirectTo)) {
        $backUrl = url($redirectTo);
    }
    
    $plainForm = $plainForm ?? false;
    $formId = $formId ?? 'pierForm';
@endphp

@if ($plainForm)
    <div id="{{ $formId }}"></div>
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
                <div id="{{ $formId }}"></div>
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
                    "appName": "{{ env('APP_NAME') }}",
                    "unsplashClientId": "{{ env('PIER_UNSPLASH_CLIENT_ID') }}",
                    "fileUploadUrl": "{{ env('PIER_UPLOAD_DIR') != null && strlen(env('PIER_UPLOAD_DIR')) > 0 ? url('api/' . env('PIER_UPLOAD_DIR') . '/upload_file') : null }}",
                    "s3": {
                        bucketName: "{{ env('PIER_S3_BUCKET') }}",
                        region: "{{ env('PIER_S3_REGION') }}",
                        accessKeyId: "{{ env('PIER_S3_ACCESS_KEY_ID') }}",
                        secretAccessKey: "{{ env('PIER_S3_SECRET_ACCESS_KEY') }}",
                    },
                    ...rest,
                });
            }

            window.PierFormLoaded = true;

            window.loadPierForm({
                pierFormId,
                pierModel,
                pierModelValues,
                onPierFormSuccess(data, el) {
                    console.log("On main form success: ", data, el);
                }
            });
        });
    })()
</script>

@if ($formId != 'pierReferenceForm')
    <div id="addReferenceModal" class="modal micromodal-slide">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
        </div>

        <div class="pointer-events-none fixed inset-0 z-[999] flex items-start justify-center py-12">
            <div class="pointer-events-auto modal__card w-[600px] rounded-lg max-h-screen overflow-auto bg-white"
                role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
                <div class="sticky bg-white border-b shadow-sm p-3 top-0 flex items-center gap-3">
                    <button class="w-7 h-7 rounded-full border flex items-center justify-center"
                        aria-label="Close modal" data-micromodal-close>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <header class="flex-1 text-center text-lg font-medium pr-7">Add new entry</header>
                </div>

                <main class="px-5 pt-4"></main>
            </div>
        </div>
    </div>

    <script src="{{ asset('pier/js/pier-form.js') }}"></script>

    <script defer src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>

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

        window.openPierReferenceModalForm = function() {
            const id = "addReferenceModal";
            const modal = document.querySelector("#" + id);

            if (!window.referenceModalListenersAdded) {
                modal.querySelectorAll("[data-micromodal-close]").forEach(el => {
                    el.addEventListener("click", () => hidePierReferenceModalForm(id))
                });

                window.referenceModalListenersAdded = true;
            }

            try {
                MicroModal.show(id);
            } catch (error) {
                modal.classList.add("is-open");
                modal.setAttribute("aria-hidden", "false");
            }
        }

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

        window.hidePierReferenceModalForm = function() {
            const id = "addReferenceModal";
            const modal = document.querySelector("#" + id);

            try {
                MicroModal.close(id);
            } catch (error) {
                modal.classList.remove("is-open");
                modal.setAttribute("aria-hidden", "true");
            }

            if (typeof window.pierReferenceModalFormResolver == "function") window.pierReferenceModalFormResolver(null);
        }
    </script>
@endif
