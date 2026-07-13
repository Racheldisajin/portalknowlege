<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <form wire:submit="register" class="space-y-5">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" class="text-slate-300 font-semibold" />
            <x-text-input wire:model="name" id="name" class="block mt-1.5 w-full bg-slate-950 border-slate-800 focus:border-indigo-500 focus:ring-indigo-500 text-slate-100 rounded-xl px-4 py-2.5 shadow-sm transition" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-rose-400" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-slate-300 font-semibold" />
            <x-text-input wire:model="email" id="email" class="block mt-1.5 w-full bg-slate-950 border-slate-800 focus:border-indigo-500 focus:ring-indigo-500 text-slate-100 rounded-xl px-4 py-2.5 shadow-sm transition" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-rose-400" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-slate-300 font-semibold" />

            <x-text-input wire:model="password" id="password" class="block mt-1.5 w-full bg-slate-950 border-slate-800 focus:border-indigo-500 focus:ring-indigo-500 text-slate-100 rounded-xl px-4 py-2.5 shadow-sm transition"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-400" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-slate-300 font-semibold" />

            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1.5 w-full bg-slate-950 border-slate-800 focus:border-indigo-500 focus:ring-indigo-500 text-slate-100 rounded-xl px-4 py-2.5 shadow-sm transition"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-rose-400" />
        </div>

        <div class="flex items-center justify-between pt-2">
            <a class="text-sm text-slate-400 hover:text-white transition duration-150 font-medium" href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="bg-gradient-to-r from-indigo-500 to-cyan-500 hover:from-indigo-600 hover:to-cyan-600 transition duration-200 rounded-xl px-5 py-2.5 text-sm font-semibold shadow-lg text-white border-0">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</div>
