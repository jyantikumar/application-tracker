<x-app-layout active="profile">
    <!-- Main Content Wrapper with Sidebar Padding -->
    <div class="lg:pl-64 flex flex-col min-h-screen" x-data="{ ActiveTab: 'profile' }">
        
        <!-- Mobile Header -->
        <header class="lg:hidden bg-gray-800/80 backdrop-blur-md border-b border-gray-700/50 p-4 sticky top-0 z-40 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="p-1.5 bg-[#92BA56]/20 rounded-lg border border-[#92BA56]/30">
                    <svg class="w-5 h-5 text-[#92BA56]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <span class="font-bold text-lg tracking-tight text-white">Profile Settings</span>
            </div>
            <div class="flex items-center gap-3">
        <a href="{{ route('job-applications.create') }}" class="px-2.5 py-1.5 text-xs font-semibold text-gray-900 bg-[#92BA56] hover:bg-[#82a74a] rounded-lg transition-colors">+ Log</a>
        <!-- Mobile Menu Toggle Button -->
        <button @click="IsMobileMenuOpen = true" type="button" class="p-1 text-gray-400 hover:text-white rounded-lg focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>
        </header>

        <main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

            <!-- Page Header -->
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Account & Profile Settings</h1>
                <p class="text-gray-400 text-sm mt-0.5">Manage your personal account credentials, security details, and profile choices.</p>
            </div>

            <!-- Main Layout with Vertical Tabs for Desktop -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
                
                <!-- Tab Navigation Buttons -->
                <nav class="flex lg:flex-col gap-2 overflow-x-auto pb-2 lg:pb-0 border-b lg:border-b-0 border-gray-700/60">
                    <button 
                        @click="ActiveTab = 'profile'"
                        :class="ActiveTab === 'profile' 
                            ? 'bg-[#92BA56]/10 text-[#92BA56]' 
                            : 'text-gray-400 hover:text-gray-200 hover:bg-gray-800/40 border-transparent'"
                        class="flex items-center gap-3 px-4 py-3 text-xs font-bold rounded-xl transition-all whitespace-nowrap">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Profile Information</span>
                    </button>

                    <button 
                        @click="ActiveTab = 'security'"
                        :class="ActiveTab === 'security' 
                            ? 'bg-[#92BA56]/10 text-[#92BA56]' 
                            : 'text-gray-400 hover:text-gray-200 hover:bg-gray-800/40 border-transparent'"
                        class="flex items-center gap-3 px-4 py-3 text-xs font-bold rounded-xl transition-all whitespace-nowrap">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span>Security & Password</span>
                    </button>

                    <button 
                        @click="ActiveTab = 'danger'"
                        :class="ActiveTab === 'danger' 
                            ? 'bg-rose-500/10 text-rose-400' 
                            : 'text-gray-400 hover:text-rose-400 hover:bg-rose-950/20 border-transparent'"
                        class="flex items-center gap-3 px-4 py-3 text-xs font-bold rounded-xl transition-all whitespace-nowrap">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <span>Delete Account</span>
                    </button>
                </nav>

                <!-- Tab Content Panels -->
                <div class="lg:col-span-3">
                    <!-- Tab 1: Profile Information -->
                    <div x-show="ActiveTab === 'profile'" class="bg-gray-800/40 backdrop-blur-sm border border-gray-700/60 rounded-xl p-6 sm:p-8 shadow-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>

                    <!-- Tab 2: Security & Password -->
                    <div x-show="ActiveTab === 'security'" x-cloak class="bg-gray-800/40 backdrop-blur-sm border border-gray-700/60 rounded-xl p-6 sm:p-8 shadow-xl">
                        @include('profile.partials.update-password-form')
                    </div>

                    <!-- Tab 3: Danger Zone -->
                    <div x-show="ActiveTab === 'danger'" x-cloak class="bg-rose-950/10 border border-rose-500/20 rounded-xl p-6 sm:p-8 shadow-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>

            </div>

        </main>
    </div>
</x-app-layout>