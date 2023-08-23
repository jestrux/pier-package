<link href="{{ asset('pier/css/micromodal.css') }}" rel="stylesheet" />

<div class="modal micromodal-slide" aria-hidden="true" id="errorModal">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close>
        <div style="width: 400px" class="modal__container p-6 relative overflow-visible" role="dialog" aria-modal="true"
            aria-labelledby="modal-1-title">
            <button
                class="shadow-md absolute right-0 top-0 -mr-2 -mt-2 rounded-full flex items-center justify-center w-6 h-6 bg-black text-orange-300 ml-1 border-0 bg-transparent outline-none focus:outline-none"
                onclick="hideErrorModal()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>

            <div id="errorModalText" class="text-lg leading-relaxed">
                Something went wrong, please try again.
            </div>
        </div>
    </div>
</div>

<div class="modal micromodal-slide" aria-hidden="true" id="successModal">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close>
        <div style="width: 400px" class="modal__container relative overflow-visible" role="dialog" aria-modal="true"
            aria-labelledby="modal-1-title">
            <div class="p-6 flex flex-col items-center justify-center text-center">
                <h2 class="flex items-center text-2xl mb-2 font-semibold">
                    <span
                        class="mr-3 flex items-center justify-center rounded-full p-1 border-none bg-green-500 text-white">
                        <svg class="text-white-500" width="13" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-check">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </span>
                    Success!
                </h2>
                <p class="text-xl mb-4 px-6 opacity-75">
                    You've rejected the suggested edit to <span class="font-semibold"></span>'s details'.
                </p>
                <div class="flex justify-center">
                    <button id="okayButton" type="button"
                        class="focus:outline-none px-5 py-1 mr-4 border-2 border-black uppercase text-xs tracking-wide font-semibold bg-black text-white rounded-full"
                        onclick="hideSuccessModal()">
                        Okay, Cool
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="confirmDialog" class="modal micromodal-slide" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close>
        <div style="width: 400px"
            class="modal__container p-6 relative overflow-visible flex flex-col items-center justify-center"
            role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
            <div id="confirmDialogText" class="text-lg text-center leading-normal">

            </div>

            <div class="mt-5 flex items-center">
                <button type="button"
                    class="focus:outline-none px-5 py-1 mr-4 border-2 border-black uppercase text-xs tracking-wide font-semibold bg-transparent text-black rounded-full"
                    onclick="hideConfirmDialog()">
                    No, Cancel
                </button>
                <button id="confirmButton" type="button"
                    class="focus:outline-none px-5 py-1 mr-4 border-2 border-black uppercase text-xs tracking-wide font-semibold bg-black text-white rounded-full">
                    Yes I'm Sure
                </button>
            </div>
        </div>
    </div>
</div>

{{-- <script src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script> --}}

<script>
    window.appendMicromodal = (
        filepath = "https://unpkg.com/micromodal/dist/micromodal.min.js"
    ) => {
        return new Promise((resolve, reject) => {
            if (document.querySelector('head script[src="' + filepath + '"]'))
                return resolve();

            const script = document.createElement("script");
            script.setAttribute("type", "text/javascript");
            script.setAttribute("src", filepath);
            document.querySelector("head").appendChild(script);

            script.onload = setTimeout(() => {
                resolve();
            }, 800);
        });
    };

    appendMicromodal()
        .then(() => {
            const errorModalText = document.querySelector("#errorModalText");
            const confirmDialogText = document.querySelector("#confirmDialogText");
            const confirmDialogOkayButton = document.querySelector("#confirmDialog #confirmButton");

            try {
                window.MicroModal.init({
                    closeTrigger: 'data-custom-close',
                    openClass: 'is-open',
                    disableScroll: true,
                    disableFocus: false,
                    awaitOpenAnimation: false,
                    awaitCloseAnimation: false,
                    debugMode: true
                });
            } catch (error) {

            }

            window.hideErrorModal = function() {
                MicroModal.close('errorModal');
            }

            window.showErrorMessage = function(message) {
                errorModalText.textContent = message;
                MicroModal.show('errorModal');
            }
            const successModalText = document.querySelector("#successModal p");
            const successDialogOkayButton = document.querySelector("#successModal #okayButton");

            window.showsuccessMessage = function(message) {
                successModalText.innerHTML = message;
                MicroModal.show('successModal');
            }

            window.hideSuccessModal = function() {
                MicroModal.close('successModal');
                window.location.reload();
            }
            const defaultConfirmOptions = {
                message: "Are you absolutely sure you wanna do such a crazy thing?",
                okayText: "Yes I'm Sure",
                onOkay: null
            };

            window.showConfirmDialog = function(options) {
                const mergedOptions = {
                    ...defaultConfirmOptions,
                    ...options
                };
                const {
                    message,
                    okayText,
                    onOkay
                } = mergedOptions;
                confirmDialogText.innerHTML = message;
                confirmDialogOkayButton.textContent = okayText;
                MicroModal.show('confirmDialog');
                confirmDialogOkayButton.addEventListener('click', () => {
                    hideConfirmDialog();
                    if (onOkay) onOkay();
                });
            }

            window.hideConfirmDialog = function() {
                MicroModal.close('confirmDialog');
            }

            window.deleteEntry = function(model, id) {
                showConfirmDialog({
                    message: `Are you sure you want to delete ${model}?`,
                    async onOkay() {
                        const item = document.querySelector(`#item${id}`);
                        item.classList.add("animate-pulse", "pointer-events-none");
                        await fetch(`{{ url('api') }}/${model}/${id}`, {
                            method: "DELETE",
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                _token: "{{ csrf_token() }}"
                            })
                        });
                        item.remove();
                        showsuccessMessage(`${model} has beeen deleted`);
                    }
                });
            }
        })
</script>
