{{-- 📄 resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Reflection Tool - Admin</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-50">
        <div class="min-h-screen">
            
            {{-- 🎯 ここが超重要！この {{ $slot }} という目印がある場所に、
                 ログイン画面やカレンダー画面の「本当の中身」が自動的にはめ込まれます --}}
            <main>
                {{ $slot }}
            </main>
            
        </div>
    </body>
</html>