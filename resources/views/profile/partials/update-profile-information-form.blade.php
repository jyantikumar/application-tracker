<section>
    <header>
        <h2 class="text-lg font-bold text-white">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-xs text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-xs uppercase tracking-wider text-gray-400 font-bold mb-2">{{ __('Name *') }}</label>
            <input id="name" name="name" type="text" class="w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            <x-input-error class="mt-2 text-xs text-rose-400" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="email" class="block text-xs uppercase tracking-wider text-gray-400 font-bold mb-2">{{ __('Email *') }}</label>
            <input id="email" name="email" type="email" class="w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors" value="{{ old('email', $user->email) }}" required autocomplete="username" />
            <x-input-error class="mt-2 text-xs text-rose-400" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div>
                    <p class="text-xs mt-2 text-amber-400">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-xs text-gray-400 hover:text-white rounded-md focus:outline-none">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-xs text-emerald-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="px-6 py-2.5 text-xs font-semibold text-gray-900 bg-[#92BA56] hover:bg-[#82a74a] rounded-lg transition-colors shadow-md shadow-[#92BA56]/20">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-xs text-emerald-400">
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>