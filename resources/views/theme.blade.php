@include('pier::utils')

{{-- @assets() --}}
<script>
    window.loadPierTheme = () => {
        window.appendAlpineJS();
        document.body.classList.add("bg-card", "text-content");
    };

    document.addEventListener('DOMContentLoaded', loadPierTheme, false);
</script>

<script src="https://cdn.tailwindcss.com"></script>

<style type="text/tailwindcss">
    :root {
        --stroke-color: 226 232 240;
        --border-color: #e2e8f0;
        --canvas-color: 229 229 229;
        --card-color: 255 255 255;
        --content-color: 0 0 0;
        --primary-color: {{ join(' ', sscanf(env('APP_COLOR') ?? '#2c5282', '#%02x%02x%02x')) }};
    }

    @media (prefers-color-scheme: dark) {
        :root {
            --stroke-color: 53 53 53;
            --border-color: rgba(255, 255, 255, 0.16);
            --canvas-color: 24 24 24;
            --card-color: 37 37 37;
            --content-color: 255 255 255;
        }

        input[type="date"] {
            color-scheme: dark;
        }
    }

    * {
        border-color: rgb(var(--stroke-color));
    }

    .flex>* {
        min-width: 0;
    }
</style>

<script>
    window.tailwind.config = {
        theme: {
            extend: {
                colors: {
                    canvas: "rgb(var(--canvas-color) / <alpha-value>)",
                    card: "rgb(var(--card-color) / <alpha-value>)",
                    content: "rgb(var(--content-color) / <alpha-value>)",
                    primary: "rgb(var(--primary-color) / <alpha-value>)",
                    stroke: "rgb(var(--stroke-color) / <alpha-value>)",
                },
            },
        },
    };
</script>

{{-- Form --}}
<style>
    body {
        --input-bg-color: rgb(var(--content-color) / 0.05);
        --input-border-color: rgb(var(--content-color) / 0.07);
    }

    .pier-form-field .pier-label {
        font-size: 0.95rem;
        opacity: 0.9;
        margin-bottom: 0.1rem;
    }

    .pier-form-field .pier-input,
    .pier-form-field .pier-textarea,
    .pier-form-field .pier-select {
        font-size: 20px;
        width: 100%;
    }

    .pier-form-field .pier-label,
    .pier-form-field .pier-select,
    .pier-form-field .pier-textarea,
    .pier-form-field .pier-input {
        display: block;
        width: 100%;
    }

    .pier-form-field .pier-select,
    .pier-form-field .pier-textarea,
    .pier-form-field .pier-input {
        background-color: var(--input-bg-color);
        border: 1px solid var(--input-border-color);
        border-radius: 0.35rem;
        font-size: 1rem;
        line-height: 1.5rem;
        padding: 0.5rem 0.75rem;
        outline: none;
    }

    .pier-form-field .pier-select,
    .pier-form-field .pier-input,
    .pier-form-field .pier-textarea {
        min-height: 3rem;
        font-size: 1.1rem;
        padding: 0.65rem 0.8rem;
    }

    .pier-form-field .pier-textarea {
        min-height: 150px;
    }

    .pier-form-field>.mt-6 {
        margin-top: -0.1rem !important;
    }

    #qlEditorWrapper .ql-container,
    #qlEditorWrapper .ql-editor {
        height: auto !important;
    }

    #qlEditorWrapper .ql-toolbar {
        border-radius: 0.35rem 0.35rem 0 0 !important;
    }

    #qlEditorWrapper .ql-container {
        border-radius: 0 0 0.35rem 0.35rem !important;
    }

    .PierTelInput .vti__dropdown {
        pointer-events: none;
    }

    @media only screen and (max-width: 680px) {
        .pier-form-fields .grid {
            display: flex;
            flex-direction: column;
        }
    }

    @media only screen and (max-width: 680px) {
        .pier-form-fields .grid {
            display: flex;
            flex-direction: column;
        }
    }

    .pier-toast-message {
        background: #333;
        color: white;
        font-size: 0.9rem;
        padding: 0.8rem 1rem;
        border-radius: 4px;
        pointer-events: none;

        display: inline-flex;
        align-items: center;

        position: fixed;
        top: 1rem;
        left: 50%;
        z-index: 9999;

        transform: translateX(-50%);
        transition: all 0.15s ease-in-out;
    }

    .pier-toast-message span {
        color: greenyellow;
    }

    .pier-toast-message svg {
        fill: currentColor;
    }

    .pier-toast-message:not(.show) {
        transform: translateX(-50%) translateY(-80%);
        opacity: 0;
    }
</style>
{{-- @endassets() --}}
