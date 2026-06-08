@extends('layouts.app')

@section('content')
    <div class="p-8 h-full overflow-y-auto custom-scrollbar bg-slate-50/50">
        <div class="max-w-4xl mx-auto">

            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Pengaturan Profil</h1>
                <p class="text-slate-500 mt-1.5 text-sm">Kelola informasi akun dan keamanan kata sandi Anda.</p>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 text-emerald-700 text-sm border border-emerald-200 flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 text-rose-700 text-sm border border-rose-200">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                {{-- SIDEBAR PROFIL --}}
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 text-center">
                        <div class="w-24 h-24 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold border-4 border-white shadow-sm">
                            {{ strtoupper(substr($userData['name'] ?? 'U', 0, 1)) }}
                        </div>
                        <h2 class="text-xl font-bold text-slate-800">{{ $userData['name'] ?? 'User' }}</h2>
                        <p class="text-sm text-slate-500 mb-4">{{ $userData['email'] ?? '-' }}</p>
                        <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-[10px] font-bold uppercase tracking-wider">
                            Role: {{ session('user_role') ?? 'User' }}
                        </span>
                    </div>

                    <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 text-white shadow-sm relative overflow-hidden">
                        <div class="absolute -right-6 -top-6 w-20 h-20 bg-white/5 rounded-full blur-xl"></div>
                        <h3 class="font-bold text-sm mb-2 relative z-10">Keamanan Akun</h3>
                        <p class="text-xs text-slate-400 leading-relaxed relative z-10">Pastikan Anda menggunakan kata sandi yang kuat dan unik untuk menjaga keamanan data apotek.</p>
                    </div>
                </div>

                {{-- FORM EDIT --}}
                <div class="md:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-100 bg-white">
                            <h3 class="font-bold text-slate-800">Informasi Pribadi</h3>
                        </div>

                        <form action="/profile/update" method="POST" class="p-6 space-y-5">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                                    <input type="text" name="name" value="{{ old('name', $userData['name'] ?? '') }}" required
                                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-emerald-500 text-sm outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Email</label>
                                    <input type="email" name="email" value="{{ old('email', $userData['email'] ?? '') }}" required
                                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-emerald-500 text-sm outline-none transition-all">
                                </div>
                            </div>

                            <hr class="border-slate-100 my-6">

                            <div>
                                <h3 class="font-bold text-slate-800 mb-4">Ganti Kata Sandi</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kata Sandi Baru</label>
                                        <input type="password" name="password" placeholder="••••••••"
                                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-emerald-500 text-sm outline-none transition-all">
                                        <p class="text-[10px] text-slate-400 mt-1.5">Kosongkan jika tidak ingin mengubah</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Konfirmasi Kata Sandi</label>
                                        <input type="password" name="password_confirmation" placeholder="••••••••"
                                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-emerald-500 text-sm outline-none transition-all">
                                    </div>
                                </div>
                            </div>

                            <div class="pt-6 border-t border-slate-100 flex justify-end">
                                <button type="submit" class="px-8 py-3 bg-emerald-600 text-white rounded-xl font-bold text-sm hover:bg-emerald-700 transition shadow-lg shadow-emerald-200/50 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection
