<!DOCTYPE html>
<html lang="fa" dir="rtl" class="h-full bg-stone-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'ورود' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex h-full items-center justify-center p-4 text-stone-800 antialiased">
    <div class="w-full max-w-sm">
        {{ $slot }}
    </div>
</body>
</html>
