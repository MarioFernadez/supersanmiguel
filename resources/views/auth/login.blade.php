<x-guest-layout>
    <x-authentication-card>
        <div class="mb-8 text-center sm:mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">
                Bienvenido a <span class="text-emerald-600 dark:text-emerald-400">ZonaShop</span>
            </h1>
            
            <div class="mt-2.5">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/40">
                    Sistema POS & Ventas
                </span>
            </div>

            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400 font-normal">
                Ingresa tus credenciales para acceder
            </p>
        </div>

        <x-validation-errors class="mb-4" />

        @if(request('expired'))
            <div class="mb-4 bg-amber-50 border border-amber-200 text-amber-900 px-4 py-3 rounded-2xl text-xs font-medium flex items-center gap-2.5 shadow-sm dark:bg-amber-950/40 dark:border-amber-900/60 dark:text-amber-300">
                <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>Tu sesión ha expirado por inactividad. Por favor, inicia sesión nuevamente para continuar.</span>
            </div>
        @endif

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-emerald-600 dark:text-emerald-400">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Usuario / Email') }}" class="font-medium text-xs text-gray-600 dark:text-gray-400 mb-1" />
                <x-input id="email" class="block mt-1 w-full py-2.5 px-3.5 rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Contraseña') }}" class="font-medium text-xs text-gray-600 dark:text-gray-400 mb-1" />
                <x-input id="password" class="block mt-1 w-full py-2.5 px-3.5 rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" class="rounded-lg text-emerald-600 focus:ring-emerald-500/20" />
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400 font-normal">{{ __('Recordarme') }}</span>
                </label>
            </div>

            <div class="mt-6">
                <button type="submit" class="w-full justify-center inline-flex items-center px-4 py-3 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-xl font-semibold text-sm transition shadow-sm hover:shadow focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    {{ __('Iniciar Sesión') }}
                </button>
            </div>
            
            <div class="mt-6 text-center text-xs text-gray-500 dark:text-gray-400">
                ZonaPOS® es un producto digital desarrollado por <a href="https://soteweb.com" target="_blank" class="text-green-500 hover:text-green-600 font-bold hover:underline">Soteweb Tecnología</a>
                <div class="mt-2 flex justify-center items-center gap-1">
                    Made in <img src="https://flagcdn.com/w20/py.png" srcset="https://flagcdn.com/w40/py.png 2x" width="20" alt="Paraguay">
                </div>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
