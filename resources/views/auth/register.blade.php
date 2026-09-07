<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-white tracking-tight">Create an Account</h2>
        <p class="text-xs text-gray-400 mt-1">Start tracking your job applications with user-level security</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Full Name *</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                   class="w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors"
                   placeholder="e.g. Juan Dela Cruz">
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-xs text-rose-400" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Email Address *</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                   class="w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors"
                   placeholder="name@example.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-rose-400" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Password *</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                   class="w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors"
                   placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-rose-400" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Confirm Password *</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                   class="w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors"
                   placeholder="••••••••">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-xs text-rose-400" />
        </div>

        <div class="flex items-center justify-between pt-2">
            <a class="text-xs text-gray-400 hover:text-[#92BA56] transition-colors" href="{{ route('login') }}">
                Already registered? Log in
            </a>

            <button type="submit" class="px-6 py-2.5 text-xs font-semibold text-gray-900 bg-[#92BA56] hover:bg-[#82a74a] rounded-lg transition-colors shadow-md shadow-[#92BA56]/20">
                Register
            </button>
        </div>
    </form>
</x-guest-layout>