<?php

use App\Models\Domain;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component {
    public ?int $editId = null;

    #[Rule('required|string|max:255')]
    public string $name = '';

    public $domainList;

    public function mount(): void
    {
        $this->domainList = Domain::orderBy('name')->get();
    }

    public function create(): void
    {
        $this->validate();

        Domain::create(['name' => $this->name]);

        $this->reset('name');
        $this->domainList = Domain::orderBy('name')->get();
    }

    public function edit(int $id): void
    {
        $domain = Domain::findOrFail($id);
        $this->editId = $domain->id;
        $this->name = $domain->name;
    }

    public function update(): void
    {
        $this->validate();

        $domain = Domain::findOrFail($this->editId);
        $domain->update(['name' => $this->name]);

        $this->reset('name', 'editId');
        $this->domainList = Domain::orderBy('name')->get();
    }

    public function cancel(): void
    {
        $this->reset('name', 'editId');
    }

    public function delete(int $id): void
    {
        $domain = Domain::findOrFail($id);
        $domain->knowledge()->detach();
        $domain->delete();

        $this->domainList = Domain::orderBy('name')->get();
    }
}; ?>

<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gradient-to-r from-teal-800 via-teal-900 to-slate-900 p-8 rounded-2xl shadow-xl text-white relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-teal-500/10 rounded-full blur-2xl"></div>
            <div class="absolute right-20 top-2 w-24 h-24 bg-white/5 rounded-full blur-xl"></div>
            
            <div class="space-y-1">
                <h1 class="text-3xl font-extrabold tracking-tight">Kategori Domain</h1>
                <p class="text-teal-200 text-sm max-w-xl">Kelola kategori pengelompokan knowledge dalam sistem.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Form Card (Add/Edit) -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 h-fit space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">{{ $editId ? 'Ubah Kategori' : 'Tambah Kategori Baru' }}</h3>
                    <p class="text-xs text-slate-500 mt-1">{{ $editId ? 'Perbarui nama kategori yang sudah ada.' : 'Tambahkan kategori baru untuk pengelompokan knowledge.' }}</p>
                </div>

                <form wire:submit="{{ $editId ? 'update' : 'create' }}" class="space-y-4">
                    <div>
                        <x-input-label for="name" value="Nama Domain / Kategori" />
                        <x-text-input wire:model="name" id="name" class="mt-1.5 block w-full shadow-sm" type="text" placeholder="Contoh: Programming, DevOps, Design" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <x-primary-button class="w-full justify-center">{{ $editId ? 'Simpan Perubahan' : 'Tambah Kategori' }}</x-primary-button>
                        
                        @if ($editId)
                            <button type="button" wire:click="cancel" class="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-semibold rounded-lg hover:bg-slate-200 transition duration-150">
                                Batal
                            </button>
                        @endif
                    </div>
                </form>
            </div>

            <!-- List Card -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-slate-600">
                        <thead>
                            <tr class="bg-slate-50/70 border-b border-slate-100 text-left font-semibold text-slate-500 uppercase tracking-wider text-xs">
                                <th class="py-4 px-6">Nama Kategori</th>
                                <th class="py-4 px-6">Slug</th>
                                <th class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($domainList as $d)
                                <tr class="hover:bg-slate-50/50 transition duration-150">
                                    <td class="py-4 px-6">
                                        <span class="font-semibold text-slate-800">{{ $d->name }}</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="font-mono text-xs text-slate-400 bg-slate-50 px-2 py-1 rounded border border-slate-100">{{ $d->slug }}</span>
                                    </td>
                                    <td class="py-4 px-6 text-right space-x-1">
                                        <button wire:click="edit({{ $d->id }})" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold bg-slate-50 text-indigo-600 rounded-lg hover:bg-indigo-50 border border-slate-100 hover:border-indigo-100 transition duration-150">
                                            Edit
                                        </button>
                                        <button wire:confirm="Hapus kategori '{{ $d->name }}'? Kategori ini akan dilepas dari semua data knowledge terkait." wire:click="delete({{ $d->id }})" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold bg-slate-50 text-red-600 rounded-lg hover:bg-red-50 border border-slate-100 hover:border-red-100 transition duration-150">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-12 text-center">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581a2.25 2.25 0 003.182 0l4.318-4.318a2.25 2.25 0 000-3.182L11.16 3.659A2.25 2.25 0 009.568 3z" />
                                            </svg>
                                            <p class="text-sm font-semibold text-slate-500">Belum ada kategori domain</p>
                                            <p class="text-xs text-slate-400">Silakan tambahkan data melalui form di sebelah kiri.</p>
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

