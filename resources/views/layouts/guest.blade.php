<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-950 text-slate-100 font-sans antialiased min-h-screen relative overflow-x-hidden flex flex-col justify-center sm:py-12">
        
        <!-- Glowing background spots -->
        <div class="absolute top-[-20%] left-[-10%] w-[50%] h-[50%] bg-indigo-600/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-emerald-600/10 rounded-full blur-[120px] pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col items-center justify-center px-6">
            <div class="flex flex-col items-center gap-3 mb-8">
                <a href="/" wire:navigate class="flex flex-col items-center gap-3 group">
                    <x-application-logo class="w-16 h-16 fill-current text-slate-300 group-hover:scale-105 transition duration-200" />
                    <span class="font-extrabold tracking-wider bg-gradient-to-r from-indigo-400 via-cyan-400 to-emerald-400 bg-clip-text text-transparent text-xl">RAYCORP PORTAL</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md px-8 py-8 bg-slate-900/40 backdrop-blur-xl border border-slate-800/80 rounded-3xl shadow-2xl overflow-hidden">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
