<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Job Tracker') }}</title>

    <!-- Montserrat Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN & Custom Font Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Montserrat', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="{ IsMobileMenuOpen: false }" class="font-sans antialiased bg-[#0B1120] text-gray-100 min-h-screen">

    <!-- Render Custom Sidebar Layout -->
   <x-sidebar :active="
        request()->routeIs('job-applications.index') ? 'dashboard' : (
        request()->routeIs('job-applications.applications') ? 'applications' : (
        request()->routeIs('profile.*') ? 'profile' : ''
    ))" />

    <!-- Page Content Slot -->
    <main class="flex-1">
        {{ $slot }}
    </main>

</body>
<script>
window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
</script>
</html>