<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rekap Pengelolaan Sampah - {{ $periode }}</title>
  <link rel="icon" type="image/png" href="/favicon.png">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @media print {
      .no-print { display: none !important; }
      body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
      @page { margin: 1.5cm; }
      table { page-break-inside: avoid; }
      tr { page-break-inside: avoid; }
    }
    @media screen {
      body { background: #f1f5f9; }
    }
  </style>
</head>
<body class="bg-slate-100 text-slate-800 font-sans p-4 md:p-8 min-h-screen">
  <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-md border border-slate-200 print:shadow-none print:border-none print:p-0">

    <!-- Action Bar (Hidden when printing) -->
    <div class="no-print mb-6 flex items-center justify-between bg-slate-50 p-4 rounded-xl border border-slate-200">
      <div class="flex items-center gap-2">
        <a href="/" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded-lg text-xs transition-all flex items-center gap-1.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          Kembali ke Aplikasi
        </a>
      </div>
      <div class="flex items-center gap-3">
        <button onclick="window.print()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-xs shadow-sm transition-all flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
          Cetak / Simpan PDF
        </button>
      </div>
    </div>

    <!-- Kop Laporan -->
    <div class="border-b-2 border-slate-800 pb-4 mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-black text-emerald-700 tracking-tight">PLUNG! - SISTEM PEMILAHAN SAMPAH MANDIRI</h1>
          <p class="text-sm font-bold text-slate-700">RUKUN TETANGGA 36 / RUKUN WARGA 09</p>
          <p class="text-xs text-slate-500">Kelurahan Damai Sejahtera, Kecamatan Lestari Bersih</p>
        </div>
        <div class="text-right">
          <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 font-bold text-xs rounded-full uppercase">Laporan Resmi</span>
          <p class="text-xs text-slate-400 mt-1">Dicetak: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y, H:i') }} WIB</p>
        </div>
      </div>
    </div>

    <!-- Judul Laporan -->
    <div class="text-center my-6">
      <h2 class="text-lg font-black uppercase text-slate-900 tracking-wider">Rekapitulasi Pengelolaan Sampah Warga</h2>
      <p class="text-sm font-semibold text-emerald-700 mt-0.5">Periode: {{ $periode }}</p>
    </div>

    @if($setorans->isEmpty())
    <div class="text-center py-16">
      <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      </div>
      <h3 class="text-lg font-bold text-slate-700">Data Tidak Ditemukan</h3>
      <p class="text-sm text-slate-500 mt-1">Tidak ada data setoran pada periode {{ $periode }}.</p>
    </div>
    @else
    <!-- Ringkasan Kartu -->
    <div class="grid grid-cols-3 gap-4 mb-6">
      <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl text-center">
        <p class="text-xs text-slate-500 font-bold uppercase">Total Sampah Terkelola</p>
        <p class="text-xl font-black text-emerald-600 mt-1">{{ number_format($totalBerat, 2, ',', '.') }} <span class="text-xs font-semibold">Kg</span></p>
      </div>
      <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl text-center">
        <p class="text-xs text-slate-500 font-bold uppercase">Total Transaksi</p>
        <p class="text-xl font-black text-slate-800 mt-1">{{ $setorans->count() }} <span class="text-xs font-semibold">Setoran</span></p>
      </div>
      <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl text-center">
        <p class="text-xs text-slate-500 font-bold uppercase">Total Poin Diberikan</p>
        <p class="text-xl font-black text-amber-600 mt-1">{{ number_format($totalPoin, 0, ',', '.') }} <span class="text-xs font-semibold">Pts</span></p>
      </div>
    </div>

    <!-- Tabel 1: Ringkasan Per Kategori -->
    <div class="mb-6">
      <h3 class="text-xs font-black uppercase text-slate-700 mb-2 tracking-wider">1. Ringkasan Volume per Kategori Sampah</h3>
      <table class="w-full text-xs text-left border border-slate-200">
        <thead class="bg-slate-100 text-slate-700 font-bold">
          <tr>
            <th class="p-2 border-b border-r border-slate-200 w-10 text-center">No</th>
            <th class="p-2 border-b border-r border-slate-200">Kategori Sampah</th>
            <th class="p-2 border-b border-r border-slate-200 text-center">Frekuensi Transaksi</th>
            <th class="p-2 border-b border-r border-slate-200 text-right">Total Berat (Kg)</th>
            <th class="p-2 border-b border-slate-200 text-right">Total Poin</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
          @forelse($kategoriSummary as $idx => $kat)
          <tr>
            <td class="p-2 border-r border-slate-200 text-center">{{ $loop->iteration }}</td>
            <td class="p-2 border-r border-slate-200 font-bold text-slate-800">{{ $kat['nama'] }}</td>
            <td class="p-2 border-r border-slate-200 text-center">{{ $kat['jumlah_transaksi'] }}x</td>
            <td class="p-2 border-r border-slate-200 text-right font-semibold">{{ number_format($kat['total_berat_kg'], 2, ',', '.') }}</td>
            <td class="p-2 text-right font-bold text-emerald-700">{{ number_format($kat['total_poin'], 0, ',', '.') }} pts</td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="p-4 text-center text-slate-400 italic">Tidak ada data setoran pada periode ini.</td>
          </tr>
          @endforelse
        </tbody>
        @if($setorans->isNotEmpty())
        <tfoot class="bg-slate-50 font-bold border-t-2 border-slate-300">
          <tr>
            <td colspan="2" class="p-2 border-r border-slate-200 text-center uppercase">Total Keseluruhan</td>
            <td class="p-2 border-r border-slate-200 text-center">{{ $setorans->count() }}x</td>
            <td class="p-2 border-r border-slate-200 text-right">{{ number_format($totalBerat, 2, ',', '.') }} Kg</td>
            <td class="p-2 text-right text-emerald-700">{{ number_format($totalPoin, 0, ',', '.') }} pts</td>
          </tr>
        </tfoot>
        @endif
      </table>
    </div>

    <!-- Tabel 2: Rincian Transaksi Setoran Warga -->
    <div class="mb-8">
      <h3 class="text-xs font-black uppercase text-slate-700 mb-2 tracking-wider">2. Rincian Data Transaksi Warga</h3>
      <table class="w-full text-xs text-left border border-slate-200">
        <thead class="bg-slate-100 text-slate-700 font-bold">
          <tr>
            <th class="p-2 border-b border-r border-slate-200 w-8 text-center">No</th>
            <th class="p-2 border-b border-r border-slate-200">Tanggal</th>
            <th class="p-2 border-b border-r border-slate-200">Nama Warga</th>
            <th class="p-2 border-b border-r border-slate-200">Rumah</th>
            <th class="p-2 border-b border-r border-slate-200">Kategori</th>
            <th class="p-2 border-b border-r border-slate-200">Berat</th>
            <th class="p-2 border-b border-r border-slate-200 text-right">Poin</th>
            <th class="p-2 border-b border-slate-200 text-center">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
          @forelse($setorans as $s)
          <tr>
            <td class="p-2 border-r border-slate-200 text-center">{{ $loop->iteration }}</td>
            <td class="p-2 border-r border-slate-200 text-slate-600">{{ $s->tgl ? $s->tgl->locale('id')->translatedFormat('d/m/Y') : '-' }}</td>
            <td class="p-2 border-r border-slate-200 font-bold text-slate-800">{{ $s->user ? $s->user->nama : '-' }}</td>
            <td class="p-2 border-r border-slate-200 text-slate-600">{{ $s->user ? $s->user->rumah : '-' }}</td>
            <td class="p-2 border-r border-slate-200">{{ $s->kategori ? $s->kategori->nama : '-' }}</td>
            <td class="p-2 border-r border-slate-200 font-semibold">{{ ((float)$s->berat == (int)$s->berat ? (int)$s->berat : (float)$s->berat) }} {{ $s->satuan }}</td>
            <td class="p-2 border-r border-slate-200 text-right font-bold text-emerald-700">+{{ $s->poin }}</td>
            <td class="p-2 text-center font-bold">
              @if($s->status === 'diterima')
                <span class="text-emerald-700">Diterima</span>
              @elseif($s->status === 'ditolak')
                <span class="text-rose-600">Ditolak</span>
              @else
                <span class="text-amber-600">Diproses</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="p-4 text-center text-slate-400 italic">Belum ada transaksi pada periode ini.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Tanda Tangan -->
    <div class="mt-12 pt-4 flex justify-end text-xs">
      <div class="text-center max-w-[200px]">
        <p class="text-slate-500">Mengetahui,</p>
        <p class="font-bold text-slate-800 mt-0.5">Ketua RT 36 / RW 09</p>
        <div class="h-24 flex items-end justify-center pb-1">
          <span class="text-[10px] text-slate-300 italic">[ Tanda Tangan & Cap ]</span>
        </div>
        <p class="font-extrabold text-slate-900 underline">Pak RT</p>
      </div>
    </div>
    @endif

  </div>
</body>
</html>
