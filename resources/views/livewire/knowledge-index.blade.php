<?php

use App\Models\Domain;
use App\Models\Knowledge;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

use App\Models\KnowledgeFile;
use App\Services\DocumentTextExtractor;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    public string $search = '';
    public ?int $domainFilter = null;
    public $allDomains;
    
    public int $totalCount = 0;
    public int $fileCount = 0;
    public int $domainCount = 0;

    public function mount(): void
    {
        $this->allDomains = Domain::orderBy('name')->get();
        $this->updateStats();
    }

    public function updateStats(): void
    {
        $this->totalCount = Knowledge::count();
        $this->fileCount = KnowledgeFile::count();
        $this->domainCount = Domain::count();
    }

    public function withKnowledge()
    {
        return Knowledge::with(['domains', 'files'])
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->domainFilter, fn($q) => $q->whereHas('domains', fn($q) => $q->where('domains.id', $this->domainFilter)))
            ->latest()
            ->paginate(10);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedDomainFilter(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $knowledge = Knowledge::with('files')->findOrFail($id);

        foreach ($knowledge->files as $file) {
            try {
                $extension = strtolower(pathinfo($file->file_path, PATHINFO_EXTENSION));
                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif']);

                if ($isImage) {
                    Storage::disk('public')->delete($file->file_path);
                } else {
                    $urlPath = parse_url($file->file_path, PHP_URL_PATH);
                    $path = preg_replace('/^\/files\//', '', $urlPath);
                    DocumentTextExtractor::deleteFromRayCloud($path);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Gagal menghapus berkas saat menghapus knowledge: " . $e->getMessage());
            }
            $file->delete();
        }

        $knowledge->domains()->detach();
        $knowledge->delete();
        
        $this->updateStats();
    }
}; ?>

<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        
        <!-- Welcome & Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gradient-to-r from-indigo-800 via-indigo-900 to-slate-900 p-8 rounded-2xl shadow-xl text-white relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-indigo-500/10 rounded-full blur-2xl"></div>
            <div class="absolute right-20 top-2 w-24 h-24 bg-white/5 rounded-full blur-xl"></div>
            
            <div class="space-y-1">
                <h1 class="text-3xl font-extrabold tracking-tight">Portal Knowledge News</h1>
                <p class="text-indigo-200 text-sm max-w-xl">Pusat dokumentasi, ekstraksi berkas, dan artikel pengetahuan terintegrasi.</p>
            </div>
            
            <div>
                <a href="{{ route('knowledge.create') }}" wire:navigate class="inline-flex items-center gap-2 px-5 py-3 bg-white text-indigo-950 font-semibold text-sm rounded-xl shadow-md hover:bg-indigo-50 hover:scale-105 active:scale-95 transition-all duration-150">
                    <svg class="h-5 w-5 text-indigo-950" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Data
                </a>
            </div>
        </div>

        <!-- Dashboard Stats Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Stat Card 1 -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-5 hover:shadow-md transition duration-200">
                <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Knowledge</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-0.5">{{ $totalCount }}</h3>
                </div>
            </div>

            <!-- Stat Card 2 -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-5 hover:shadow-md transition duration-200">
                <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Berkas Terlampir</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-0.5">{{ $fileCount }}</h3>
                </div>
            </div>

            <!-- Stat Card 3 -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-5 hover:shadow-md transition duration-200">
                <div class="p-3 bg-rose-50 rounded-xl text-rose-600">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581a2.25 2.25 0 003.182 0l4.318-4.318a2.25 2.25 0 000-3.182L11.16 3.659A2.25 2.25 0 009.568 3z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Kategori Domain</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-0.5">{{ $domainCount }}</h3>
                </div>
            </div>
        </div>

        <!-- Filter & Search Widget -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="relative w-full sm:w-80">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input wire:model.live="search" type="text" class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150" placeholder="Cari judul knowledge..." />
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider hidden md:inline">Filter Domain:</span>
                <select wire:model.live="domainFilter" class="block w-full sm:w-56 px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150">
                    <option value="">Semua Domain</option>
                    @foreach ($allDomains as $domain)
                        <option value="{{ $domain->id }}">{{ $domain->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Table Wrapper Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            @php $allKnowledge = $this->withKnowledge(); @endphp

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-slate-600">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-100 text-left font-semibold text-slate-500 uppercase tracking-wider text-xs">
                            <th class="py-4 px-6">Judul</th>
                            <th class="py-4 px-6">Domain</th>
                            <th class="py-4 px-6">Dokumen Pendukung</th>
                            <th class="py-4 px-6">Tanggal Dibuat</th>
                            <th class="py-4 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($allKnowledge as $k)
                            <tr class="hover:bg-slate-50/50 transition duration-150">
                                <td class="py-4 px-6">
                                    <span class="font-semibold text-slate-800 hover:text-indigo-600 transition">{{ $k->title }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach ($k->domains as $d)
                                            @php
                                                // Create a stable random background color hash based on domain name
                                                $colors = [
                                                    'Programming' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                                    'Design' => 'bg-pink-50 text-pink-700 border-pink-100',
                                                    'DevOps' => 'bg-amber-50 text-amber-700 border-amber-100',
                                                ];
                                                $colorClass = $colors[$d->name] ?? 'bg-slate-50 text-slate-600 border-slate-100';
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold border {{ $colorClass }}">
                                                {{ $d->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    @if ($k->files->isNotEmpty())
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach ($k->files as $file)
                                                @php
                                                    $ext = strtolower(pathinfo($file->file_path, PATHINFO_EXTENSION));
                                                    
                                                    // Determine badge based on file extension
                                                    $badgeClasses = match ($ext) {
                                                        'pdf' => 'bg-red-50 text-red-700 border-red-100',
                                                        'docx', 'doc' => 'bg-blue-50 text-blue-700 border-blue-100',
                                                        'xlsx', 'xls' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                                        'pptx', 'ppt' => 'bg-orange-50 text-orange-700 border-orange-100',
                                                        'jpg', 'jpeg', 'png', 'webp', 'gif' => 'bg-purple-50 text-purple-700 border-purple-100',
                                                        'txt', 'md', 'markdown' => 'bg-slate-50 text-slate-700 border-slate-200',
                                                        default => 'bg-slate-50 text-slate-600 border-slate-100',
                                                    };

                                                    $isRayCloud = str_starts_with($file->file_path, 'http');
                                                    $targetUrl = $isRayCloud ? $file->file_path : asset('storage/' . $file->file_path);
                                                @endphp
                                                <a href="{{ $targetUrl }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badgeClasses }} hover:scale-105 transition duration-100" title="{{ basename($file->file_path) }} (Klik untuk buka)">
                                                    {{ strtoupper($ext) }}
                                                    @if ($isRayCloud)
                                                        <!-- RayCloud Cloud Icon -->
                                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 004.5 4.5H18a3.75 3.75 0 001.332-7.257 3 3 0 00-3.758-3.848 5.25 5.25 0 00-10.233 2.33A4.502 4.502 0 002.25 15z" />
                                                        </svg>
                                                    @else
                                                        <!-- Local Home/Server Icon -->
                                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 3h13.5m-13.5-6h13.5m-13.5-3h13.5m-13.5-3H9m-3.75 15H18a2.25 2.25 0 002.25-2.25V5.25A2.25 2.25 0 0018 3H6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 006 18z" />
                                                        </svg>
                                                    @endif
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 font-medium italic">Tidak ada berkas</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-slate-400 text-xs font-medium">
                                    {{ $k->created_at->format('d M Y') }}
                                </td>
                                <td class="py-4 px-6 text-right space-x-1">
                                    <a href="{{ route('knowledge.edit', $k) }}" wire:navigate class="inline-flex items-center px-3 py-1.5 text-xs font-semibold bg-slate-50 text-indigo-600 rounded-lg hover:bg-indigo-50 border border-slate-100 hover:border-indigo-100 transition duration-150">
                                        Edit
                                    </a>
                                    <button wire:confirm="Hapus data knowledge ini?" wire:click="delete({{ $k->id }})" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold bg-slate-50 text-red-600 rounded-lg hover:bg-red-50 border border-slate-100 hover:border-red-100 transition duration-150">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.008 1.24l.885 1.77a2.25 2.25 0 002.007 1.24h1.98a2.25 2.25 0 002.007-1.24l.885-1.77a2.25 2.25 0 012.007-1.24h3.86m-18 0h18" />
                                        </svg>
                                        <p class="text-sm font-semibold text-slate-500">Belum ada data knowledge ditemukan</p>
                                        <p class="text-xs text-slate-400">Silakan tambahkan data baru dengan menekan tombol Tambah Data.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination block -->
            @if ($allKnowledge->hasPages())
                <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100">
                    {{ $allKnowledge->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

