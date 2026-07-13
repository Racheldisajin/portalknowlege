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
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-6 rounded-2xl shadow-xl text-white relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-indigo-500/10 rounded-full blur-2xl"></div>
            <div class="absolute right-20 top-2 w-24 h-24 bg-white/5 rounded-full blur-xl"></div>
            
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 relative z-10">
                <div>
                    <h2 class="font-extrabold text-2xl tracking-tight leading-tight">
                        {{ __('Kelola Akses Pengguna') }}
                    </h2>
                    <p class="text-sm text-indigo-200 mt-1 font-medium">Manajemen dan pembatasan hak akses akun internal Raycorp Portal</p>
                </div>
                <div class="flex items-center gap-2 bg-indigo-500/20 border border-indigo-500/30 rounded-2xl px-4 py-2 text-xs font-semibold text-indigo-300">
                    <svg class="h-4 w-4 text-indigo-400 animate-pulse" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Registrasi Publik Nonaktif
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50/50 min-h-[calc(100vh-4rem)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <!-- Statistics Row -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Stat Card 1 -->
                <div class="bg-gradient-to-br from-indigo-50/70 via-white to-white rounded-2xl shadow-sm border border-indigo-100 p-5 flex items-center justify-between hover:shadow-md transition duration-200">
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-indigo-400 uppercase tracking-wider">Total Pengguna</span>
                        <div class="text-3xl font-black text-indigo-600">{{ \App\Models\User::count() }}</div>
                        <p class="text-xs text-slate-500 font-medium">Akun terdaftar dalam sistem</p>
                    </div>
                    <div class="p-3.5 bg-indigo-100 text-indigo-600 rounded-2xl shadow-sm">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Stat Card 2 -->
                <div class="bg-gradient-to-br from-rose-50/70 via-white to-white rounded-2xl shadow-sm border border-rose-100 p-5 flex items-center justify-between hover:shadow-md transition duration-200">
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-rose-400 uppercase tracking-wider">Metode Registrasi</span>
                        <div class="text-lg font-black text-rose-600 flex items-center gap-1.5 pt-1">
                            <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                            Tertutup (Admin Only)
                        </div>
                        <p class="text-xs text-slate-500 font-medium">Hanya admin yang dapat mendaftarkan</p>
                    </div>
                    <div class="p-3.5 bg-rose-100 text-rose-600 rounded-2xl shadow-sm">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </div>
                </div>

                <!-- Stat Card 3 -->
                <div class="bg-gradient-to-br from-emerald-50/70 via-white to-white rounded-2xl shadow-sm border border-emerald-100 p-5 flex items-center justify-between hover:shadow-md transition duration-200">
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Aktivitas Sistem</span>
                        <div class="text-lg font-black text-emerald-600 flex items-center gap-1.5 pt-1">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-ping"></span>
                            Online & Secure
                        </div>
                        <p class="text-xs text-slate-500 font-medium">Bekerja pada database internal</p>
                    </div>
                    <div class="p-3.5 bg-emerald-100 text-emerald-600 rounded-2xl shadow-sm">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 3h13.5m-13.5-6h13.5m-13.5-3h13.5m-13.5-3H9m-3.75 15H18a2.25 2.25 0 002.25-2.25V5.25A2.25 2.25 0 0018 3H6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 006 18z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Status Alerts -->
            @if (session('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-100/80 text-emerald-800 rounded-2xl text-sm font-semibold shadow-sm flex items-center gap-3">
                    <div class="p-1.5 bg-emerald-100 text-emerald-700 rounded-lg">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-rose-50 border border-rose-100/80 text-rose-800 rounded-2xl text-sm font-semibold shadow-sm flex items-center gap-3">
                    <div class="p-1.5 bg-rose-100 text-rose-700 rounded-lg">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                    </div>
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left panel: Add User Form -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100/80 border-t-4 border-t-indigo-500 p-6 h-fit space-y-6">
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-lg">Tambah User Baru</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Buat akun untuk akses internal sistem</p>
                    </div>

                    <form wire:submit="createUser" class="space-y-4">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 pl-1">Nama Lengkap</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                </span>
                                <input wire:model="name" id="name" type="text" placeholder="Masukkan nama lengkap"
                                       class="w-full bg-slate-50 border border-slate-200/80 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 text-slate-800 rounded-2xl pl-10 pr-4 py-2.5 shadow-sm text-sm transition outline-none" />
                            </div>
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 pl-1">Alamat Email</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                    </svg>
                                </span>
                                <input wire:model="email" id="email" type="email" placeholder="contoh@domain.com"
                                       class="w-full bg-slate-50 border border-slate-200/80 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 text-slate-800 rounded-2xl pl-10 pr-4 py-2.5 shadow-sm text-sm transition outline-none" />
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 pl-1">Password</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                    </svg>
                                </span>
                                <input wire:model="password" id="password" type="password" placeholder="Minimal 8 karakter"
                                       class="w-full bg-slate-50 border border-slate-200/80 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 text-slate-800 rounded-2xl pl-10 pr-4 py-2.5 shadow-sm text-sm transition outline-none" />
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-1" />
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 pl-1">Konfirmasi Password</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </span>
                                <input wire:model="password_confirmation" id="password_confirmation" type="password" placeholder="Masukkan kembali password"
                                       class="w-full bg-slate-50 border border-slate-200/80 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 text-slate-800 rounded-2xl pl-10 pr-4 py-2.5 shadow-sm text-sm transition outline-none" />
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit"
                                    style="background: linear-gradient(135deg, #6366f1 0%, #06b6d4 50%, #10b981 100%); border: 0; padding: 12px 16px; border-radius: 16px; color: #ffffff; font-size: 0.875rem; font-weight: 700; cursor: pointer; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.35); transition: all 0.2s; width: 100%; text-align: center; display: block;"
                                    onmouseover="this.style.opacity='0.95'; this.style.transform='translateY(-1px)';"
                                    onmouseout="this.style.opacity='1'; this.style.transform='translateY(0)';">
                                Simpan & Aktifkan User
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right panel: Users List Table -->
                <div class="lg:col-span-2 space-y-5">
                    
                    <!-- Search & Stats Bar -->
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-100/80 border-t-4 border-t-indigo-500 p-4 flex items-center justify-between gap-4">
                        <div class="relative w-full max-w-md">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input wire:model.live="search" type="text" placeholder="Cari nama atau email pengguna..."
                                   class="w-full pl-11 pr-4 py-2 bg-slate-50 border border-slate-200/80 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 text-slate-800 rounded-2xl text-sm transition outline-none" />
                        </div>
                        <div class="text-xs font-semibold text-slate-400 bg-slate-50 border border-slate-100 rounded-xl px-3.5 py-1.5 shrink-0">
                            Total: {{ count($this->withUsers()) }} Pengguna
                        </div>
                    </div>

                    <!-- Users Table Card -->
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-100/80 border-t-4 border-t-emerald-500 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-slate-600">
                                <thead>
                                    <tr class="bg-slate-50/50 border-b border-slate-100 text-left font-bold text-slate-400 uppercase tracking-widest text-[10px]">
                                        <th class="py-4.5 px-6">Nama Pengguna</th>
                                        <th class="py-4.5 px-6">Alamat Email</th>
                                        <th class="py-4.5 px-6">Hak Akses</th>
                                        <th class="py-4.5 px-6">Tanggal Terdaftar</th>
                                        <th class="py-4.5 px-6 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse ($this->withUsers() as $user)
                                        <tr class="hover:bg-slate-50/40 transition duration-150">
                                            <!-- Name -->
                                            <td class="py-4 px-6">
                                                <div class="flex items-center gap-3">
                                                    @php
                                                        // alternate colorful gradients based on user id
                                                        $gradient = $user->id % 2 === 0 
                                                            ? 'from-indigo-500 to-cyan-500' 
                                                            : 'from-fuchsia-500 to-indigo-500';
                                                    @endphp
                                                    <div class="h-9 w-9 rounded-2xl bg-gradient-to-tr {{ $gradient }} flex items-center justify-center font-extrabold text-white text-sm shadow-sm shrink-0">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span class="font-semibold text-slate-800 flex items-center">
                                                            {{ $user->name }}
                                                            @if ($user->id === auth()->id())
                                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-indigo-500 text-white shadow-sm uppercase tracking-wider">Anda</span>
                                                            @endif
                                                        </span>
                                                        <span class="text-[10px] text-slate-400 font-medium">User ID: #{{ $user->id }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <!-- Email -->
                                            <td class="py-4 px-6 font-medium text-slate-700">
                                                {{ $user->email }}
                                            </td>

                                            <!-- Role Badge -->
                                            <td class="py-4 px-6">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                    Staff Akses
                                                </span>
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
                                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold bg-slate-50 text-rose-600 rounded-xl hover:bg-rose-50 border border-slate-100 hover:border-rose-100 transition duration-150 cursor-pointer">
                                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                        </svg>
                                                        Hapus
                                                    </button>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold bg-slate-50 text-slate-400 rounded-xl border border-slate-100 select-none">
                                                        <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                                        </svg>
                                                        Terkunci
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-12 text-center">
                                                <div class="flex flex-col items-center justify-center gap-2">
                                                    <svg class="h-10 w-10 text-slate-300 animate-bounce" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                                    </svg>
                                                    <p class="text-sm font-semibold text-slate-500">Tidak ada pengguna ditemukan</p>
                                                    <p class="text-xs text-slate-400">Gunakan kata kunci pencarian yang berbeda.</p>
                                                </div>
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

