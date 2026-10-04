<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Sampah - {{ $periode }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; font-size: 12px; }
        h1 { text-align: center; color: #065f46; }
        .subtitle { text-align: center; color: #64748b; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background-color: #ecfdf5; color: #065f46; font-weight: bold; }
        .total-row { background-color: #f0fdf4; font-weight: bold; }
        .section-title { font-weight: bold; margin-top: 20px; color: #065f46; }
        .footer { margin-top: 30px; text-align: center; color: #94a3b8; font-size: 10px; }
    </style>
</head>
<body>
    <h1>LAPORAN REKAPITULASI PENGELOLAAN SAMPAH</h1>
    <p class="subtitle">PLUNG! - Sistem Pemilahan Sampah Mandiri<br>
    Periode: {{ $periode }}<br>
    Tanggal Cetak: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y, H:i') }} WIB</p>

    <p class="section-title">RINGKASAN PER KATEGORI SAMPAH</p>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kategori Sampah</th>
                <th>Jumlah Transaksi</th>
                <th>Total Berat (Kg)</th>
                <th>Total Poin</th>
            </tr>
        </thead>
        <tbody>
            @php $noKat = 1; @endphp
            @foreach($kategoriSummary as $kat)
            <tr>
                <td>{{ $noKat++ }}</td>
                <td>{{ $kat['nama'] }}</td>
                <td>{{ $kat['jumlah_transaksi'] }}</td>
                <td>{{ number_format($kat['total_berat_kg'], 2, ',', '.') }}</td>
                <td>{{ $kat['total_poin'] }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2">TOTAL</td>
                <td>{{ count($setorans) }}</td>
                <td>{{ number_format($totalBerat, 2, ',', '.') }}</td>
                <td>{{ $totalPoin }}</td>
            </tr>
        </tbody>
    </table>

    <p class="section-title">RINCIAN TRANSAKSI SETORAN WARGA</p>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Warga</th>
                <th>No. Rumah</th>
                <th>Kategori</th>
                <th>Berat</th>
                <th>Poin</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($setorans as $s)
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $s->tgl ? $s->tgl->locale('id')->translatedFormat('d/m/Y') : '-' }}</td>
                <td>{{ $s->user ? $s->user->nama : '-' }}</td>
                <td>{{ $s->user ? $s->user->rumah : '-' }}</td>
                <td>{{ $s->kategori ? $s->kategori->nama : '-' }}</td>
                <td>{{ ((float)$s->berat == (int)$s->berat ? (int)$s->berat : $s->berat) . ' ' . $s->satuan }}</td>
                <td>{{ $s->poin }}</td>
                <td>{{ ucfirst($s->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p class="footer">Dicetak otomatis oleh Sistem Aplikasi PLUNG!</p>
</body>
</html>
