@extends('layouts.app')

@section('content')
    <div class="p-8 h-full overflow-y-auto custom-scrollbar bg-slate-50/50">
        <div class="max-w-7xl mx-auto">

            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Daftar Transaksi</h1>
                    <p class="text-slate-500 mt-1">Pantau semua pesanan dan status transaksi Anda</p>
                </div>
                <a href="/obat" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl font-semibold text-sm hover:bg-slate-50 transition-all shadow-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Katalog
                </a>
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
                    <input type="text" id="realtimeSearchTransaksi" onkeyup="filterTransaksi()" placeholder="Cari kode pesanan (Order Code)..."
                        class="w-full pl-10 pr-4 py-2 bg-transparent text-sm text-slate-800 placeholder-slate-400 outline-none">
                </div>
                <div class="px-4 py-2 text-slate-400 font-medium text-xs border-l border-slate-100 hidden md:block">
                    Real-time Search
                </div>
            </div>

            <script>
                function filterTransaksi() {
                    const input = document.getElementById('realtimeSearchTransaksi');
                    const filter = input.value.toLowerCase();
                    const cards = document.getElementsByClassName('transaction-card');
                    let found = 0;

                    for (let i = 0; i < cards.length; i++) {
                        const code = cards[i].getAttribute('data-order-code').toLowerCase();
                        
                        if (code.includes(filter)) {
                            cards[i].style.display = "";
                            found++;
                        } else {
                            cards[i].style.display = "none";
                        }
                    }

                    const emptyState = document.getElementById('emptySearchStateTransaksi');
                    if (found === 0) {
                        emptyState.classList.remove('hidden');
                    } else {
                        emptyState.classList.add('hidden');
                    }
                }
            </script>

            <div class="flex flex-col lg:flex-row gap-6 items-start">

                <div class="flex-1 w-full bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-white">
                        <h2 class="font-bold text-slate-800">Riwayat Pesanan ({{ count($orders) }})</h2>
                    </div>

                    <div class="p-6 space-y-4">

                        @forelse($orders as $order)
                            <div class="transaction-card flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 border border-slate-100 rounded-2xl hover:border-emerald-200 hover:bg-emerald-50/30 transition-all gap-4 group"
                                data-order-code="{{ $order['order_code'] }}">
                                <div class="flex items-center gap-4 flex-1">
                                    <div class="w-14 h-14 bg-slate-50 rounded-xl flex items-center justify-center text-slate-300 shrink-0 group-hover:bg-white group-hover:text-emerald-500 transition-colors border border-slate-100">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-semibold text-slate-800">{{ $order['order_code'] }}</h4>
                                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase
                                                {{ $order['status'] == 'completed' ? 'bg-emerald-100 text-emerald-600' :
                                                   ($order['status'] == 'pending' ? 'bg-amber-100 text-amber-600' : 'bg-slate-100 text-slate-600') }}">
                                                {{ $order['status'] }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-1">Pembeli: {{ $order['customer_name'] }}</p>
                                        <p class="text-[11px] text-slate-400">{{ \Carbon\Carbon::parse($order['created_at'])->format('d M Y, H:i') }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-6 w-full sm:w-auto justify-between sm:justify-end">
                                    <div class="text-right">
                                        <p class="text-[10px] text-slate-400 uppercase font-bold">Qty: {{ $order['quantity'] }}</p>
                                        <div class="font-bold text-slate-800 text-lg">Rp {{ number_format($order['total_price'], 0, ',', '.') }}</div>
                                    </div>

                                    <div class="flex gap-2">
                                        {{-- ADMIN ONLY: EDIT STATUS & HAPUS --}}
                                        @if(session('user_role') == 'admin')
                                            <button type="button" data-order="{{ json_encode($order) }}" onclick="openEditOrderModal(this)"
                                                class="p-2.5 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors" title="Edit Transaksi">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </button>

                                            <form action="/transaksi/{{ $order['id'] }}/destroy" method="POST" onsubmit="return confirm('Hapus permanen transaksi ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2.5 rounded-xl bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition-colors" title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-12 text-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                </div>
                                <h3 class="font-bold text-slate-700">Belum Ada Transaksi</h3>
                                <p class="text-slate-500 text-sm mt-1">Silakan pilih obat dari katalog untuk memulai pesanan.</p>
                                <a href="/obat" class="mt-4 px-6 py-2 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-700 transition shadow-lg shadow-emerald-200/50">Ke Katalog</a>
                            </div>
                        @endforelse

                        {{-- Empty State for Real-time Search --}}
                        <div id="emptySearchStateTransaksi" class="hidden flex flex-col items-center justify-center py-12 text-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <h3 class="font-bold text-slate-700">Kode Pesanan Tidak Ditemukan</h3>
                            <p class="text-slate-500 text-sm mt-1">Coba gunakan kata kunci kode pesanan yang lain.</p>
                        </div>

                    </div>
                </div>

                <div class="w-full lg:w-[400px] shrink-0">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden sticky top-8">

                        <div class="p-6 bg-emerald-600 text-white flex justify-between items-center relative overflow-hidden">
                            <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                            <div class="relative z-10">
                                <h2 class="text-xl font-bold leading-none mb-1.5">Ringkasan Sistem</h2>
                                <p class="text-emerald-100/90 text-sm font-medium mt-1">
                                    {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}
                                </p>
                            </div>
                        </div>

                        <div class="p-6 bg-white space-y-4">
                            <div class="flex justify-between text-sm text-slate-500">
                                <span>Total Transaksi</span>
                                <span class="font-medium text-slate-700">{{ count($orders) }} Pesanan</span>
                            </div>
                            <div class="flex justify-between text-sm text-slate-500">
                                <span>Total Omzet</span>
                                <span class="font-medium text-emerald-600">Rp {{ number_format(collect($orders)->sum('total_price'), 0, ',', '.') }}</span>
                            </div>
                            <div class="pt-4 border-t border-slate-100 flex justify-between items-end">
                                <span class="text-sm font-medium text-slate-500">Status Layanan</span>
                                <span class="text-lg font-black text-emerald-600">AKTIF</span>
                            </div>
                        </div>

                        <div class="p-6 bg-slate-50 border-t border-slate-100 text-center">
                            <p class="text-xs text-slate-400">
                                Data disinkronkan langsung dari Order Service Microservice.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL EDIT ORDER (ADMIN ONLY) --}}
    @if(session('user_role') == 'admin')
        <div id="modalEditOrder" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm transition-opacity p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all border border-slate-100">
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-blue-50/50">
                    <h3 class="text-lg font-bold text-slate-900">Update Status Pesanan</h3>
                    <button onclick="document.getElementById('modalEditOrder').classList.add('hidden')" class="p-2 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form id="formEditOrder" method="POST" class="p-6">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Update Status Transaksi</label>
                            <select name="status" id="edit_order_status" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 text-sm font-semibold outline-none transition-all">
                                <option value="pending" class="text-amber-600">⏳ Menunggu (Pending)</option>
                                <option value="processing" class="text-blue-600">🔄 Diproses (Processing)</option>
                                <option value="completed" class="text-emerald-600">✅ Selesai (Completed)</option>
                                <option value="cancelled" class="text-rose-600">❌ Dibatalkan (Cancelled)</option>
                            </select>
                        </div>
                        <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                            <h4 class="text-[11px] font-bold text-blue-700 uppercase tracking-wider mb-1">Panduan Admin:</h4>
                            <ul class="text-[10px] text-blue-600 space-y-1 list-disc pl-3">
                                <li>Pilih <b>Processing</b> jika obat sedang disiapkan.</li>
                                <li>Pilih <b>Completed</b> jika transaksi sudah selesai/dibayar.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100 flex gap-3">
                        <button type="button" onclick="document.getElementById('modalEditOrder').classList.add('hidden')" class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition">Batal</button>
                        <button type="submit" class="flex-1 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function openEditOrderModal(button) {
                const order = JSON.parse(button.getAttribute('data-order'));
                document.getElementById('formEditOrder').action = '/transaksi/' + order.id + '/update';
                document.getElementById('edit_order_status').value = order.status;
                document.getElementById('modalEditOrder').classList.remove('hidden');
            }
        </script>
    @endif
@endsection
