<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public string $search = '';

    public function createUser(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'min:8', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Reset input fields
        $this->reset(['name', 'email', 'password', 'password_confirmation']);

        session()->flash('status', 'User baru berhasil ditambahkan!');
    }

    public function deleteUser(int $id): void
    {
        if ($id === auth()->id()) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
            return;
        }

        $user = User::find($id);
        if ($user) {
            $user->delete();
            session()->flash('status', 'User berhasil dihapus!');
        }
    }

    public function withUsers()
    {
        return User::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->get();
    }
}; ?>

<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Kelola Pengguna') }}
            </h2>
            <p class="text-xs text-slate-500 font-medium">Manajemen akun internal portal raycorp</p>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-[calc(100vh-4rem)]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Status Alerts -->
            @if (session('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-xl text-sm font-semibold shadow-sm flex items-center gap-2">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-rose-50 border border-rose-100 text-rose-700 rounded-xl text-sm font-semibold shadow-sm flex items-center gap-2">
                    <svg class="h-5 w-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left panel: Add User Form -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 h-fit">
                    <div class="mb-6">
                        <h3 class="font-bold text-slate-800 text-lg">Tambah User Baru</h3>
                        <p class="text-xs text-slate-400 mt-1">Buat akun akses internal baru</p>
                    </div>

                    <form wire:submit="createUser" class="space-y-4">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                            <input wire:model="name" id="name" type="text" placeholder="Masukkan nama user"
                                   class="w-full bg-slate-50/50 border border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-slate-800 rounded-xl px-4 py-2.5 shadow-sm text-sm transition outline-none" />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Alamat Email</label>
                            <input wire:model="email" id="email" type="email" placeholder="contoh@domain.com"
                                   class="w-full bg-slate-50/50 border border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-slate-800 rounded-xl px-4 py-2.5 shadow-sm text-sm transition outline-none" />
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Password</label>
                            <input wire:model="password" id="password" type="password" placeholder="Minimal 8 karakter"
                                   class="w-full bg-slate-50/50 border border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-slate-800 rounded-xl px-4 py-2.5 shadow-sm text-sm transition outline-none" />
                            <x-input-error :messages="$errors->get('password')" class="mt-1" />
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Konfirmasi Password</label>
                            <input wire:model="password_confirmation" id="password_confirmation" type="password" placeholder="Ulangi password"
                                   class="w-full bg-slate-50/50 border border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-slate-800 rounded-xl px-4 py-2.5 shadow-sm text-sm transition outline-none" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit"
                                    class="w-full bg-gradient-to-r from-indigo-500 to-cyan-500 hover:from-indigo-600 hover:to-cyan-600 text-white rounded-xl py-3 px-4 font-semibold text-sm shadow-md hover:shadow-lg hover:-translate-y-0.5 transition duration-150 text-center">
                                Simpan Akun
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right panel: Users List Table -->
                <div class="lg:col-span-2 space-y-4">
                    
                    <!-- Search Bar -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 flex items-center justify-between">
                        <div class="relative w-full max-w-md">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input wire:model.live="search" type="text" placeholder="Cari nama atau email pengguna..."
                                   class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-slate-800 rounded-xl text-sm transition outline-none" />
                        </div>
                        <div class="text-xs font-semibold text-slate-400">
                            Total: {{ count($this->withUsers()) }} User
                        </div>
                    </div>

                    <!-- Users Table -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-slate-600">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-100 text-left font-semibold text-slate-500 uppercase tracking-wider text-xs">
                                        <th class="py-4 px-6">Nama Pengguna</th>
                                        <th class="py-4 px-6">Alamat Email</th>
                                        <th class="py-4 px-6">Tanggal Terdaftar</th>
                                        <th class="py-4 px-6 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse ($this->withUsers() as $user)
                                        <tr class="hover:bg-slate-50/50 transition">
                                            <!-- Name -->
                                            <td class="py-4 px-6 font-semibold text-slate-800">
                                                <div class="flex items-center gap-2.5">
                                                    <div class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center font-bold text-indigo-600 text-sm">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        {{ $user->name }}
                                                        @if ($user->id === auth()->id())
                                                            <span class="ml-1 text-[10px] bg-indigo-50 text-indigo-700 border border-indigo-100 px-1.5 py-0.5 rounded font-bold">Anda</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <!-- Email -->
                                            <td class="py-4 px-6">
                                                {{ $user->email }}
                                            </td>

                                            <!-- Created At -->
                                            <td class="py-4 px-6 text-slate-400 text-xs font-medium">
                                                {{ $user->created_at->format('d M Y, H:i') }}
                                            </td>

                                            <!-- Actions -->
                                            <td class="py-4 px-6 text-right">
                                                @if ($user->id !== auth()->id())
                                                    <button wire:confirm="Hapus pengguna ini secara permanen?" 
                                                            wire:click="deleteUser({{ $user->id }})" 
                                                            class="inline-flex items-center px-3 py-1.5 text-xs font-semibold bg-slate-50 text-rose-600 rounded-lg hover:bg-rose-50 border border-slate-100 hover:border-rose-100 transition duration-150">
                                                        Hapus
                                                    </button>
                                                @else
                                                    <span class="text-xs text-slate-300 font-semibold italic">Akses Terkunci</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-12 text-center text-slate-400 italic">
                                                Tidak ada pengguna ditemukan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
