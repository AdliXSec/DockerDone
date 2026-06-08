<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - MedTech</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 to-emerald-50 antialiased selection:bg-emerald-500 selection:text-white overflow-x-hidden">

    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-12 relative">

        <a href="/"
            class="absolute top-8 left-8 flex items-center gap-2 text-slate-500 hover:text-emerald-600 transition font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Kembali ke Beranda
        </a>

        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 border border-slate-100 z-10">
            <div class="text-center mb-8">
                <div
                    class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                        </path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-slate-800">Buat Akun Baru</h2>
                <p class="text-slate-500 mt-2 text-sm">Daftarkan diri Anda ke sistem MedTech</p>
            </div>

            @if($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-rose-50 text-rose-600 text-sm border border-rose-200">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all outline-none"
                        placeholder="Masukkan nama Anda">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all outline-none"
                        placeholder="contoh@apotek.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all outline-none"
                        placeholder="••••••••">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Pilih Role Akses</label>
                    <div class="relative">
                        <select name="role" required
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all outline-none appearance-none text-slate-700">
                            <option value="" disabled selected>Pilih peran Anda...</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="flex items-start gap-2 pt-2">
                    <input type="checkbox" required
                        class="w-4 h-4 mt-1 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                    <span class="text-sm text-slate-600 leading-relaxed">
                        Saya menyetujui <a href="#" class="text-emerald-600 font-medium hover:text-emerald-700">Syarat &
                            Ketentuan</a> serta <a href="#"
                            class="text-emerald-600 font-medium hover:text-emerald-700">Kebijakan Privasi</a>.
                    </span>
                </div>

                <button type="submit"
                    class="block text-center w-full py-3.5 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-700 transition shadow-lg shadow-emerald-200 hover:-translate-y-0.5 mt-2">
                    Daftar Sekarang
                </button>
            </form>

            <div class="mt-8 text-center border-t border-slate-100 pt-6">
                <p class="text-slate-500 text-sm">
                    Sudah memiliki akun?
                    <a href="/login" class="text-emerald-600 font-bold hover:text-emerald-700 transition ml-1">Masuk di
                        sini</a>
                </p>
            </div>
        </div>

        <div
            class="absolute bottom-0 right-0 w-96 h-96 bg-emerald-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 -z-10 translate-x-1/3 translate-y-1/3">
        </div>
        <div
            class="absolute top-20 left-10 w-72 h-72 bg-teal-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 -z-10 -translate-x-1/3">
        </div>
    </div>

</body>

</html>