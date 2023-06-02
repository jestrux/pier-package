{{-- <link rel="stylesheet" href="https://unpkg.com/tailwindcss@^2.0/dist/tailwind.min.css" /> --}}
<link rel="stylesheet" href="{{asset('pier/css/cms.css')}}" />
<script src="https://cdn.tailwindcss.com"></script>

<style>
    .text-blue-800,
    .text-blue-900{
        color: #333 !important;
    }

    .bg-blue-800{
        background: #333 !important;
    }

    .input-group {
        margin: 20px 0;
    }

    .input-group label {
        font-size: 1rem;
        color: #444;
        margin-bottom: 0.35rem !important;
    }

    .input-group select, .input-group textarea, .input-group input {
        margin-top: 0.2rem;
        background-color: white;
        font-size: 1.1rem;
        padding: 0.65rem 0.8rem;
    }

    form > .mt-6{
        margin-top: -0.1rem !important;
    }
</style>

@php
    $modelPlural = $model . 's';
    $backUrl = url('/admin/' . strtolower($modelPlural));

    if(isset($redirectTo))
        $backUrl = url($redirectTo);
@endphp
<div class="bg-gray-200 h-screen overflow-y-auto">
    <div class="-mt-4 mb-6">
        <div class="bg-white shadow-sm fixed inset-x-0 py-3 z-10" style="top: 0">
            <div class="container mx-auto flex items-center">
                <a href="{{$backUrl}}" class="inline-flex items-center text-xs uppercase tracking-wider border rounded pt-2 pb-1 px-3 hover:bg-gray-100">
                    <svg class="w-4 mr-3 mb-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        
                    Go Back
                </a>

                <div class="flex flex-1 pt-1 pr-48">
                    <span class="flex-1"></span>
                    <h2 class="text-xl font-medium">
                        {{isset($id) ? "Edit $model Details" : "Add New $model"}}
                    </h2>
                    <span class="flex-1"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto py-16">
        <div class="bg-white rounded-xl shadow my-4 pt-2 pb-5 px-6 mx-auto" style="width: 600px">
            @if(isset($id) && $id != null)    
                <x-pier-form 
                    :model="$model"
                    :rowId="$id"
                    :redirectTo="$backUrl"
                />
            @else
                <x-pier-form 
                    :model="$model"
                    :redirectTo="$backUrl"
                />
            @endif
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

<script src="{{asset('pier/js/pier-form.js')}}"></script>