<link href="{{ asset('pier/css/modal.css') }}" rel="stylesheet" />

@include('pier::utils')

<div id="{{ $id }}"
    class="piermodal {{ $placement == 'right' ? 'piermodal-swipe' : 'piermodal-slide' }} {{ $open ? 'is-open' : '' }}"
    aria-hidden="{{ $open ? 'false' : 'true' }}">
    <div class="piermodal__overlay" tabindex="-1" data-pieromodal-close="{{ $id }}">
    </div>

    <div class="pointer-events-none fixed inset-0 z-[999] flex items-start justify-center md:py-12">
        <div class="{{ $placement == 'right' ? 'fixed inset-y-0 right-0' : 'md:rounded-lg' }} piermodal__card pointer-events-auto min-w-full md:min-w-0 h-screen md:h-auto max-h-screen overflow-auto bg-white"
            role="dialog" aria-modal="true" aria-labelledby="modal-1-title" style="width: {{ $width }}">
            <div class="sticky z-10 bg-white border-b shadow-sm p-3 top-0 flex items-center gap-3">
                <button class="w-7 h-7 rounded-full border flex items-center justify-center" aria-label="Close modal"
                    data-pieromodal-close="{{ $id }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <header class="flex-1 text-center text-lg font-medium pr-7">{{ $title }}</header>
            </div>

            <main class="{{ !$noPadding ? 'px-5 pt-4' : '' }}">
                {{ $slot }}
            </main>
        </div>
    </div>
</div>

<script>
    appendAlpineJS();

    window.showPierModal = function(id = "sampleModal", {
        title
    } = {}) {
        const modal = document.querySelector("#" + id);

        if (!modal) return;

        modal.querySelectorAll(`[data-pieromodal-close="${id}"]`).forEach(el => {
            el.addEventListener("click", () => hidePierModal(id))
        });

        if (title)
            document.querySelector(`#${id} header`).textContent = title;

        try {
            MicroModal.show(id);
        } catch (error) {
            modal.classList.add("is-open");
            modal.setAttribute("aria-hidden", "false");
        }

        return new Promise((res) => {
            modal.onCloseResolver = res;
        })
    }

    window.hidePierModal = function(id = "sampleModal", data) {
        const modal = document.querySelector("#" + id);

        if (!modal) return;

        // window.MicroModalInitialized
        try {
            MicroModal.close(id);
        } catch (error) {
            modal.classList.remove("is-open");
            modal.setAttribute("aria-hidden", "true");
        }

        if (modal.onCloseResolver) modal.onCloseResolver(data);
    }

    window.loadPierModalContent = async function(modalId, {
        url,
        title
    } = {}) {
        const modalContent = document.querySelector(`#${modalId} main`);
        const modalTitle = document.querySelector(`#${modalId} header`);

        if (!url || !modalContent || !modalTitle) return;

        if (title) modalTitle.textContent = title;

        const res = await loadPierSectionContent(`#${modalId} main`, {
            url
        });

        return showPierModal(modalId, {
            url,
            title
        });
    }

    window.addEventListener("load", function() {
        MicroModal.init();

        setTimeout(() => {
            window.MicroModalInitialized = true;
        }, 100);
    });
</script>

<script defer src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>
