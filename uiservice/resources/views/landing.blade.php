<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedTech - Solusi Apotek Modern</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; scroll-behavior: smooth; } </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-emerald-500 selection:text-white">

    <nav class="container mx-auto px-6 py-4 flex justify-between items-center relative z-10">
        <div class="text-2xl font-bold text-emerald-600 flex items-center gap-2">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
            MedTech
        </div>
        <div class="hidden md:flex gap-8 text-slate-600 font-medium">
            <a href="/" class="text-emerald-600 transition">Home</a>
            <a href="#fitur" class="hover:text-emerald-600 transition">Fitur</a>
            <a href="#tentang" class="hover:text-emerald-600 transition">Tentang</a>
        </div>
        <div class="flex items-center gap-3">
            <a href="/login" class="px-6 py-2.5 text-slate-600 font-medium hover:text-emerald-600 transition">Login</a>
            <a href="/register" class="px-6 py-2.5 bg-emerald-600 text-white rounded-xl font-medium hover:bg-emerald-700 transition shadow-lg shadow-emerald-200">Register</a>
        </div>
    </nav>

    <section class="container mx-auto px-6 py-20 flex flex-col md:flex-row items-center gap-12 relative">
        <div class="absolute top-0 left-0 w-72 h-72 bg-emerald-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 -z-10"></div>
        <div class="absolute top-0 right-20 w-72 h-72 bg-teal-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 -z-10"></div>

        <div class="flex-1 space-y-8 z-10">
            <div class="inline-block px-4 py-2 bg-emerald-100/50 text-emerald-700 rounded-full text-sm font-semibold mb-4 border border-emerald-200 backdrop-blur-sm">
                ✨ Platform Manajemen Apotek #1 di Indonesia
            </div>
            <h1 class="text-5xl lg:text-6xl font-bold leading-tight text-slate-800 tracking-tight">
                Solusi Digital untuk <br> <span class="text-transparent bg-clip-text .bg-gradient-to-r from-emerald-500 to-teal-400">Manajemen Apotek Modern</span>
            </h1>
            <p class="text-lg text-slate-500 leading-relaxed max-w-lg">
                Pantau stok obat, kelola transaksi kasir, dan pantau performa apotek Anda dalam satu dashboard intuitif bergaya SaaS premium.
            </p>
            <div class="flex gap-4">
                <a href="/login" class="px-8 py-4 bg-emerald-600 text-white rounded-xl font-semibold hover:bg-emerald-700 transition shadow-xl shadow-emerald-200 flex items-center gap-2 hover:-translate-y-1">
                    Mulai Sekarang <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        </div>
        
        <div class="flex-1 z-10 w-full">
            <div class="w-full h-[400px] .bg-gradient-to-tr from-slate-800 to-slate-700 rounded-2xl .border-[8px] border-white shadow-2xl flex flex-col relative overflow-hidden transform rotate-2 hover:rotate-0 transition-transform duration-500">
                <div class="h-8 bg-slate-900 flex items-center px-4 gap-2">
                    <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                    <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                    <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                </div>
                <div class="flex-1 p-6 relative flex items-center justify-center">
                    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 20px 20px;"></div>
                    <div class="text-center">
                        <svg class="w-16 h-16 text-emerald-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        <span class="text-emerald-100 font-medium text-lg z-10">[ Ilustrasi Dashboard 3D ]</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

</body>
</html>