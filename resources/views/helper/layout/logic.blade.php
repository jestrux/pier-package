<script src="https://unpkg.com/prismjs@1.28.0/components/prism-core.min.js"></script>
<script src="https://unpkg.com/prismjs@1.28.0/plugins/autoloader/prism-autoloader.min.js"></script>

<script src="https://unpkg.com/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
<script src="//unpkg.com/alpinejs" defer></script>

@verbatim
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data("pierHelper", function() {
            return {
                models: window.models,
                initialized: false,
                selectedModel: this.$persist(null),
                pluck: this.$persist([]),
                searchQuery: this.$persist(""),
                groupBy: this.$persist(""),
                orderBy: this.$persist(""),
                orderByAscend: this.$persist(false),
                currentPage: this.$persist(null),
                perPage: this.$persist(null),
                limit: this.$persist(""),
                randomize: this.$persist(false),
                apiResponse: null,
                dataGridContent: "",
                dataGridShowPreview: false,
                dataGridFieldMap: this.$persist({
                    image: "image",
                    meta: "meta",
                    title: "title",
                    descriptipon: "descriptipon",
                }),
                reset() {
                    Object.keys(localStorage).map(key => {
                        if (key.indexOf('_x_') != -1) localStorage.removeItem(key);
                    });

                    this.pluck = [],
                        this.searchQuery = "",
                        this.groupBy = "",
                        this.orderBy = "",
                        this.orderByAscend = false,
                        this.currentPage = null,
                        this.perPage = null,
                        this.limit = "",
                        this.randomize = false,
                        this.dataGridContent = "",
                        this.dataGridFieldMap = {
                            image: "image",
                            meta: "meta",
                            title: "title",
                            descriptipon: "descriptipon",
                        }
                },
                togglePagination(e) {
                    if (e.target.value) {
                        this.currentPage = 1;
                        this.perPage = 25;
                    } else {
                        this.currentPage = null;
                        this.perPage = null
                    }
                },
                decodeEntities(str = "gda") {
                    // if (str && typeof str === 'string') return "";

                    // this prevents any overhead from creating the object each time
                    var element = document.createElement('div');

                    // strip script/html tags
                    str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
                    str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
                    element.innerHTML = str;
                    // str = element.textContent;

                    console.log(str);
                    return str;
                },
                get dataGridCode() {
                    return `<x-pier-data-grid
    model="${this.selectedModel.name}"
    image-field="${this.dataGridFieldMap.image}"
    meta-field="${this.dataGridFieldMap.meta}"
    title-field="${this.dataGridFieldMap.title}"
    description-field="${this.dataGridFieldMap.description}"
/>`
                },
                get paginationPages() {
                    return Array(this.apiResponse?.last_page || 1).fill("").map((_, i) => i);
                },
                get selectedModelId() {
                    return this.selectedModel?._id;
                },
                set selectedModelId(modelId) {
                    if (!modelId?.length) return;

                    if (!this.initialized) {
                        this.initialized = true;
                    } else {
                        this.reset();
                    }

                    this.selectedModel = this.models.find(({
                        _id
                    }) => _id === modelId);
                },
                get cleanAPIUrl() {
                    const pageUrl = window.location.href;
                    const url = new URL(pageUrl.substring(0, pageUrl.indexOf("/pier-helper")) + this
                        .apiUrl);
                    const excemptParams = ["imageField", "metaField", "titleField",
                        "descriptionField"
                    ];
                    excemptParams.forEach(param => {
                        url.searchParams.delete(param);
                    });

                    return url.toString();
                },
                get apiUrl() {
                    let params = Object.entries({
                        pluck: this.pluck.join(","),
                        q: this.searchQuery,
                        page: this.currentPage,
                        perPage: this.perPage,
                        limit: this.currentPage == null ? this.limit : null,
                        randomize: this.randomize,
                        groupBy: this.groupBy,
                        orderBy: `${this.orderBy}${this.orderBy && this.orderByAscend ? `,asc` :'' }`
                            .trim(),
                        imageField: this.dataGridFieldMap.image,
                        metaField: this.dataGridFieldMap.meta,
                        titleField: this.dataGridFieldMap.title,
                        descriptionField: this.dataGridFieldMap.description,
                    }).reduce((agg, [key, value]) => {
                        if (value)
                            return {
                                ...agg,
                                [key]: value
                            };

                        return agg;
                    }, {});

                    const queryParams = new URLSearchParams(params).toString();

                    return ("/api/" + this.selectedModel?.name + (queryParams.length ?
                        `?${queryParams}` : "")).replaceAll("%2C", ",");
                },
                init() {
                    this.$watch('apiUrl', () => {
                        this.fetchAPI();
                        this.reRenderGrid();
                    });

                    this.$nextTick(() => {
                        this.selectedModelId = (this.selectedModel || this.models[0])._id;
                    });
                },
                async fetchAPI() {
                    const res = await fetch(this.apiUrl).then(r => r.json());
                    this.apiResponse = JSON.stringify(res, null, "\t");
                    this.$nextTick(() => {
                        Prism.highlightAll();
                    })
                },
                async reRenderGrid() {
                    this.dataGridContent = await fetch(this.apiUrl.replace("api",
                        "pier-helper/data-grid-render")).then(r => r.text());
                    // this.apiResponse = JSON.stringify(res, null, "\t");
                    // this.$nextTick(() => {
                    //     Prism.highlightAll();
                    // })
                },
            }
        });
    });
</script>

<script type="module">
    import {
        LitElement,
        html,
        css
    } from 'https://unpkg.com/lit-element/lit-element.js?module';
    import {
        create,
        cssomSheet
    } from 'https://cdn.skypack.dev/twind';

    const sheet = cssomSheet({
        target: new CSSStyleSheet()
    });

    const {
        tw
    } = create({
        sheet
    });

    class MyElement extends LitElement {
        static get properties() {
            return {
                label: {
                    type: String
                },
                value: {
                    type: Boolean
                }
            }
        }

        static styles = [sheet.target];

        handleChange(e) {
            this.value = e.target.checked;
            this.dispatchEvent(new CustomEvent("input", {
                bubbles: true,
                composed: true,
            }));
        }

        render() {
            return html`
                <label class=${tw`inline-flex relative items-center cursor-pointer`}>
                    <input type="checkbox" class=${tw`sr-only`} @change=${this.handleChange} />

                    <span class=${tw`w-11 h-6 rounded-full ${this.value ? 'bg-blue-600 ' : 'bg-gray-200'}`}>
                        <span
                            class=${tw`absolute top-[2px] left-[2px] bg-white border-gray-300 border rounded-full h-5 w-5 transition-all ${this.value ? 'translate-x-full border-white' : ''}`}></span>
                    </span>

                    ${this.label?.length && html`<span class=${tw`ml-2 text-sm font-medium text-gray-900 dark:text-gray-400`}>
                                                                                                                                                                    ${this.label}
                                                                                                                                                                </span>`}
                </label>
                `;
        }

    }

    customElements.define('a-switch', MyElement);
</script>
@endverbatim
