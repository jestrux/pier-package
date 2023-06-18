<div id="{{ $instanceId }}" class="PierFormFieldWrapper">
    <pier-form-field :field="{{ json_encode($field) }}" />
</div>

<script>
    window.appendComponentScript = () => {
        return new Promise((resolve, reject) => {
            const filepath = "{{ asset('pier/js/pier-form-field.js') }}";

            if (document.querySelector('head script[src="' + filepath + '"]'))
                return resolve();

            const script = document.createElement("script");
            script.setAttribute("type", "text/javascript");
            script.setAttribute("src", filepath);
            document.querySelector("head").appendChild(script);

            script.onload = resolve();
        });
    };

    if (window.initializePierFormFields)
        window.initializePierFormFields();
    else {
        appendComponentScript().then(function() {
            window.initializePierFormFields = function() {
                setTimeout(() => {
                    document.querySelectorAll(".PierFormFieldWrapper").forEach(field => {
                        PierFormField(`#${field.id}`, {
                            "unsplashClientId": "{{ env('PIER_UNSPLASH_CLIENT_ID') }}",
                            "fileUploadUrl": "{{ env('PIER_UPLOAD_DIR') != null && strlen(env('PIER_UPLOAD_DIR')) > 0 ? url('api/' . env('PIER_UPLOAD_DIR') . '/upload_file') : null }}",
                            "s3": {
                                bucketName: "{{ env('PIER_S3_BUCKET') }}",
                                region: "{{ env('PIER_S3_REGION') }}",
                                accessKeyId: "{{ env('PIER_S3_ACCESS_KEY_ID') }}",
                                secretAccessKey: "{{ env('PIER_S3_SECRET_ACCESS_KEY') }}",
                            },
                        });
                    })
                }, 200);
            }

            initializePierFormFields();
        });
    }
</script>
