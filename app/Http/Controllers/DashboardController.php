<?php

namespace App\Http\Controllers;

use App\Models\Penukaran;
use App\Models\Reward;
use App\Models\RiwayatAktivitas;
use App\Models\Setoran;
use App\Models\User;
use App\Services\PeringkatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private readonly PeringkatService $peringkat)
    {
    }

    /**
     * Tiga warga teratas berdasarkan PILAH SAMPAH.
     *
     * Sumber datanya hanya pengumpulan sampah terverifikasi (setoran berstatus
     * "diterima"), dan urutannya memakai peringkat yang sama dengan dashboard
     * warga supaya tidak ada dua definisi "paling aktif" di aplikasi.
     * Poin sengaja tidak dipakai: nilainya naik-turun setiap kali warga menukar
     * hadiah sehingga urutan ikut bergeser.
     */
    private function top3PilahSampah(): array
    {
        $daftarPeringkat = $this->peringkat->semuaPeringkat();

        return User::query()
            ->where('role', 'warga')
            ->select('id', 'nama', 'rumah', 'poin')
            ->selectSub(
                Setoran::query()
                    ->selectRaw('COALESCE(SUM(berat), 0)')
                    ->whereColumn('setoran.user_id', 'users.id')
                    ->where('setoran.status', 'diterima'),
                'total_sampah'
            )
            ->get()
            ->filter(fn ($u) => (float) $u->total_sampah > 0)
            ->sort(function ($a, $b) {
                $sampahA = (float) $a->total_sampah;
                $sampahB = (float) $b->total_sampah;

                if ($sampahA !== $sampahB) {
                    return $sampahB <=> $sampahA;
                }

                return $a->id <=> $b->id;
            })
            ->take(3)
            ->values()
            ->map(fn ($u, $i) => [
                'rank' => $i + 1,
                'peringkat' => $daftarPeringkat[$u->id] ?? ($i + 1),
                'id' => $u->id,
                'nama' => $u->nama,
                'rumah' => $u->rumah,
                'total_sampah' => (float) $u->total_sampah,
                'poin' => (int) $u->poin,
            ])
            ->all();
    }

    public function warga(): JsonResponse
    {
        $user = Auth::user();

        $totalSampah = (float) Setoran::where('user_id', $user->id)
            ->where('status', 'diterima')
            ->sum('berat');

        $daftarPeringkat = $this->peringkat->semuaPeringkat();
        $totalWarga = count($daftarPeringkat);
        $peringkat = $this->peringkat->peringkat($user, $daftarPeringkat);

        $top3 = $this->top3PilahSampah();

        $riwayatTerakhir = Setoran::where('user_id', $user->id)
            ->with('kategori')
            ->terbaru()
            ->limit(5)
            ->get()
            ->map(fn ($s) => [
                'tanggal' => $s->tgl ? $s->tgl->locale('id')->translatedFormat('d M Y') : '-',
                'kategori' => $s->kategori ? $s->kategori->nama : '-',
                'berat' => ((float) $s->berat == (int) $s->berat ? (int) $s->berat : (float) $s->berat) . ' ' . $s->satuan,
                'status' => $s->status,
                'poin' => (int) $s->poin,
            ]);

        $bulanan = $this->getBulananData($user->id);

        $proporsi = $this->getProporsiData($user->id);

        $trendPoin = $this->getPoinTerakhir($user);
        $trendSampah = $this->getTrendData($user->id, 'berat');
        $trendPeringkat = $this->peringkat->tren($user, $daftarPeringkat);
        $trendBulanan = $this->getTrendBulanan($user);

        return response()->json([
            'success' => true,
            'poin' => (int) $user->poin,
            'total_sampah' => (float) $totalSampah,
            'peringkat' => $peringkat,
            'total_warga' => $totalWarga,
            'top3' => $top3,
            'riwayat_terakhir' => $riwayatTerakhir,
            'bulanan' => $bulanan,
            'proporsi' => $proporsi,
            'trend_poin' => $trendPoin,
            'trend_sampah' => $trendSampah,
            'trend_peringkat' => $trendPeringkat,
            'trend_bulanan' => $trendBulanan,
        ]);
    }

    private function getTrendBulanan(User $user): array
    {
        $now = now();
        $bulanIni = $now->month;
        $tahunIni = $now->year;
        $bulanLalu = $now->copy()->subMonth()->month;
        $tahunLalu = $now->copy()->subMonth()->year;

        $userBulanIni = (float) Setoran::where('user_id', $user->id)
            ->where('status', 'diterima')
            ->whereYear('tgl', $tahunIni)
            ->whereMonth('tgl', $bulanIni)
            ->sum('berat');

        $totalBulanIni = (float) Setoran::where('status', 'diterima')
            ->whereYear('tgl', $tahunIni)
            ->whereMonth('tgl', $bulanIni)
            ->sum('berat');

        $totalBulanLalu = (float) Setoran::where('status', 'diterima')
            ->whereYear('tgl', $tahunLalu)
            ->whereMonth('tgl', $bulanLalu)
            ->sum('berat');

        $persentaseBulanIni = $totalBulanIni > 0 ? round(($userBulanIni / $totalBulanIni) * 100, 1) : 0;
        $persentaseBulanLalu = $totalBulanLalu > 0 ? round((Setoran::where('user_id', $user->id)
            ->where('status', 'diterima')
            ->whereYear('tgl', $tahunLalu)
            ->whereMonth('tgl', $bulanLalu)
            ->sum('berat') / $totalBulanLalu) * 100, 1) : 0;

        $selisih = $persentaseBulanIni - $persentaseBulanLalu;

        if ($selisih > 0) {
            return ['direction' => 'up', 'percentage' => abs($selisih), 'value' => $persentaseBulanIni, 'nilai_bulan_ini' => $persentaseBulanIni, 'nilai_bulan_lalu' => $persentaseBulanLalu];
        } elseif ($selisih < 0) {
            return ['direction' => 'down', 'percentage' => abs($selisih), 'value' => $persentaseBulanIni, 'nilai_bulan_ini' => $persentaseBulanIni, 'nilai_bulan_lalu' => $persentaseBulanLalu];
        }

        return ['direction' => 'same', 'percentage' => 0, 'value' => $persentaseBulanIni, 'nilai_bulan_ini' => $persentaseBulanIni, 'nilai_bulan_lalu' => $persentaseBulanLalu];
    }

    /**
     * Pergerakan poin TERAKHIR untuk badge di card "Total Poin Saya".
     *
     * Hanya satu arah yang ditampilkan, yaitu arah dari pergerakan terakhir:
     *  - naik  : setoran yang terakhir diverifikasi petugas
     *  - turun : penukaran hadiah yang terakhir
     *
     * Setoran diambil dari tabel setoran berstatus 'diterima', bukan dari riwayat
     * aktivitas. Riwayat sudah menulis nilai positif begitu warga mengirim
     * laporan, padahal poinnya baru masuk ke saldo setelah petugas memverifikasi.
     * Kalau diambil dari riwayat, setoran yang masih menunggu verifikasi ikut
     * terhitung dan badge terlihat lebih besar daripada saldo yang sebenarnya.
     *
     * Waktu yang dibandingkan adalah `setoran.updated_at`, karena itulah saat
     * petugas memverifikasi; kolom `tgl` hanya berisi tanggal tanpa jam. Untuk
     * penukaran dipakai `created_at`, yaitu saat kupon terbit.
     *
     * @return array{direction: string, value: int, sumber: ?string, keterangan: string, tanggal: string}
     */
    private function getPoinTerakhir(User $user): array
    {
        $belumAda = [
            'direction' => 'same',
            'value' => 0,
            'sumber' => null,
            'keterangan' => 'Belum ada perubahan poin',
            'tanggal' => '-',
        ];

        $setoran = Setoran::where('user_id', $user->id)
            ->where('status', 'diterima')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->first(['poin', 'updated_at']);

        $penukaran = Penukaran::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->first(['id', 'poin', 'created_at']);

        if (! $setoran && ! $penukaran) {
            return $belumAda;
        }

        $poinTukar = $penukaran ? (int) $penukaran->poin : 0;
        if ($penukaran && $poinTukar <= 0) {
            $poinTukar = abs((int) RiwayatAktivitas::where('penukaran_id', $penukaran->id)->value('poin'));
        }

        $waktuSetoran = $setoran?->updated_at;
        $waktuTukar = $penukaran?->created_at;

        if ($waktuTukar && (! $waktuSetoran || $waktuTukar >= $waktuSetoran)) {
            if ($poinTukar <= 0) {
                return $belumAda;
            }

            return [
                'direction' => 'down',
                'value' => $poinTukar,
                'sumber' => 'penukaran',
                'keterangan' => 'dipakai untuk menukar hadiah',
                'tanggal' => $waktuTukar->locale('id')->translatedFormat('d M Y'),
            ];
        }

        if ((int) $setoran->poin <= 0) {
            return $belumAda;
        }

        return [
            'direction' => 'up',
            'value' => (int) $setoran->poin,
            'sumber' => 'setoran',
            'keterangan' => 'didapat dari setoran sampah terverifikasi',
            'tanggal' => $waktuSetoran->locale('id')->translatedFormat('d M Y'),
        ];
    }

    private function getTrendData(int $userId, string $field): array
    {
        $now = now();
        $bulanIni = $now->month;
        $tahunIni = $now->year;
        $bulanLalu = $now->copy()->subMonth()->month;
        $tahunLalu = $now->copy()->subMonth()->year;

        $nilaiBulanIni = (float) Setoran::where('user_id', $userId)
            ->where('status', 'diterima')
            ->whereYear('tgl', $tahunIni)
            ->whereMonth('tgl', $bulanIni)
            ->sum($field);

        $nilaiBulanLalu = (float) Setoran::where('user_id', $userId)
            ->where('status', 'diterima')
            ->whereYear('tgl', $tahunLalu)
            ->whereMonth('tgl', $bulanLalu)
            ->sum($field);

        if ($nilaiBulanLalu == 0) {
            if ($nilaiBulanIni > 0) {
                return ['direction' => 'up', 'percentage' => 100, 'value' => round($nilaiBulanIni, 2), 'nilai_bulan_ini' => round($nilaiBulanIni, 2), 'nilai_bulan_lalu' => 0];
            }
            return ['direction' => 'same', 'percentage' => 0, 'value' => 0, 'nilai_bulan_ini' => 0, 'nilai_bulan_lalu' => 0];
        }

        $selisih = $nilaiBulanIni - $nilaiBulanLalu;
        $percentage = round(($selisih / $nilaiBulanLalu) * 100, 1);

        if ($selisih > 0) {
            return ['direction' => 'up', 'percentage' => abs($percentage), 'value' => round($selisih, 2), 'nilai_bulan_ini' => round($nilaiBulanIni, 2), 'nilai_bulan_lalu' => round($nilaiBulanLalu, 2)];
        } elseif ($selisih < 0) {
            return ['direction' => 'down', 'percentage' => abs($percentage), 'value' => round(abs($selisih), 2), 'nilai_bulan_ini' => round($nilaiBulanIni, 2), 'nilai_bulan_lalu' => round($nilaiBulanLalu, 2)];
        }

        return ['direction' => 'same', 'percentage' => 0, 'value' => 0, 'nilai_bulan_ini' => round($nilaiBulanIni, 2), 'nilai_bulan_lalu' => round($nilaiBulanLalu, 2)];
    }

    private function getBulananData(int $userId): array
    {
        $bulanan = Setoran::where('user_id', $userId)
            ->where('status', 'diterima')
            ->selectRaw('MONTH(tgl) as bulan, SUM(berat) as total_berat')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total_berat', 'bulan')
            ->map(fn ($val) => round((float) $val, 2))
            ->toArray();

        $labels = [];
        $data = [];
        $namaBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        for ($m = 1; $m <= 12; $m++) {
            $labels[] = $namaBulan[$m - 1];
            $data[] = $bulanan[$m] ?? 0;
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function getProporsiData(int $userId): array
    {
        $proporsi = Setoran::where('setoran.user_id', $userId)
            ->where('setoran.status', 'diterima')
            ->join('kategori_sampah', 'setoran.kategori_id', '=', 'kategori_sampah.id')
            ->selectRaw('kategori_sampah.nama, kategori_sampah.warna, SUM(setoran.berat) as total_berat')
            ->groupBy('kategori_sampah.id', 'kategori_sampah.nama', 'kategori_sampah.warna')
            ->orderByDesc('total_berat')
            ->get();

        $labels = $proporsi->pluck('nama')->toArray();
        $data = $proporsi->pluck('total_berat')->map(fn ($v) => round((float) $v, 2))->toArray();
        $colors = $proporsi->pluck('warna')->toArray();

        if (empty($labels)) {
            $labels = ['Belum ada data'];
            $data = [1];
            $colors = ['#94a3b8'];
        }

        return ['labels' => $labels, 'data' => $data, 'colors' => $colors];
    }

    public function pengurus(): JsonResponse
    {
        $totalWarga = User::where('role', 'warga')->count();
        $totalSampah = (float) Setoran::where('status', 'diterima')->sum('berat');
        $antrean = Setoran::where('status', 'diproses')->count();

        $top3 = $this->top3PilahSampah();

        $transaksiTerbaru = Setoran::with(['user', 'kategori'])
            ->terbaru()
            ->limit(5)
            ->get()
            ->map(fn ($s) => [
                'tanggal' => $s->tgl ? $s->tgl->locale('id')->translatedFormat('d M Y') : '-',
                'nama' => $s->user ? $s->user->nama : '-',
                'rincian' => (((float) $s->berat == (int) $s->berat ? (int) $s->berat : (float) $s->berat) . ' ' . $s->satuan . ' ' . ($s->kategori ? $s->kategori->nama : '')),
                'status' => $s->status,
                'poin' => (int) $s->poin,
            ]);

        $bulanan = $this->getBulananDataRT();
        $komposisi = $this->getKomposisiDataRT();

        $trendWarga = $this->getTrendWarga();
        $trendSampahRT = $this->getTrendSampahRT();

        return response()->json([
            'success' => true,
            'total_warga' => $totalWarga,
            'total_sampah' => (float) $totalSampah,
            'antrean' => $antrean,
            'top3' => $top3,
            'transaksi_terbaru' => $transaksiTerbaru,
            'bulanan' => $bulanan,
            'komposisi' => $komposisi,
            'trend_warga' => $trendWarga,
            'trend_sampah_rt' => $trendSampahRT,
        ]);
    }

    private function getTrendWarga(): array
    {
        $now = now();
        $bulanIni = $now->month;
        $tahunIni = $now->year;
        $bulanLalu = $now->copy()->subMonth()->month;
        $tahunLalu = $now->copy()->subMonth()->year;

        $wargaBulanIni = User::where('role', 'warga')
            ->where(function ($q) use ($tahunIni, $bulanIni) {
                $q->whereYear('created_at', '<', $tahunIni)
                    ->orWhere(function ($q) use ($tahunIni, $bulanIni) {
                        $q->whereYear('created_at', $tahunIni)
                            ->whereMonth('created_at', '<=', $bulanIni);
                    });
            })
            ->count();

        $wargaBulanLalu = User::where('role', 'warga')
            ->where(function ($q) use ($tahunLalu, $bulanLalu) {
                $q->whereYear('created_at', '<', $tahunLalu)
                    ->orWhere(function ($q) use ($tahunLalu, $bulanLalu) {
                        $q->whereYear('created_at', $tahunLalu)
                            ->whereMonth('created_at', '<=', $bulanLalu);
                    });
            })
            ->count();

        if ($wargaBulanLalu == 0) {
            if ($wargaBulanIni > 0) {
                return ['direction' => 'up', 'percentage' => null, 'value' => $wargaBulanIni];
            }
            return ['direction' => 'same', 'percentage' => 0, 'value' => 0];
        }

        $selisih = $wargaBulanIni - $wargaBulanLalu;
        $percentage = round(($selisih / $wargaBulanLalu) * 100, 1);

        if ($selisih > 0) {
            return ['direction' => 'up', 'percentage' => abs($percentage), 'value' => $selisih];
        } elseif ($selisih < 0) {
            return ['direction' => 'down', 'percentage' => abs($percentage), 'value' => abs($selisih)];
        }

        return ['direction' => 'same', 'percentage' => 0, 'value' => 0];
    }

    private function getTrendSampahRT(): array
    {
        $now = now();
        $bulanIni = $now->month;
        $tahunIni = $now->year;
        $bulanLalu = $now->copy()->subMonth()->month;
        $tahunLalu = $now->copy()->subMonth()->year;

        $sampahBulanIni = (float) Setoran::where('status', 'diterima')
            ->whereYear('tgl', $tahunIni)
            ->whereMonth('tgl', $bulanIni)
            ->sum('berat');

        $sampahBulanLalu = (float) Setoran::where('status', 'diterima')
            ->whereYear('tgl', $tahunLalu)
            ->whereMonth('tgl', $bulanLalu)
            ->sum('berat');

        if ($sampahBulanLalu == 0) {
            if ($sampahBulanIni > 0) {
                return ['direction' => 'up', 'percentage' => 100, 'value' => round($sampahBulanIni, 2)];
            }
            return ['direction' => 'same', 'percentage' => 0, 'value' => 0];
        }

        $selisih = $sampahBulanIni - $sampahBulanLalu;
        $percentage = round(($selisih / $sampahBulanLalu) * 100, 1);

        if ($selisih > 0) {
            return ['direction' => 'up', 'percentage' => abs($percentage), 'value' => round($selisih, 2)];
        } elseif ($selisih < 0) {
            return ['direction' => 'down', 'percentage' => abs($percentage), 'value' => round(abs($selisih), 2)];
        }

        return ['direction' => 'same', 'percentage' => 0, 'value' => 0];
    }

    private function getBulananDataRT(): array
    {
        $bulanan = Setoran::where('status', 'diterima')
            ->selectRaw('MONTH(tgl) as bulan, SUM(berat) as total_berat')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total_berat', 'bulan')
            ->map(fn ($val) => round((float) $val, 2))
            ->toArray();

        $labels = [];
        $data = [];
        $namaBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        for ($m = 1; $m <= 12; $m++) {
            $labels[] = $namaBulan[$m - 1];
            $data[] = $bulanan[$m] ?? 0;
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function getKomposisiDataRT(): array
    {
        $komposisi = Setoran::where('setoran.status', 'diterima')
            ->join('kategori_sampah', 'setoran.kategori_id', '=', 'kategori_sampah.id')
            ->selectRaw('kategori_sampah.nama, kategori_sampah.warna, SUM(setoran.berat) as total_berat')
            ->groupBy('kategori_sampah.id', 'kategori_sampah.nama', 'kategori_sampah.warna')
            ->orderByDesc('total_berat')
            ->get();

        $labels = $komposisi->pluck('nama')->toArray();
        $data = $komposisi->pluck('total_berat')->map(fn ($v) => round((float) $v, 2))->toArray();
        $colors = $komposisi->pluck('warna')->toArray();

        if (empty($labels)) {
            $labels = ['Belum ada data'];
            $data = [1];
            $colors = ['#94a3b8'];
        }

        return ['labels' => $labels, 'data' => $data, 'colors' => $colors];
    }
}
