<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Reflection Tool') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    {{-- 🎯 背景色をアプリ本来の落ち着いた薄いグレー（bg-gray-50）に設定し、画面の上下に py-12 の余白を持たせました --}}
    <body class="font-sans text-gray-900 antialiased bg-gray-50">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 py-12">
            
            {{-- 🎯 ロゴとカードの間を程よくあけるために mb-6 に調整 --}}
            <div class="mb-6 mt-10 sm:mt-0">
                <a href="/" wire:navigate class="flex flex-col items-center group">
                    <x-application-logo class="w-20 h-auto" />
                </a>
            </div>

            {{-- 🎯 白いカード（bg-white）にして、パディングを上下左右均等に px-8 py-10 に設定。これで詰まり感が消えます --}}
            <div class="w-full sm:max-w-md px-8 py-10 bg-white shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden sm:rounded-3xl relative">
                {{ $slot }}
            </div>
            
        </div>
    </body>
</html>