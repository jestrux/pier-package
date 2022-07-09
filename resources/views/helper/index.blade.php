<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Pier Helper</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    {{-- <link rel="stylesheet" href="https://unpkg.com/prismjs@1.28.0/themes/prism.css" /> --}}
    <style>
        /* Night Owl theme
      ** https://github.com/PrismJS/prism-themes/blob/master/themes/prism-night-owl.css
      */
        code[class*=language-],
        pre[class*=language-] {
            color: #d6deeb;
            font-family: Consolas, Monaco, "Andale Mono", "Ubuntu Mono", monospace;
            text-align: left;
            white-space: pre;
            word-spacing: normal;
            word-break: normal;
            word-wrap: normal;
            line-height: 1.5;
            font-size: 1em;
            -moz-tab-size: 4;
            -o-tab-size: 4;
            tab-size: 4;
            -webkit-hyphens: none;
            -moz-hyphens: none;
            -ms-hyphens: none;
            hyphens: none
        }

        code[class*=language-] ::-moz-selection,
        code[class*=language-]::-moz-selection,
        pre[class*=language-] ::-moz-selection,
        pre[class*=language-]::-moz-selection {
            text-shadow: none;
            background: rgba(29, 59, 83, .99)
        }

        code[class*=language-] ::selection,
        code[class*=language-]::selection,
        pre[class*=language-] ::selection,
        pre[class*=language-]::selection {
            text-shadow: none;
            background: rgba(29, 59, 83, .99)
        }

        @media print {

            code[class*=language-],
            pre[class*=language-] {
                text-shadow: none
            }
        }

        pre[class*=language-] {
            padding: 1em;
            margin: .5em 0;
            overflow: auto
        }

        :not(pre)>code[class*=language-],
        pre[class*=language-] {
            color: #fff;
            background: #011627
        }

        :not(pre)>code[class*=language-] {
            padding: .1em;
            border-radius: .3em;
            white-space: normal
        }

        .token.cdata,
        .token.comment,
        .token.prolog {
            color: #637777;
            font-style: italic
        }

        .token.punctuation {
            color: #c792ea
        }

        .namespace {
            color: #b2ccd6
        }

        .token.deleted {
            color: rgba(239, 83, 80, .56);
            font-style: italic
        }

        .token.property,
        .token.symbol {
            color: #80cbc4
        }

        .token.keyword,
        .token.operator,
        .token.tag {
            color: #7fdbca
        }

        .token.boolean {
            color: #ff5874
        }

        .token.number {
            color: #f78c6c
        }

        .token.builtin,
        .token.char,
        .token.constant,
        .token.function {
            color: #82aaff
        }

        .token.doctype,
        .token.selector {
            color: #c792ea;
            font-style: italic
        }

        .token.attr-name,
        .token.inserted {
            color: #addb67;
            font-style: italic
        }

        .language-css .token.string,
        .style .token.string,
        .token.entity,
        .token.string,
        .token.url {
            color: #addb67
        }

        .token.atrule,
        .token.attr-value,
        .token.class-name {
            color: #ffcb8b
        }

        .token.important,
        .token.regex,
        .token.variable {
            color: #d6deeb
        }

        .token.bold,
        .token.important {
            font-weight: 700
        }

        .token.italic {
            font-style: italic
        }
    </style>

    <style>
        a-switch {
            display: inline-flex;
        }
    </style>
    <script>
        @if (config('pier.prefix') != null)
            window.pierPrefix = "{{ config('pier.prefix') }}";
        @endif
    </script>

    <script>
        window.models = {!! json_encode($models->all()) !!}
    </script>
</head>

<body class="bg-gray-200 p-6">
    <div x-data="pierHelper" class="flex items-start">
        <div class="w-[500px] sticky top-6 flex-shrink-0 bg-white shadow border rounded">
            <h1 class="text-xl leading-none font-semibold mt-4 mb-4 mx-4">Pier Helper</h1>

            <hr />

            <div class="mt-4 px-4">
                <h3 class="text-lg leading-none font-medium mb-2">Model</h3>
                <select class="w-full" x-model="selectedModelId">
                    <option value="">Choose One</option>

                    <template x-for="model in models">
                        <option :value="model._id" x-text="model.name"></option>
                    </template>
                </select>
            </div>

            <div>
                <div class="flex items-center justify-between mt-6 mb-2 px-4 mx-0.5">
                    <h3 class="text-lg leading-none font-medium">Fields to be returned</h3>

                    <a-switch :value="[0, selectedModel?.fields.length].includes(pluck.length)"
                        @input="$event.target.value ? pluck = [] : null" 
                        label="All fields"
                      />
                </div>

                <hr />

                <div class="pt-1.5 pb-4 px-4 space-y-2 bg-gray-100 border-b">
                    <template x-for="field in selectedModel?.fields">
                        <button class="mr-2 border px-3.5 py-1.5 rounded-full text-sm"
                            :class="pluck.includes(field.label) ? 'bg-blue-500 border-blue-300 text-white' : 'border-gray-400'"
                            @click="!pluck.includes(field.label) ? pluck.push(field.label) : pluck = pluck.filter(f => (f) != field.label)"
                            x-text="field.label">
                        </button>
                    </template>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mt-6 mb-2 px-4 mx-0.5">
                    <h3 class="text-lg leading-none font-medium">Filters</h3>

                    <button class="text-blue-500 underline">
                        Clear filters
                    </button>
                </div>

                <hr />

                <div class="pt-3 pb-4 px-4 space-y-2 bg-gray-100 max-h-[215px] overflow-y-auto border-b">
                    <div
                        class="pl-4 pr-3 h-14 bg-white border shadow rounded capitalize flex items-center justify-between">
                        Search query
                        <input class="w-56 py-1 px-2.5 border-gray-300 bg-gray-50 rounded-sm placeholder-gray-400/80"
                            type="search" placeholder="Type here to search..." x-model="searchQuery" />
                    </div>

                    <div
                        class="pl-4 pr-3 h-14 bg-white border shadow rounded capitalize flex items-center justify-between">
                        Status <span class="bg-blue-100 text-blue-900 text-sm py-1 px-2.5 rounded-full">active</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between mt-4 mb-3.5 px-4 mx-0.5">
                <h3 class="text-lg leading-none font-medium">Randomize results</h3>

                <a-switch x-model="randomize" />
            </div>

            <hr />

            <div class="flex items-center justify-between mt-4 mb-3 px-4 mx-0.5">
                <div>
                    <h3 class="text-lg leading-none font-medium">Limit</h3>
                    <p class="opacity-50">
                        Max number of results that will be returned
                    </p>
                </div>

                <input class="w-16 border-2 border-gray-300 rounded-sm py-1.5 px-3 placeholder-gray-400" x-model="limit"
                    placeholder="--" />
            </div>
        </div>

        <div class="ml-12 flex-1 min-w-0">
            <div>
                {{-- <h1 class="text-xl leading-none font-semibold mb-3">
                    Rest API
                </h1> --}}

                <div class="bg-white shadow border rounded">
                    <div>
                        <h1 class="text-xl leading-none font-semibold mt-4 mb-4 mx-4">API Response</h1>
                        <hr />
                    </div>

                    <div class="px-4 pt-4">
                        <h3 class="mb-1 text-lg leading-none font-medium">URL</h3>
                        <div class="relative">
                            <input type="text" :value="apiUrl"
                                class="w-full text-xl rounded border-2 border-gray-300 pointer-events-none" />
                            <button
                                class="absolute inset-y-0.5 right-0.5 bg-gray-200 px-3 flex items-center justify-center text-sm leading-none">
                                <svg class="w-7 h-7 -ml-1.5" viewBox="0 0 32 32" fill="none" stroke="currentColor"
                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                    aria-hidden="true">
                                    <path
                                        d="M13 10.75h-1.25a2 2 0 0 0-2 2v8.5a2 2 0 0 0 2 2h8.5a2 2 0 0 0 2-2v-8.5a2 2 0 0 0-2-2H19">
                                    </path>
                                    <path
                                        d="M18 12.25h-4a1 1 0 0 1-1-1v-1.5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v1.5a1 1 0 0 1-1 1ZM13.75 16.25h4.5M13.75 19.25h4.5">
                                    </path>
                                </svg>

                                Copy
                            </button>
                        </div>

                        <div class="-mx-4 mt-5 max-h-[500px] overflow-y-auto">
                            <h3 class="px-4 mb-2 text-lg leading-none font-medium">Sample Response</h3>
                            <pre><code class="language-javascript" x-text="apiResponse"></code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/prismjs@1.28.0/components/prism-core.min.js"></script>
    <script src="https://unpkg.com/prismjs@1.28.0/plugins/autoloader/prism-autoloader.min.js"></script>
    <script src="//unpkg.com/alpinejs" defer></script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data("pierHelper", () => ({
                models: window.models,
                selectedModelId: null,
                selectedModel: null,
                pluck: [],
                searchQuery: "",
                randomize: false,
                limit: "",
                apiResponse: null,
                get apiUrl() {
                    let params = Object.entries({
                        q: this.searchQuery,
                        limit: this.limit,
                        randomize: this.randomize,
                        pluck: this.pluck.join(",")
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
                    this.$watch('selectedModelId', modelId => {
                        if (!modelId?.length) return;

                        const model = this.models.find(({
                            _id
                        }) => _id === modelId);
                        model.fields = JSON.parse(model.fields);

                        this.pluck = [];
                        this.selectedModel = model;
                    });

                    this.$watch('apiUrl', () => {
                        this.fetchAPI();
                    });

                    this.$watch('pluck', modelId => {})

                    this.$nextTick(() =>
                        this.selectedModelId = this.models[0]._id
                    );
                },
                async fetchAPI() {
                    const res = await fetch(this.apiUrl).then(r => r.json());
                    this.apiResponse = JSON.stringify(res, null, "\t");
                    this.$nextTick(() => {
                        Prism.highlightAll();
                    })
                },
            }));
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
</body>

</html>
