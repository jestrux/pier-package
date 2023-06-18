<link href="{{ asset('pier/css/modal.css') }}" rel="stylesheet" />

<div id="{{ $id }}"
    class="piermodal {{ $placement == 'right' ? 'piermodal-swipe' : 'piermodal-slide' }} {{ $open ? 'is-open' : '' }}"
    aria-hidden="{{ $open ? 'false' : 'true' }}">
    <div class="piermodal__overlay" tabindex="-1" data-micromodal-close>
    </div>

    <div class="pointer-events-none fixed inset-0 z-[999] flex items-start justify-center md:py-12">
        <div class="{{ $placement == 'right' ? 'fixed inset-y-0 right-0' : 'md:rounded-lg' }} piermodal__card pointer-events-auto min-w-full md:min-w-0 h-screen md:h-auto max-h-screen overflow-auto bg-white"
            role="dialog" aria-modal="true" aria-labelledby="modal-1-title" style="width: {{ $width }}">
            <div class="sticky z-10 bg-white border-b shadow-sm p-3 top-0 flex items-center gap-3">
                <button class="w-7 h-7 rounded-full border flex items-center justify-center" aria-label="Close modal"
                    data-micromodal-close>
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
    window.showPierModal(id = "sampleModal") {
        const modal = document.querySelector("#" + id);
        modal.querySelectorAll("[data-micromodal-close]").forEach(el => {
            el.addEventListener("click", () => hideModal(id))
        })

        try {
            MicroModal.show(id);
        } catch (error) {
            modal.classList.add("is-open");
            modal.setAttribute("aria-hidden", "false");
        }
    }

    window.hidePierModal(id = "sampleModal") {
        const modal = document.querySelector("#" + id);

        // window.MicroModalInitialized
        try {
            MicroModal.close(id);
        } catch (error) {
            modal.classList.remove("is-open");
            modal.setAttribute("aria-hidden", "true");
        }
    }

    window._htmlToElements = function(html) {
        var template = document.createElement('template');
        template.innerHTML = html;

        const nodes = template.content.childNodes,
            nodesArray = [],
            scriptsArray = [];
        for (var i in nodes) {
            if (nodes[i].nodeType == 1) { // get rid of the whitespace text nodes
                if (nodes[i].nodeName === 'SCRIPT') {
                    scriptsArray.push(nodes[i]);
                } else {
                    nodesArray.push(nodes[i]);
                }
            }
        }
        return nodesArray.concat(scriptsArray);
    }

    window._loadPierContent = function(data, index, container, appendData) {
        if (index === 0 && !appendData)
            container.innerHTML = '';

        if (index <= data.length) {
            var element = data[index];
            if (element !== undefined && element.nodeName === 'SCRIPT') {
                // output scripts
                var script = document.createElement('script');
                // copy type
                if (element.type) {
                    script.type = element.type;
                }
                // clone attributes
                Array.prototype.forEach.call(element.attributes, function(attr) {
                    script.setAttribute(attr.nodeName, attr.nodeValue);
                });
                if (element.src != '') {
                    script.src = element.src;
                    script.onload = function() {
                        _loadPierContent(data, index + 1, container);
                    };
                    document.head.appendChild(script);
                } else {
                    script.text = element.text;
                    document.body.appendChild(script);
                    _loadPierContent(data, index + 1, container);
                }
            } else {
                if (element !== undefined)
                    container.appendChild(element);

                _loadPierContent(data, index + 1, container);
            }
        } else {
            return true;
        }
    };

    window.loadPierModalContent = async function(url, container, appendData = false) {
        const res = await fetch(url, {
            method: "GET",
            headers: {
                'Content-Type': 'application/json'
            },
        });

        const data = await res.text();

        return _loadPierContent(_htmlToElements(data), 0, container, appendData);
    }

    window.addEventListener("load", function() {
        MicroModal.init();

        setTimeout(() => {
            window.MicroModalInitialized = true;
        }, 100);
    });
</script>

<script defer src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>
