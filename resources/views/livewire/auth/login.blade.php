<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        // Obtener el usuario autenticado
        $user = Auth::user();

        if ($user && $user->rol === 'encargado_servicios') {
            $this->redirect(route('servicios.index'), navigate: true);
            return;
        } elseif ($user && $user->rol === 'odontologia_consultorio') {
            $this->redirect(route('odontologia.consultorio.index'), navigate: true);
            return;
        } elseif ($user && $user->rol === 'odontologia_almacen') {
            $this->redirect(route('odontologia.almacen.index'), navigate: true);
            return;
        } elseif ($user && $user->rol === 'encargado_ginecologia') {
            $this->redirect(route('ginecologia.index'), navigate: true);
            return;
        }

        // Si el usuario no tiene los roles específicos mencionados arriba,
        // o si no se define una redirección para su rol, se usa la redirección por defecto.
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}; ?>

<div class="flex flex-col gap-6 max-w-md mx-auto my-8">
    <header class="text-black text-center">
        <h1 class="text-3xl font-bold mb-2">Iniciar Sesión</h1>
        <p class="text-zinc-700">Ingrese su correo y contraseña designados para iniciar sesión</p>
    </header>

    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="login" class="flex flex-col gap-6">
        <label for="correo" class="text-gray-700">Correo</label>
        <input
            id="correo"
            wire:model="email"
            type="email"
            required
            autofocus
            autocomplete="email"
            placeholder="correo@ejemplo.com"
            class="w-full py-2 px-4 border-1 rounded text-gray-700 border-gray-300"
        />

        <label for="contraseña" class="text-gray-700">Contraseña</label>
        <div class="relative">
            <input
                id="contraseña"
                wire:model="password"
                type="password"
                required
                autocomplete="current-password"
                viewable
                class="w-full py-2 px-4 border-1 rounded text-gray-700 border-gray-300"
            />

            {{--     
            @if (Route::has('password.request'))
                <flux:link class="absolute end-0 top-0 text-sm" :href="route('password.request')" wire:navigate>
                    {{ __('Forgot your password?') }}
                </flux:link>
            @endif
                --}}    
        </div>

        <div class="w-min flex gap-2 cursor-pointer duration-250 hover:opacity-75">
            <input
                id="recuerdame"
                type="checkbox"
                wire:model="remember"
                class="cursor-pointer"
            />
            <label for="recuerdame" class="text-gray-600 text-sm cursor-pointer">Recuérdame</label>
        </div>

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full bg-custom-brown cursor-pointer hover:opacity-75 duration-250 text-white">{{ __('Iniciar sesión') }}</flux:button>
        </div>
    </form>

    {{-- 
    @if (Route::has('register'))
        <div class="hidden space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Don\'t have an account?') }}</span>
            <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
        </div>
    @endif
     --}}
</div>
