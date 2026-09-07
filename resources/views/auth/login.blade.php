<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-xs text-emerald-400" :status="session('status')" />

    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-white tracking-tight">Welcome Back</h2>
        <p class="text-xs text-gray-400 mt-1">Sign in to manage your job application records</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Email Address *</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors"
                   placeholder="name@example.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-rose-400" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Password *</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors"
                   placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-rose-400" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between text-xs">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-gray-700/80 bg-gray-900/60 text-[#92BA56] focus:ring-[#92BA56] focus:ring-offset-gray-900" name="remember">
                <span class="ms-2 text-gray-400">Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-gray-400 hover:text-[#92BA56] transition-colors" href="{{ route('password.request') }}">
                    Forgot password?
                </a>
            @endif
        </div>

        <div class="pt-2 flex items-center justify-between">
            <a class="text-xs text-gray-400 hover:text-[#92BA56] transition-colors" href="{{ route('register') }}">
                Need an account? Register
            </a>

            <button type="submit" class="px-6 py-2.5 text-xs font-semibold text-gray-900 bg-[#92BA56] hover:bg-[#82a74a] rounded-lg transition-colors shadow-md shadow-[#92BA56]/20">
                Log In
            </button>
        </div>
    </form>
</x-guest-layout>