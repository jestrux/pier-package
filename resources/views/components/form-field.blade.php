@php
    $field = [
        'type' => $type,
        'label' => $label,
        'name' => $name,
        'required' => $required,
        'min' => $min,
        'max' => $max,
        'meta' => $meta,
    ];
    
    $uploadDir = env('PIER_UPLOAD_DIR') ?? null;
    if (!is_null($uploadDir) && strlen($uploadDir) > 0) {
        $uploadDir = url("api/$uploadDir/upload_file");
    }
    
    $fieldProps = [
        'unsplashClientId' => env('PIER_UNSPLASH_CLIENT_ID'),
        'fileUploadUrl' => $uploadDir,
        's3' => [
            'bucketName' => env('PIER_S3_BUCKET'),
            'region' => env('PIER_S3_REGION'),
            'accessKeyId' => env('PIER_S3_ACCESS_KEY_ID'),
            'secretAccessKey' => env('PIER_S3_SECRET_ACCESS_KEY'),
        ],
    ];

    dd($field);

@endphp

<div class="pier-form-fields">
    <div id="{{ $instanceId }}" class="PierFormFieldWrapper" data-field="{{ json_encode($field) }}"
        data-value="{{ $value }}" data-model="{{ $model }}" data-row-id="{{ $rowId }}"
        on-change="{{ $onChange }}">
    </div>
</div>

<script defer>
    window.formFieldProps = {!! json_encode($fieldProps) !!};

    (() => {
        const filepath = "{{ asset('pier/js/pier-form-field.js') }}";

        if (document.querySelector('script[src="' + filepath + '"]'))
            return

        const script = document.createElement("script");
        script.setAttribute("type", "text/javascript");
        script.setAttribute("src", filepath);
        script.setAttribute("defer", "defer");
        document.body.appendChild(script);

        const link = document.createElement("link");
        link.setAttribute("rel", "stylesheet");
        link.setAttribute("href", "{{ asset('pier/css/form.css') }}");
        link.setAttribute("defer", "defer");
        document.querySelector("head").appendChild(link);
    })();
</script>

{{-- <script src="{{ asset('pier/js/pier-form-field.js') }}" defer></script> --}}
