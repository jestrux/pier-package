<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

	<title>{{ config('app.name' ?? 'Pier')}} CMS </title>
	<link href="{{ asset('img/logo-icon.png') }}" rel="shortcut icon" type="image/png" sizes="16x16" />

	<link rel="stylesheet" href="{{asset('pier/css/cms.css')}}" />
	<link rel="stylesheet" href="{{asset('pier/css/form.css')}}" />
	<script src="https://cdn.tailwindcss.com"></script>

	<style>
		:root {
			--primary-color: {{ env('APP_COLOR') ?? '#2c5282' }};
		}
	</style>

	@php
		$models = $models->map(function($model) {
            $model->plural_name = Str::plural($model->name);
            $model->settings = json_decode($model->settings);
            return $model;
        });
	@endphp

    <script>
		@if(config('pier.prefix') != null)
        	window.pierPrefix = "{{config('pier.prefix')}}";
		@endif
    </script>
    <script>
		window.models = {!! json_encode($models->all()) !!}
		const token = "{{csrf_token()}}";
		function uploadFile(file, folder = "", percentCallback) {
			return new Promise((resolve, reject) =>{
				var xhr = new XMLHttpRequest();
				// && file.size <= $id("MAX_FILE_SIZE").value
				// if (xhr.upload && file.type == "image/jpeg") {
				xhr.upload.addEventListener("progress", function(e) {
					var percent = parseInt(e.loaded / e.total * 100);
					console.log("Upload percent: " + (percent - 100));
					if(typeof percentCallback === 'function')
						percentCallback(percent);
				}, false);
				xhr.onreadystatechange = function(e) {
					if (xhr.readyState == 4){
						if(xhr.status == 200)
							resolve(JSON.parse(xhr.responseText).file);
							// resolve(file.name.replace(/ /g, "_"));
						else
							reject();
					}
				};
				if(folder && folder.length)
					folder = folder + "/";
				var data = new FormData();
				data.append("file", file);
				data.append("_token", '{{ csrf_token() }}');
				data.append("destination_path", folder);
				xhr.open("POST", '{{ route('upload_file') }}', true);
				// xhr.setRequestHeader("X-FILENAME", folder + file.name.replace(/ /g, "_"));
				xhr.send(data);
			});
		}
		function post(url, data) {
			data = JSON.parse(data);
			data["_token"] = '{{ csrf_token() }}';
			data = JSON.stringify(data);
			return new Promise((resolve, reject) => {
				var xhr = new XMLHttpRequest();
				xhr.onreadystatechange = function(e) {
					if (xhr.readyState == 4) {
						if(xhr.status == 200)
							resolve(xhr.responseText);
						else
							reject(xhr.responseText);
					}
				};
				xhr.open("POST", url, true);
				xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
				xhr.send(data);
			})
		}
	</script>
</head>
<body>
    <div id="pierCMS"></div>

	<script>
		@php
			$appLogo = env('APP_LOGO') ?? null;
			if (!is_null($appLogo)) $appLogo = asset($appLogo);

			$uploadUrl = env('PIER_UPLOAD_URL') ?? null;

			if(is_null($uploadUrl)) {
				$uploadDir = env('PIER_UPLOAD_DIR') ?? null;
				if (!is_null($uploadDir) && strlen($uploadDir) > 0) {
					$uploadUrl = url("api/$uploadDir/upload_file");
				}
			}
		@endphp
		
		window.addEventListener("PierCMS:loaded", () => {
			PierCMS("#pierCMS", {
				"appName": "{{env('APP_NAME')}}",
				"appLogo": "{{$appLogo}}",
				"unsplashClientId": "{{env('PIER_UNSPLASH_CLIENT_ID')}}",
				"fileUploadUrl": "{{$uploadUrl}}",
				"s3": {
					bucketName: "{{env('PIER_S3_BUCKET')}}",
					region: "{{env('PIER_S3_REGION')}}",
					accessKeyId: "{{env('PIER_S3_ACCESS_KEY_ID')}}",
					secretAccessKey: "{{env('PIER_S3_SECRET_ACCESS_KEY')}}",
				},
				"authUser": "{{Auth::check() ? Auth::id() : null}}",
			})
		});
	</script>

	<script src="{{ asset('pier/js/pier-cms.js') }}" defer></script>
	<script>
		function openModal(id){
			var modal = document.getElementById(id);
			modal.classList.add('open');
		}
		function closeModal(id){
			var modal = document.getElementById(id);
			modal.classList.remove('open');
		}
	</script>
</body>
</html>
