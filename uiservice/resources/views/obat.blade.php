@extends('layouts.app')

@section('content')
    <div class="p-6 md:p-8 h-full overflow-y-auto custom-scrollbar relative bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto">

            {{-- HEADER AREA --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-5">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Katalog Obat</h1>
                    <p class="text-slate-500 mt-1.5 text-sm">Kelola inventaris dan temukan produk kesehatan dengan cepat.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    @if(session('user_role') == 'admin')
                        <button onclick="document.getElementById('modalTambahObat').classList.remove('hidden')"
                            class="flex-1 md:flex-none px-5 py-2.5 bg-emerald-600 text-white rounded-xl font-semibold text-sm hover:bg-emerald-700 transition-all shadow-sm shadow-emerald-600/20 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Tambah Obat
                        </button>
                    @endif

                    <a href="/transaksi"
                        class="flex-1 md:flex-none px-5 py-2.5 bg-white border border-emerald-500 text-emerald-600 rounded-xl font-semibold text-sm hover:bg-emerald-50 transition-all shadow-sm flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01m-.01 4h.01">
                            </path>
                        </svg>
                        Riwayat Transaksi
                    </a>
                </div>
            </div>

            {{-- NOTIFIKASI --}}
            @if(session('warning'))
                <div class="mb-6 p-4 rounded-xl bg-orange-50 text-orange-700 text-sm border border-orange-200 flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    {{ session('warning') }}
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 text-emerald-700 text-sm border border-emerald-200 flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- FORM PENCARIAN (CLIENT-SIDE REALTIME) --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-2 mb-8 flex gap-2 items-center focus-within:border-emerald-500 transition-all">
                <div class="flex-1 relative flex items-center">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" id="realtimeSearch" onkeyup="filterObat()" placeholder="Cari nama atau kategori obat..."
                        class="w-full pl-10 pr-4 py-2 bg-transparent text-sm text-slate-800 placeholder-slate-400 outline-none">
                </div>
                <div class="px-4 py-2 text-slate-400 font-medium text-xs border-l border-slate-100 hidden md:block">
                    Real-time Search
                </div>
            </div>

            <script>
                function filterObat() {
                    const input = document.getElementById('realtimeSearch');
                    const filter = input.value.toLowerCase();
                    const cards = document.getElementsByClassName('obat-card');
                    let found = 0;

                    for (let i = 0; i < cards.length; i++) {
                        const name = cards[i].getAttribute('data-name').toLowerCase();
                        const category = cards[i].getAttribute('data-category').toLowerCase();
                        
                        if (name.includes(filter) || category.includes(filter)) {
                            cards[i].style.display = "";
                            found++;
                        } else {
                            cards[i].style.display = "none";
                        }
                    }

                    const emptyState = document.getElementById('emptySearchState');
                    if (found === 0) {
                        emptyState.classList.remove('hidden');
                    } else {
                        emptyState.classList.add('hidden');
                    }
                }
            </script>

            {{-- GRID CARD COMPACT (LOGIC PENUH) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5" id="obatGrid">
                @forelse($obatList as $obat)
                    @php
                        $stock = $obat['stock'] ?? 0;
                        $isLowStock = $stock > 0 && $stock <= 5;
                        $isOutOfStock = $stock <= 0;
                    @endphp

                    <div class="obat-card rounded-2xl p-5 flex flex-col relative transition-all duration-200
                        {{ $isLowStock ? 'bg-orange-50/30 border border-orange-200 hover:shadow-lg hover:shadow-orange-100/50' : 'bg-white border border-slate-200 hover:border-emerald-300 hover:shadow-lg hover:shadow-slate-200/50' }}
                        {{ $isOutOfStock ? 'opacity-60 grayscale-[30%]' : '' }}"
                        data-name="{{ $obat['name'] ?? '' }}"
                        data-category="{{ $obat['category'] ?? 'Umum' }}">

                        <div class="flex justify-between items-center mb-3">
                            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider {{ $isLowStock || $isOutOfStock ? 'bg-white border border-slate-200' : 'bg-slate-100' }} px-2.5 py-1 rounded-lg">
                                {{ $obat['category'] ?? 'Umum' }}
                            </span>

                            <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg flex items-center gap-1.5
                                {{ $isOutOfStock ? 'bg-rose-100 text-rose-600' : ($isLowStock ? 'bg-orange-100 text-orange-600' : 'bg-emerald-50 text-emerald-600') }}">
                                @if($isLowStock && !$isOutOfStock)
                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                                @endif
                                Stok: {{ $stock }}
                            </span>
                        </div>

                        <h3 class="text-lg font-bold text-slate-900 mb-1 leading-tight">
                            {{ $obat['name'] ?? 'Produk Tanpa Nama' }}
                        </h3>
                        <p class="text-sm text-slate-500 line-clamp-2 mb-4">
                            {{ $obat['description'] ?? 'Belum ada deskripsi.' }}
                        </p>

                        <div class="mt-auto pt-4 border-t {{ $isLowStock ? 'border-orange-100/50' : 'border-slate-100' }} flex items-end justify-between">
                            <div>
                                <p class="text-[10px] font-semibold uppercase tracking-wider mb-0.5 {{ $isLowStock ? 'text-orange-400' : 'text-slate-400' }}">Harga</p>
                                <p class="text-lg font-black {{ $isOutOfStock ? 'text-slate-500' : 'text-emerald-600' }}">
                                    Rp {{ number_format($obat['price'] ?? 0, 0, ',', '.') }}
                                </p>
                            </div>

                            <div class="flex gap-1.5">

                                @if(session('user_role') != 'admin')
                                    <button type="button" data-obat="{{ json_encode($obat) }}" onclick="openBeliModal(this)"
                                        class="p-2.5 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-colors {{ $isOutOfStock ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        title="Beli Sekarang" {{ $isOutOfStock ? 'disabled' : '' }}>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </button>
                                @endif

                                @if(session('user_role') == 'admin')
                                    <button type="button" data-obat="{{ json_encode($obat) }}" onclick="openEditModal(this)" title="Edit"
                                        class="p-2.5 rounded-xl transition-colors {{ $isLowStock ? 'bg-white border border-slate-200 text-blue-600 hover:bg-blue-600 hover:text-white hover:border-transparent' : 'bg-slate-50 text-blue-600 hover:bg-blue-600 hover:text-white' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>

                                    <form action="/obat/{{ $obat['id'] ?? '' }}/destroy" method="POST" class="m-0" onsubmit="return confirm('Hapus {{ $obat['name'] }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus"
                                            class="p-2.5 rounded-xl transition-colors {{ $isLowStock ? 'bg-white border border-slate-200 text-rose-500 hover:bg-rose-500 hover:text-white hover:border-transparent' : 'bg-slate-50 text-rose-500 hover:bg-rose-500 hover:text-white' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                @empty
                    <div class="col-span-1 sm:col-span-2 lg:col-span-3 xl:col-span-4 flex flex-col items-center justify-center py-20 bg-white rounded-2xl border border-slate-200 border-dashed">
                        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">Tidak Ditemukan</h3>
                        <p class="text-slate-500 text-sm mt-1 text-center">Pastikan ID obat benar atau tambahkan data baru.</p>
                    </div>
                @endforelse

                {{-- Empty State for Real-time Search --}}
                <div id="emptySearchState" class="hidden col-span-1 sm:col-span-2 lg:col-span-3 xl:col-span-4 flex flex-col items-center justify-center py-20 bg-white rounded-2xl border border-slate-200 border-dashed">
                    <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Hasil tidak ditemukan</h3>
                    <p class="text-slate-500 text-sm mt-1 text-center">Coba cari dengan kata kunci nama atau kategori lain.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL AREA --}}
    @if(session('user_role') != 'admin')
        {{-- MODAL: BELI OBAT --}}
        <div id="modalBeliObat" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm transition-opacity p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all border border-slate-100">
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-emerald-50/50">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Pesan Obat</h3>
                        <p id="beli_nama_obat" class="text-xs text-emerald-600 font-semibold"></p>
                    </div>
                    <button onclick="document.getElementById('modalBeliObat').classList.add('hidden')" class="p-2 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form action="/transaksi/tambah" method="POST" class="p-6">
                    @csrf
                    <input type="hidden" id="beli_id_obat" name="obat_id">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jumlah Pesanan</label>
                            <div class="flex items-center gap-3">
                                <button type="button" onclick="adjustQty(-1)" class="w-10 h-10 rounded-lg border border-slate-200 flex items-center justify-center hover:bg-slate-50 font-bold">-</button>
                                <input type="number" id="beli_qty" name="quantity" value="1" min="1" required
                                    class="flex-1 text-center px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-emerald-500 text-lg font-bold outline-none transition-all">
                                <button type="button" onclick="adjustQty(1)" class="w-10 h-10 rounded-lg border border-slate-200 flex items-center justify-center hover:bg-slate-50 font-bold">+</button>
                            </div>
                            <p id="beli_info_stok" class="text-[10px] text-slate-400 mt-2 text-center uppercase tracking-wider font-bold"></p>
                        </div>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100 flex gap-3">
                        <button type="button" onclick="document.getElementById('modalBeliObat').classList.add('hidden')" class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition">Batal</button>
                        <button type="submit" class="flex-1 py-2.5 text-sm font-semibold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 transition shadow-lg shadow-emerald-200/50 flex items-center justify-center gap-2">
                            Konfirmasi Pesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function openBeliModal(button) {
                const obat = JSON.parse(button.getAttribute('data-obat'));
                document.getElementById('beli_id_obat').value = obat.id;
                document.getElementById('beli_nama_obat').innerText = obat.name;
                document.getElementById('beli_info_stok').innerText = 'Stok Tersedia: ' + obat.stock;
                document.getElementById('beli_qty').value = 1;
                document.getElementById('beli_qty').max = obat.stock;
                document.getElementById('modalBeliObat').classList.remove('hidden');
            }

            function adjustQty(amount) {
                const input = document.getElementById('beli_qty');
                let val = parseInt(input.value) + amount;
                if (val < 1) val = 1;
                if (val > parseInt(input.max)) val = input.max;
                input.value = val;
            }
        </script>
    @endif

    {{-- MODAL AREA ADMIN --}}
    @if(session('user_role') == 'admin')
        {{-- MODAL 1: TAMBAH OBAT --}}
        <div id="modalTambahObat" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm transition-opacity p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden transform transition-all border border-slate-100">
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-900">Tambah Obat Baru</h3>
                    <button onclick="document.getElementById('modalTambahObat').classList.add('hidden')" class="p-2 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form action="/obat/store" method="POST" class="p-6">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Obat</label>
                            <input type="text" name="name" required placeholder="Contoh: Paracetamol 500mg" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-emerald-500 text-sm outline-none transition-all">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kategori</label>
                                <input type="text" name="category" required placeholder="Tablet, Sirup..." class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-emerald-500 text-sm outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Stok Awal</label>
                                <input type="number" name="stock" required min="0" placeholder="0" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-emerald-500 text-sm outline-none transition-all">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Harga Jual (Rp)</label>
                            <input type="number" name="price" required min="0" placeholder="Contoh: 15000" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-emerald-500 text-sm outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Deskripsi Singkat</label>
                            <textarea name="description" rows="3" required placeholder="Jelaskan kegunaan obat..." class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-emerald-500 text-sm outline-none transition-all resize-none"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100 flex gap-3">
                        <button type="button" onclick="document.getElementById('modalTambahObat').classList.add('hidden')" class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition">Batal</button>
                        <button type="submit" class="flex-1 py-2.5 text-sm font-semibold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 transition">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL 2: EDIT OBAT --}}
        <div id="modalEditObat" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm transition-opacity p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all border border-slate-100">
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-900">Edit Data Obat</h3>
                    <button onclick="document.getElementById('modalEditObat').classList.add('hidden')" class="p-2 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form id="formEditObat" method="POST" class="p-6">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Obat</label>
                            <input type="text" id="edit_name" name="name" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 text-sm outline-none transition-all">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kategori</label>
                                <input type="text" id="edit_category" name="category" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 text-sm outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Sisa Stok</label>
                                <input type="number" id="edit_stock" name="stock" required min="0" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 text-sm outline-none transition-all">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Harga (Rp)</label>
                            <input type="number" id="edit_price" name="price" required min="0" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 text-sm outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Deskripsi Singkat</label>
                            <textarea id="edit_description" name="description" rows="3" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 text-sm outline-none transition-all resize-none"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100 flex gap-3">
                        <button type="button" onclick="document.getElementById('modalEditObat').classList.add('hidden')" class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition">Batal</button>
                        <button type="submit" class="flex-1 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition">Perbarui Data</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function openEditModal(button) {
                const obat = JSON.parse(button.getAttribute('data-obat'));
                document.getElementById('formEditObat').action = '/obat/' + obat.id + '/update';
                document.getElementById('edit_name').value = obat.name || '';
                document.getElementById('edit_category').value = obat.category || '';
                document.getElementById('edit_price').value = obat.price || 0;
                document.getElementById('edit_stock').value = obat.stock || 0;
                document.getElementById('edit_description').value = obat.description || '';
                document.getElementById('modalEditObat').classList.remove('hidden');
            }
        </script>
    @endif
@endsection
