<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth.register')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $rol = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'rol' => ['required', 'string', 'max:255'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        if ($this->rol == 'encargado_ginecologia') {
            $this->redirectIntended(route('ginecologia.index', absolute: false), navigate: true);
        } else if ($this->rol == 'odontologia_consultorio') {
            $this->redirectIntended(route('odontologia.consultorio.index', absolute: false), navigate: true);
        } else if ($this->rol == 'odontologia_almacen') {
            $this->redirectIntended(route('odontologia.almacen.index', absolute: false), navigate: true);
        } else if ($this->rol == 'encargado_servicios') {
            $this->redirectIntended(route('servicios.index', absolute: false), navigate: true);
        }
    }
}; ?>

<div class="flex flex-col gap-6 max-w-md mx-auto my-[.5rem]">
    <header class="text-black text-center">
        <h1 class="text-3xl font-bold mb-2">Crear cuenta</h1>
        <p class="text-zinc-700">Ingresa los siguientes campos para registrar una cuenta</p>
    </header>

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="flex flex-col gap-6">
        <!-- Name -->
        <label for="nombre">Nombre</label>
        <input
            id="nombre"
            wire:model="name"
            type="text"
            required
            autofocus
            autocomplete="name"
            placeholder="Nombre completo"
            class="w-full py-2 px-4 border-1 rounded text-gray-700 border-gray-300"
        />

        <!-- Email Address -->
        <label for="correo">Correo</label>
        <input
            id="correo"
            wire:model="email"
            type="email"
            required
            autocomplete="email"
            placeholder="correo@ejemplo.com"
            class="w-full py-2 px-4 border-1 rounded text-gray-700 border-gray-300"
        />

        <!-- Rol -->
        <label for="rol">Rol</label>
        <select id="rol" wire:model="rol" class="w-full py-2 px-4 border-1 rounded text-gray-700 border-gray-300 cursor-pointer">
            <option value="">-- Seleccione un rol --</option>
            <option value="encargado_ginecologia">Ginecología</option>
            <option value="odontologia_consultorio">Odontología - Consultorio</option>
            <option value="odontologia_almacen">Odontología - Almacén</option>
            <option value="encargado_servicios">Servicios generales</option>
        </select>
        
        <!-- Password -->
        <label for="contraseña">Contraseña</label>
        <input
            id="contraseña"
            wire:model="password"
            type="password"
            required
            autocomplete="new-password"
            viewable
            class="w-full py-2 px-4 border-1 rounded text-gray-700 border-gray-300"
        />

        <!-- Confirm Password -->
        <label for="confirmar-contraseña">Confirmar Contraseña</label>
        <input
            id="confirmar-contraseña"
            wire:model="password_confirmation"
            :label="__('Confirm password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Confirm password')"
            viewable
            class="w-full py-2 px-4 border-1 rounded text-gray-700 border-gray-300"
        />

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full bg-custom-brown cursor-pointer hover:opacity-75 duration-250 text-white">{{ __('Crear cuenta') }}</flux:button>
        </div>
    </form>

    {{-- 
    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        <span class="font-bold text-black">{{ __('Already have an account?') }}</span>
        <flux:link :href="route('login')" wire:navigate class="font-bold text-black">{{ __('Log in') }}</flux:link>
    </div>
     --}}
</div>
