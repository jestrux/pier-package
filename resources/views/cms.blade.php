<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

	<title>Youlead CMS</title>

	<link rel="stylesheet" href="{{asset('pier/css/cms.css')}}" />
    <link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet">

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
    <div id="pierCMS">
		
	</div>
	
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
	
	<script src="{{ asset('pier/js/pier-cms.js') }}" defer></script>
</body>
</html>
