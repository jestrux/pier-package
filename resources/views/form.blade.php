<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Pier Form</title>

    <link rel="stylesheet" href="{{ asset('css/cms.css') }}" />
    {{-- <link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet" /> --}}

    <script>
        @if (config('pier.prefix') != null)
            window.pierPrefix = "{{ config('pier.prefix') }}";
        @endif
    </script>
</head>

<body style="overflow: visible">
    <div class="py-12 bg-gray-200">
        <div class="container mx-auto">
            <div class="mx-auto" style="width: 560px">
                <x-pier-form model="Station" rowId="0c59b963-78b3-43cc-8149-321e981cc7ed" :redirectTo="url('/home')" />
            </div>
        </div>
    </div>

    <script>
        window.addEventListener("PierForm:loaded", () => {
            PierForm("#pierForm", {
                "appName": "{{ env('APP_NAME') }}",
                "unsplashClientId": "{{ env('PIER_UNSPLASH_CLIENT_ID') }}",
                "fileUploadUrl": "{{ env('PIER_UPLOAD_DIR') != null && strlen(env('PIER_UPLOAD_DIR')) > 0 ? url('api/' . env('PIER_UPLOAD_DIR') . '/upload_file') : null }}",
                "s3": {
                    bucketName: "{{ env('PIER_S3_BUCKET') }}",
                    region: "{{ env('PIER_S3_REGION') }}",
                    accessKeyId: "{{ env('PIER_S3_ACCESS_KEY_ID') }}",
                    secretAccessKey: "{{ env('PIER_S3_SECRET_ACCESS_KEY') }}",
                },
            })
        });
    </script>

    <script src="{{ asset('js/pier-form.js') }}" defer></script>
</body>

</html>
