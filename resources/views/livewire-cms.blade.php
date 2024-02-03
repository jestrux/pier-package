<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="{{ asset('apple-touch-icon.png') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}" />

    <meta name="csrf_token" value="{{ csrf_token() }}"/>

    <title>Pier CMS</title>

    <link rel="manifest" href="{{ asset('manifest.json') }}" />

    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
</head>

<body class="h-screen bg-gray-200">
    <livewire:pier-cms :modelName="$modelName ?? null" />
</body>

</html>
