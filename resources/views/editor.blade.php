<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'YouLead') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com" />
    <link href="https://fonts.googleapis.com/css2?family=Muli:wght@200;400;520;600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet">

    <script>
        @if(config('pier.prefix') != null)
        	window.pierPrefix = "{{config('pier.prefix')}}";
		@endif
    </script>
    
    <style>
        body{
            font-family: 'Muli', sans-serif;
        }
        .bg-dark,
        .bg-dark-500{
            background: #050505;
        }
        .bg-dark-100{
            background: #424242;
        }
        .bg-dark-200{
            background: #303030;
        }
        .bg-dark-300{
            background: #212121;
        }
        .bg-dark-400{
            background: #181818;
        }
    </style>

    <!-- Scripts -->
    <script src="{{ asset('pier/js/pier-editor.js') }}" defer></script>
</head>
<body>
    <div id="app">
        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>