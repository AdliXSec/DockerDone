@extends('layouts.app')

@section('content')
    <div class="p-8 h-full overflow-y-auto custom-scrollbar bg-slate-50/50">
        <div class="max-w-7xl mx-auto">

            <div
                class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-3xl p-8 sm:p-10 text-white shadow-lg shadow-emerald-200/50 mb-8 relative overflow-hidden">
                <div class="absolute -right-10 -top-10 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute right-20 -bottom-20 w-40 h-40 bg-teal-400/20 rounded-full blur-2xl"></div>

                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <p class="text-emerald-100 font-medium mb-1">
                            {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                        </p>
                        <h1 class="text-3xl font-bold mb-2">Selamat Bertugas, {{ session('user_name') ?? 'Admin' }}!
                            👋</h1>
                        <p class="text-emerald-50 opacity-90">Sistem apotek siap digunakan. Kelola data obat dan transaksi
                            hari ini.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div
                    class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center gap-5 hover:-translate-y-1 transition-transform duration-300">
                    <div
                        class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 font-medium">Total Jenis Obat</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-0.5">{{ $totalObat }}</h3>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center gap-5 hover:-translate-y-1 transition-transform duration-300">
                    <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 font-medium">
                            {{ session('user_role') == 'admin' ? 'Total Transaksi' : 'Transaksi Saya' }}
                        </p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-0.5">{{ $totalPesanan }}</h3>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center gap-5 hover:-translate-y-1 transition-transform duration-300">
                    <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 font-medium">Total Akun Pelanggan</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-0.5">{{ $totalUser }}</h3>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                    <h3 class="font-bold text-slate-800 mb-6 text-lg">Panduan Penggunaan Sistem</h3>

                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div
                                class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 mt-0.5">
                                <span class="font-bold">1</span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-slate-700">Menu Transaksi (Point of Sales)</h4>
                                <p class="text-sm text-slate-500 mt-1 leading-relaxed">Gunakan menu ini untuk memproses
                                    pembayaran kasir pelanggan secara langsung. Sistem akan otomatis memotong stok obat.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div
                                class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                                <span class="font-bold">2</span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-slate-700">Manajemen Katalog Obat</h4>
                                <p class="text-sm text-slate-500 mt-1 leading-relaxed">Kelola semua inventaris apotek di
                                    sini. Anda dapat menambah obat baru, memperbarui harga, dan mengecek tanggal
                                    kedaluwarsa.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 shadow-sm relative overflow-hidden text-white flex flex-col justify-between">
                    <div class="absolute -right-8 -top-8 w-32 h-32 bg-emerald-500/20 rounded-full blur-2xl"></div>

                    <div
                        class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 shadow-sm relative overflow-hidden text-white flex flex-col justify-between">
                        <div class="absolute -right-8 -top-8 w-32 h-32 bg-emerald-500/20 rounded-full blur-2xl"></div>

                        <div class="relative z-10">
                            <h3 class="font-bold text-lg mb-2 flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>
                                Pusat Bantuan Cepat
                            </h3>
                            <p class="text-slate-400 text-sm mb-6 leading-relaxed">Mengalami kendala teknis pada aplikasi?
                                Segera hubungi tim pengembang kami</p>

                            <div class="space-y-3">

                                <a href="https://wa.me/6285210205269?text=Halo%20IT%20Support,%20saya%20membutuhkan%20bantuan%20terkait%20sistem%20Apotek."
                                    target="_blank"
                                    class="flex items-center gap-4 bg-white/5 border border-white/10 p-3.5 rounded-xl hover:bg-white/10 transition-colors cursor-pointer group block">
                                    <div
                                        class="w-10 h-10 bg-emerald-500/20 text-emerald-400 rounded-lg flex items-center justify-center group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm text-slate-200">WhatsApp IT Support</p>
                                        <p class="text-xs text-slate-400 mt-0.5">Respon cepat (08:00 - 17:00)</p>
                                    </div>
                                </a>

                                <a href="mailto:dwikyr1221@gmail.com?subject=Bantuan%20Teknis%20Sistem%20Apotek"
                                    class=".flex items-center gap-4 bg-white/5 border border-white/10 p-3.5 rounded-xl hover:bg-white/10 transition-colors cursor-pointer group block">
                                    <div
                                        class="w-10 h-10 bg-blue-500/20 text-blue-400 rounded-lg flex items-center justify-center group-hover:bg-blue-500 group-hover:text-white transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm text-slate-200">Email Bantuan Teknis</p>
                                        <p class="text-xs text-slate-400 mt-0.5">naufalfataaa@gmail.com</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection