<!doctype html>
<html lang="en" class="h-full bg-gray-300">
  <head>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- <link rel="icon" type="image/svg+xml" href="/vite.svg" /> --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Boss Dog</title>
    @vite(['resources/css/app.css', 'admin-vue/src/style.css', 'admin-vue/src/main.js'])
    <!-- Add CORS headers -->
    <meta http-equiv="Access-Control-Allow-Origin" content="*">
    <meta http-equiv="Access-Control-Allow-Methods" content="GET, POST, PUT, DELETE, OPTIONS">
    <meta http-equiv="Access-Control-Allow-Headers" content="Content-Type, Authorization">
  </head>
  <body class="h-full">
    <div id="app" class="h-full"></div>
  </body>
</html>