<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin · GEKRAFS</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/admin/main.js'])
</head>
<body class="h-full bg-gray-100 font-sans text-gray-900 antialiased">
    <div id="admin-app"
         data-app-name="{{ config('app.name') }}"
         data-user='@json(auth()->user()->only('id', 'name', 'email'))'
         data-logout-url="{{ route('logout') }}"
         data-site-url="{{ route('home') }}"></div>
</body>
</html>
