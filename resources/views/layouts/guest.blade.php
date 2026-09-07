<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <title>{{ config('app.name', 'Job Tracker') }}</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-900 text-gray-100 font-sans antialiased min-h-screen">
    <div class="min-h-screen flex flex-col justify-center items-center px-4 py-8 sm:px-6 lg:px-8">
        
        <!-- App Header Brand Link -->
        <div class="mb-6 flex items-center space-x-3">
            <div class="p-2 bg-[#92BA56]/20 rounded-xl border border-[#92BA56]/30">
                <svg class="w-7 h-7 text-[#92BA56]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <span class="font-bold text-2xl tracking-tight text-white">Job Tracker</span>
        </div>

        <!-- Glassmorphism Card Container -->
        <div class="w-full sm:max-w-md bg-gray-800/40 backdrop-blur-sm border border-gray-700/60 shadow-2xl rounded-xl p-6 sm:p-8">
            {{ $slot }}
        </div>
        
    </div>
</body>
</html>