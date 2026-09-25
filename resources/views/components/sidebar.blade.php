@props(['active' => 'applications'])

@php
    // Define the title and icon based on the current page
    $headerConfig = [
        'dashboard'    => ['title' => 'Dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>'],
        'applications' => ['title' => 'Applications', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>'],
        'sources'      => ['title' => 'Sources', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>'],
        'statuses'     => ['title' => 'Statuses', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>'],
        'profile'      => ['title' => 'Profile', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>'],
    ];
    
    $currentHeader = $headerConfig[$active] ?? $headerConfig['dashboard'];
@endphp

<!-- Mobile Top Header Bar -->
<header class="lg:hidden bg-gray-900 border-b border-gray-800 p-4 sticky top-0 z-40 flex items-center justify-between">
    <!-- Left: Dynamic Icon & Title -->
    <div class="flex items-center space-x-3">
        <div class="p-1.5 bg-[#92BA56]/10 rounded-lg border border-[#92BA56]/20">
            <svg class="w-5 h-5 text-[#92BA56]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $currentHeader['icon'] !!}
            </svg>
        </div>
        <span class="font-bold text-lg tracking-tight text-white">{{ $currentHeader['title'] }}</span>
    </div>
    
    <!-- Right: Log Button & Hamburger Menu -->
    <div class="flex items-center gap-4">
        <a href="{{ route('job-applications.create') }}" class="px-4 py-1.5 text-sm font-semibold text-gray-950 bg-[#92BA56] hover:bg-[#82a74a] rounded-lg transition-colors">
            + Log
        </a>
        <button @click="IsMobileMenuOpen = true" type="button" class="text-gray-400 hover:text-white focus:outline-none transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>
</header>

<!-- Mobile Drawer Backdrop -->
<div x-show="IsMobileMenuOpen" 
     x-cloak
     @click="IsMobileMenuOpen = false"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm z-50 lg:hidden">
</div>

<!-- Sidebar Navigation -->
<aside :class="IsMobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed top-0 left-0 h-screen w-64 bg-gray-800/95 backdrop-blur-md border-r border-gray-700/60 z-50 flex flex-col justify-between p-4 transition-transform duration-300 ease-in-out lg:translate-x-0">
    <div class="space-y-6">
        <!-- Brand Logo -->
        <div class="flex items-center justify-between px-2 pt-2">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-[#92BA56]/20 rounded-lg border border-[#92BA56]/30">
                    <svg class="w-6 h-6 text-[#92BA56]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <span class="font-bold text-xl tracking-tight text-white">Job Tracker</span>
            </div>
            <!-- Mobile Close Button -->
            <button @click="IsMobileMenuOpen = false" class="lg:hidden text-gray-400 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Sidebar Navigation Links -->
        <nav class="space-y-1">
            <a href="{{ route('job-applications.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ $active === 'dashboard' ? 'bg-gray-900 text-[#92BA56] border border-[#92BA56]/30' : 'text-gray-400 hover:text-white hover:bg-gray-700/50' }}">
                <svg class="w-5 h-5 {{ $active === 'dashboard' ? 'text-[#92BA56]' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('job-applications.applications') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ $active === 'applications' ? 'bg-gray-900 text-[#92BA56] border border-[#92BA56]/30' : 'text-gray-400 hover:text-white hover:bg-gray-700/50' }}">
                <svg class="w-5 h-5 {{ $active === 'applications' ? 'text-[#92BA56]' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Applications
            </a>

            <div class="pt-4 mt-4 border-t border-gray-700/40">
                <p class="px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-gray-500">Configuration</p>

                <a href="{{ route('application-sources.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ $active === 'sources' ? 'bg-gray-900 text-[#92BA56] border border-[#92BA56]/30' : 'text-gray-400 hover:text-white hover:bg-gray-700/50' }}">
                    <svg class="w-5 h-5 {{ $active === 'sources' ? 'text-[#92BA56]' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Sources
                </a>

                <a href="{{ route('application-statuses.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ $active === 'statuses' ? 'bg-gray-900 text-[#92BA56] border border-[#92BA56]/30' : 'text-gray-400 hover:text-white hover:bg-gray-700/50' }}">
                    <svg class="w-5 h-5 {{ $active === 'statuses' ? 'text-[#92BA56]' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    Statuses
                </a>
            </div>

            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ $active === 'profile' ? 'bg-gray-900 text-[#92BA56] border border-[#92BA56]/30' : 'text-gray-400 hover:text-white hover:bg-gray-700/50' }}">
                <svg class="w-5 h-5 {{ $active === 'profile' ? 'text-[#92BA56]' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Profile Settings
            </a>
        </nav>
    </div>

    <!-- Action Buttons & User Profile Footer -->
    <div class="space-y-2 pt-4 border-t border-gray-700/60">
        <button @click="IsImportModalOpen = true; IsMobileMenuOpen = false" 
                class="w-full flex items-center justify-center gap-2 px-3 py-2.5 text-xs font-semibold text-gray-300 bg-gray-800 hover:bg-gray-700 border border-gray-700 rounded-xl transition-colors">
            <svg class="w-4 h-4 text-[#92BA56]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
            </svg>
            Import CSV
        </button>

        <a href="{{ route('job-applications.create') }}" 
           class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-semibold text-gray-900 bg-[#92BA56] hover:bg-[#82a74a] rounded-xl transition-colors shadow-md shadow-[#92BA56]/20">
            + Log Application
        </a>

        <!-- Authenticated User & Logout Form -->
        @auth
            <div class="pt-2 border-t border-gray-700/40 flex items-center justify-between px-1 mt-2">
                <a href="{{ route('profile.edit') }}" class="truncate pr-2 group">
                    <span class="block text-xs font-bold text-white group-hover:text-[#92BA56] transition-colors truncate">{{ Auth::user()->name }}</span>
                    <span class="block text-[10px] text-gray-400 truncate">{{ Auth::user()->email }}</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" 
                            title="Log Out"
                            class="p-2 text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 border border-transparent hover:border-rose-500/30 rounded-lg transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </form>
            </div>
        @endauth
    </div>
</aside>