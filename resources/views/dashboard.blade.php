<x-app-layout>
    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Welcome Header Card -->
            <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-indigo-900 p-8 rounded-2xl shadow-xl text-white relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-indigo-505/10 rounded-full blur-2xl"></div>
                <div class="absolute right-20 top-2 w-24 h-24 bg-white/5 rounded-full blur-xl"></div>
                
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-500/20 text-indigo-300 rounded-full text-xs font-semibold uppercase tracking-wider animate-pulse">
                        Selamat Datang Kembali
                    </div>
                    <h1 class="text-3xl font-extrabold tracking-tight">Halo, {{ auth()->user()->name }}!</h1>
                    <p class="text-indigo-200 text-sm max-w-xl font-medium">Anda telah masuk ke panel Raycorp Portal Knowledge. Gunakan menu di bawah untuk mengelola informasi pengetahuan dan mengekstrak berkas.</p>
                </div>
            </div>

            <!-- Quick Access Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Box 1 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-4 hover:shadow-md hover:scale-[1.01] transition duration-200">
                    <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600 w-fit">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-xl font-bold text-slate-800">Knowledge Base</h3>
                        <p class="text-sm text-slate-500">Kelola kumpulan data pengetahuan, cari, edit, atau buat artikel baru dengan fitur unggah file cerdas.</p>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('knowledge.index') }}" wire:navigate class="inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">
                            Buka Kelola Knowledge
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Box 2 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-4 hover:shadow-md hover:scale-[1.01] transition duration-200">
                    <div class="p-3 bg-teal-50 rounded-xl text-teal-600 w-fit">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581a2.25 2.25 0 003.182 0l4.318-4.318a2.25 2.25 0 000-3.182L11.16 3.659A2.25 2.25 0 009.568 3z" />
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-xl font-bold text-slate-800">Kategori Domain</h3>
                        <p class="text-sm text-slate-500">Kelola label pengelompokan domain agar basis data artikel terstruktur dan mudah dinavigasi.</p>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('domains.index') }}" wire:navigate class="inline-flex items-center gap-1.5 text-sm font-semibold text-teal-600 hover:text-teal-800 transition">
                            Buka Kelola Domain
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
