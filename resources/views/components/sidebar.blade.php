@props(['active' => 'applications'])

<!-- Fixed Left Sidebar Navigation -->
<aside class="fixed top-0 left-0 h-screen w-64 bg-gray-800/90 backdrop-blur-md border-r border-gray-700/60 z-50 flex flex-col justify-between p-4 hidden lg:flex">
    <div class="space-y-6">
        <!-- Brand Logo -->
        <div class="flex items-center space-x-3 px-2 pt-2">
            <div class="p-2 bg-[#92BA56]/20 rounded-lg border border-[#92BA56]/30">
                <svg class="w-6 h-6 text-[#92BA56]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <span class="font-bold text-xl tracking-tight text-white">Job Tracker</span>
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
        <button @click="IsImportModalOpen = true" 
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