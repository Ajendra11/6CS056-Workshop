@props(['title' => 'Home'])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $title ?? 'Home') | Training Institute</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <x-navbar />
    <main class="container">
        <x-alert />
        {{ $slot ?? '' }}
        @yield('content')
    </main>
    <x-footer />
</body>
</html>
