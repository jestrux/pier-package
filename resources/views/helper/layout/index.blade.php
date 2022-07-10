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
        window.models = {!! json_encode($models->all()) !!}.map(m => ({
            ...m,
            fields: [...JSON.parse(m.fields), {
                label: "created_at"
            }, {
                label: "updated_at"
            }]
        }))
    </script>
</head>

<body class="bg-gray-200" x-data="pierHelper">
    @include('pier::helper.layout.nav')

    @yield('content')

    @include('pier::helper.layout.logic')
</body>

</html>
