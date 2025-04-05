<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'admin-vue/src/style.css', 'admin-vue/src/main.js'])
</head>

<body class="h-full font-sans antialiased">
    <div id="app"></div>
</body>

</html>

<script>
    window.laravelRoutes = {
        'profile.view': '{{ route('profile.view') }}',
    };
</script>
