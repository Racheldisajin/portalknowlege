<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Raycorp Portal Knowledge</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-950 text-slate-100 font-sans antialiased min-h-screen relative overflow-hidden flex flex-col justify-between">
        
        <!-- Glowing background spots -->
        <div class="absolute top-[-20%] left-[-10%] w-[50%] h-[50%] bg-indigo-600/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-emerald-600/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute top-[30%] right-[10%] w-[30%] h-[30%] bg-cyan-600/5 rounded-full blur-[120px] pointer-events-none"></div>

        <!-- Header -->
        <header class="relative z-10 max-w-7xl mx-auto w-full px-6 py-6 flex items-center justify-between border-b border-slate-900/50">
            <div class="flex items-center gap-3">
                <x-application-logo class="h-9 w-auto" />
                <span class="font-extrabold tracking-wider bg-gradient-to-r from-indigo-400 via-cyan-400 to-emerald-400 bg-clip-text text-transparent text-lg">RAYCORP PORTAL</span>
            </div>
            
            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-indigo-600 text-white font-semibold text-sm rounded-xl shadow-lg hover:bg-indigo-500 hover:scale-105 active:scale-95 transition-all duration-150">
                            Buka Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-300 hover:text-white transition">
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-4 py-2 bg-slate-900 text-slate-100 border border-slate-800 font-semibold text-sm rounded-xl hover:bg-slate-850 hover:border-slate-700 transition">
                                Register
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </header>

        <!-- Hero Section -->
        <main class="relative z-10 max-w-7xl mx-auto w-full px-6 py-16 flex-1 flex flex-col lg:flex-row items-center justify-between gap-12">
            <div class="flex-1 space-y-8 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 rounded-full text-xs font-semibold uppercase tracking-wider">
                    ⚡ New: Webhook OCR & Native PDF/Office Parser
                </div>
                
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-none text-white">
                    Pusat Pengetahuan <br class="hidden sm:inline" />
                    <span class="bg-gradient-to-r from-indigo-400 via-cyan-400 to-emerald-400 bg-clip-text text-transparent">Cerdas & Terintegrasi</span>
                </h1>
                
                <p class="text-slate-400 text-base sm:text-lg max-w-xl leading-relaxed mx-auto lg:mx-0">
                    Unggah berbagai jenis dokumen pendukung Anda (Gambar, PDF, Word, Excel, PowerPoint, hingga File Teks) dan sistem akan mengekstrak kontennya secara otomatis menggunakan teknologi OCR cerdas dan parser dokumen berkecepatan tinggi.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                    @auth
                        <a href="{{ route('knowledge.index') }}" class="w-full sm:w-auto px-6 py-3.5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white font-bold text-base rounded-xl shadow-xl hover:from-indigo-500 hover:to-indigo-600 hover:scale-105 active:scale-95 transition-all duration-150 text-center">
                            Mulai Cari Knowledge
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-6 py-3.5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white font-bold text-base rounded-xl shadow-xl hover:from-indigo-500 hover:to-indigo-600 hover:scale-105 active:scale-95 transition-all duration-150 text-center">
                            Masuk Sekarang
                        </a>
                    @endauth
                    <a href="#fitur" class="w-full sm:w-auto px-6 py-3.5 bg-slate-900 text-slate-200 border border-slate-800 font-semibold text-base rounded-xl hover:bg-slate-850 hover:border-slate-700 transition text-center">
                        Pelajari Selengkapnya
                    </a>
                </div>
            </div>

            <!-- Dashboard Mockup Image -->
            <div class="flex-1 w-full relative">
                <div class="absolute inset-0 bg-indigo-500/10 rounded-3xl blur-2xl"></div>
                <div class="relative bg-slate-900/60 border border-slate-800 p-2.5 rounded-3xl shadow-2xl backdrop-blur-xl">
                    <!-- Fake browser chrome -->
                    <div class="flex items-center gap-1.5 px-4 py-2 border-b border-slate-800/80">
                        <div class="w-3 h-3 rounded-full bg-rose-500/80"></div>
                        <div class="w-3 h-3 rounded-full bg-amber-500/80"></div>
                        <div class="w-3 h-3 rounded-full bg-emerald-500/80"></div>
                        <div class="ml-4 bg-slate-950/80 border border-slate-850 text-[10px] text-slate-500 px-3 py-1 rounded-lg w-52 truncate">raycorp-portal.test/knowledge/create</div>
                    </div>
                    
                    <!-- Dropzone Mockup inside image container -->
                    <div class="p-6 bg-slate-950/50 rounded-2xl mt-3 border border-slate-850 space-y-4">
                        <div class="border border-dashed border-slate-800 rounded-xl p-8 bg-slate-900/20 text-center space-y-3">
                            <div class="mx-auto w-12 h-12 rounded-full bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                                </svg>
                            </div>
                            <div class="text-xs font-semibold text-indigo-400">Pilih dokumen pendukung untuk diunggah</div>
                            <div class="text-[10px] text-slate-500">PDF, Word, Excel, PPTX, JPG, PNG hingga 10MB</div>
                        </div>
                        <div class="space-y-1">
                            <div class="text-[11px] font-bold text-slate-400">Teks Hasil Ekstraksi</div>
                            <div class="bg-slate-900/80 border border-slate-850 p-3 rounded-lg text-[10px] font-mono text-emerald-400 leading-relaxed max-h-24 overflow-hidden">
                                [OCR SUCCESS] HALO ANTIGRAVITY OCR TEST...<br/>
                                [DOCX SUCCESS] Halo Antigravity! Ini adalah teks dokumen Word...<br/>
                                [PDF SUCCESS] Menampilkan isi dokumen teks dari berkas PDF...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Feature Matrix Block -->
        <section id="fitur" class="relative z-10 max-w-7xl mx-auto w-full px-6 py-16 border-t border-slate-900/30">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="p-6 bg-slate-900/30 border border-slate-900 rounded-2xl space-y-4 hover:border-slate-800 transition">
                    <div class="p-2.5 bg-indigo-500/10 text-indigo-400 w-fit rounded-xl">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">Raycorp OCR (n8n Webhook)</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Mengintegrasikan n8n webhook untuk melakukan OCR (Optical Character Recognition) pada format gambar JPG, PNG, dan WebP secara akurat.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="p-6 bg-slate-900/30 border border-slate-900 rounded-2xl space-y-4 hover:border-slate-800 transition">
                    <div class="p-2.5 bg-cyan-500/10 text-cyan-400 w-fit rounded-xl">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">Ekstraksi Dokumen Office & PDF</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Mengekstrak teks langsung dari file PDF menggunakan parser handal, serta file Word (DOCX), Excel (XLSX), dan PowerPoint (PPTX) secara native.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="p-6 bg-slate-900/30 border border-slate-900 rounded-2xl space-y-4 hover:border-slate-800 transition">
                    <div class="p-2.5 bg-emerald-500/10 text-emerald-400 w-fit rounded-xl">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">Manajemen Kategori Terstruktur</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Mengelompokkan data knowledge dalam kategori domain yang teratur dan rapi demi kemudahan pencarian informasi dalam portal.
                    </p>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="relative z-10 max-w-7xl mx-auto w-full px-6 py-6 text-center text-xs text-slate-500 border-t border-slate-900/50">
            © 2026 Raycorp Group. All rights reserved. Powered by Laravel v{{ Illuminate\Foundation\Application::VERSION }}
        </footer>
    </body>
</html>
