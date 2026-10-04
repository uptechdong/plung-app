<?php

namespace App\Http\Controllers;

use App\Models\KategoriSampah;
use App\Models\RiwayatAktivitas;
use App\Models\Reward;
use App\Models\Setoran;
use App\Services\PeringkatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SetoranController extends Controller
{
    public function __construct(private readonly PeringkatService $peringkat)
    {
    }

    public function index(): JsonResponse
    {
        $user = Auth::user();

        if ($user->isPengurus()) {
            $setoran = Setoran::with(['user', 'kategori'])
                ->terbaru()
                ->get()
                ->map(fn ($s) => $this->formatSetoran($s));
        } else {
            $setoran = Setoran::with(['kategori'])
                ->where('user_id', $user->id)
                ->terbaru()
                ->get()
                ->map(fn ($s) => $this->formatSetoran($s));
        }

        return response()->json(['success' => true, 'data' => $setoran]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'kategori_id' => 'required|exists:kategori_sampah,id',
            'berat' => 'required|numeric|min:0.001|max:100000',
            'satuan' => 'required|string',
            'catatan' => 'nullable|string',
            // Batas 3 MB (bukan 2 MB) disamakan dengan foto profil. Foto kamera HP
            // sekarang bisa 3-8 MB; sisi klien sudah mengompres gambar ke
            // bawah 1 MB sebelum dikirim, jadi angka ini cuma jaring
            // pengaman kalau kompresi gagal (mis. berkas HEIC).
            'foto' => 'nullable|image|max:3072',
        ]);

        $kategori = KategoriSampah::findOrFail($data['kategori_id']);

        // Berat WAJIB disimpan dalam satuan dasar kategori, karena poin_per_unit
        // dihitung per satuan itu. Kalau warga mengetik "800" dengan satuan Gram
        // pada kategori berpunit Kg, nilainya harus jadi 0,8 -- bukan 800.
        // Tanpa normalisasi ini, 800 Gram terhitung 800 Kg dan poinnya meledak
        // 1000 kali.
        $satuanDasar = trim((string) $kategori->satuan);
        $beratDasar = $this->keSatuanDasar((float) $data['berat'], (string) $data['satuan'], $satuanDasar);

        if ($beratDasar === null) {
            throw ValidationException::withMessages([
                'satuan' => 'Satuan "' . $data['satuan'] . '" tidak dikenali untuk kategori ini. '
                    . 'Gunakan: ' . implode(', ', $this->satuanYangDipakai($kategori)) . '.',
            ]);
        }

        if ($beratDasar <= 0) {
            throw ValidationException::withMessages([
                'berat' => 'Berat harus lebih besar dari nol. Contoh: 500 Gram = 0,5 Kg.',
            ]);
        }

        if ($beratDasar > self::BERAT_MAKS) {
            throw ValidationException::withMessages([
                'berat' => 'Berat maksimal ' . number_format(self::BERAT_MAKS, 0, ',', '.') . ' '
                    . $satuanDasar . ' per setoran. Kalau maksudnya jauh lebih kecil, '
                    . 'pilih satuan Gram.',
            ]);
        }

        // Dibulatkan, bukan dipotong, supaya warga tidak kehilangan poin
        // karena sisa pecahan.
        $poin = (int) round($beratDasar * $kategori->poin_per_unit);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('setoran', 'public');
        }

        $setoran = Setoran::create([
            'user_id' => Auth::id(),
            'kategori_id' => $data['kategori_id'],
            'berat' => $beratDasar,
            'satuan' => $satuanDasar,
            'tgl' => now(),
            'catatan' => $data['catatan'] ?? null,
            'foto' => $fotoPath,
            'status' => 'diproses',
            'poin' => $poin,
        ]);

        RiwayatAktivitas::create([
            'user_id' => Auth::id(),
            'jenis' => 'setor_sampah',
            'rincian' => $this->formatBerat($beratDasar) . ' ' . $satuanDasar . ' ' . $kategori->nama,
            'status' => 'diproses',
            'poin' => $poin,
            'setoran_id' => $setoran->id,
        ]);

        return response()->json(['success' => true, 'data' => $setoran]);
    }

    /** Pengali ke satuan dasar. Gram = 0,001 Kg; satuan lain tidak dikonversi. */
    private const FAKTOR_SATUAN = [
        'kg' => 1.0,
        'gram' => 0.001,
        'gr' => 0.001,
        'unit' => 1.0,
        'buah' => 1.0,
        'biji' => 1.0,
        'pcs' => 1.0,
    ];

    /** Satuan yang menyatakan BERAT (bukan menghitung). */
    private const SATUAN_BERAT = ['kg', 'gram', 'gr'];

    /** Batas atas berat per setoran, dalam satuan dasar (Kg atau Unit). */
    private const BERAT_MAKS = 1000;

    /**
     * Satuan yang boleh dipilih untuk sebuah kategori.
     *
     * Kategori berpunit Kg boleh diisi Kg atau Gram. Kategori per-unit (B3)
     * hanya boleh diisi satuannya sendiri: memakai "Buah" dan "Biji" untuk
     * B3 membingungkan warga (baterai 1 buah atau 1 biji?), padahal angkanya
     * sama saja dan sudah diterima sebagai "Unit". Memperkecil dropdown
     * sekaligus mengurangi kesalahan input.
     *
     * @return array<int, string>
     */
    public function satuanYangDipakai(KategoriSampah $kategori): array
    {
        return strtolower(trim((string) $kategori->satuan)) === 'kg'
            ? ['Kg', 'Gram']
            : [$kategori->satuan];
    }

    /**
     * Ubah berat + satuan pilihan warga menjadi satuan dasar kategori.
     * Mengembalikan null kalau satuannya tidak dikenal ATAU jenisnya tidak
     * cocok dengan satuan dasar kategori.
     */
    private function keSatuanDasar(float $berat, string $satuan, string $satuanDasar): ?float
    {
        $kunci = strtolower(trim($satuan));
        if (!array_key_exists($kunci, self::FAKTOR_SATUAN)) {
            return null;
        }

        $dasarAdalahBerat = strtolower(trim($satuanDasar)) === 'kg';
        $iniAdalahBerat = in_array($kunci, self::SATUAN_BERAT, true);

        // Kategori ber-unit tidak boleh diisi satuan berat, dan sebaliknya.
        // Kalau ini dilewati, "800 Gram" pada kategori B3 (25 poin per Unit)
        // akan ditafsirkan 800 Unit = 20.000 poin, bukan 0,8.
        if ($dasarAdalahBerat !== $iniAdalahBerat) {
            return null;
        }

        return round($dasarAdalahBerat ? $berat * self::FAKTOR_SATUAN[$kunci] : $berat, 2);
    }

    /** Tulis angka dengan koma desimal supaya mudah dibaca warga. */
    private function formatBerat(float $berat): string
    {
        return fmod($berat, 1.0) === 0.0
            ? (string) (int) $berat
            : rtrim(rtrim(number_format($berat, 2, ',', ''), '0'), ',');
    }

    public function verify(Request $request, Setoran $setoran): JsonResponse
    {
        $data = $request->validate([
            'status' => 'required|in:diterima,ditolak',
        ]);

        // Peringkat warga dihitung dari TOTAL SAMPAH terverifikasi, jadi
        // snapshot harus diambil SEBELUM status berubah -- kalau tidak, rank
        // yang terkunci sudah termasuk setoran ini sendiri. Cukup dijalankan
        // saat status benar-benar pindah (ditolak <-> diterima), karena itu
        // satu-satunya momen total sampah warga bertambah atau berkurang.
        $totalBerubah = ($data['status'] === 'diterima') !== ($setoran->status === 'diterima');
        if ($totalBerubah) {
            $this->peringkat->snapshot();
        }

        $setoran->update(['status' => $data['status']]);

        if ($data['status'] === 'diterima') {
            $user = $setoran->user;

            if ($user) {
                $user->increment('poin', $setoran->poin);
            }
        }

        // Update status di RiwayatAktivitas agar sinkron dengan setoran
        try {
            RiwayatAktivitas::where('setoran_id', $setoran->id)
                ->update(['status' => $data['status']]);
        } catch (\Exception $e) {
            // Log error tapi jangan gagalkan proses verifikasi
            \Log::error('Gagal update riwayat: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'data' => $setoran]);
    }

    public function kategori(): JsonResponse
    {
        $kategori = KategoriSampah::orderBy('id')->get();

        return response()->json(['success' => true, 'data' => $kategori->map(function ($k) {
            return [
                'id' => $k->id,
                'nama' => $k->nama,
                'poin_per_unit' => $k->poin_per_unit,
                'satuan' => $k->satuan,
                // Satuan yang boleh dipilih warga untuk kategori ini, supaya
                // form tidak menawarkan Gram pada kategori yang dihitung per unit.
                'satuan_opsi' => $this->satuanYangDipakai($k),
            ];
        })]);
    }

    public function riwayat(): JsonResponse
    {
        $user = Auth::user();

        $riwayat = $user->riwayatAktivitas()
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($r) {
                $jenis = str_replace('_', ' ', $r->jenis);
                $jenis = ucwords($jenis);

                // Selalu baca status langsung dari tabel terkait agar sinkron otomatis
                $status = $r->status;
                $rincian = $r->rincian;
                if ($r->jenis === 'setor_sampah' && $r->setoran_id) {
                    $setoran = DB::table('setoran')->where('id', $r->setoran_id)->first();
                    if ($setoran) {
                        $status = $setoran->status;
                    }
                } elseif ($r->jenis === 'penukaran_hadiah') {
                    $penukaran = DB::table('penukaran')
                        ->where('id', $r->penukaran_id ?? null)
                        ->first();
                    $status = $penukaran ? ($penukaran->diambil ? 'sudah_ditukar' : 'belum_ditukar') : 'belum_ditukar';
                    // Tambahkan kode kupon ke rincian
                    if ($penukaran) {
                        $rincian = $r->rincian . ' (' . $penukaran->kode_kupon . ')';
                    }
                }

                return [
                    'tanggal' => $r->created_at ? $r->created_at->locale('id')->translatedFormat('d M Y') : '-',
                    'jenis' => $jenis,
                    'rincian' => $rincian,
                    'status' => $status,
                    'poin' => (int) $r->poin,
                ];
            });

        return response()->json(['success' => true, 'data' => $riwayat]);
    }

    private function formatSetoran(Setoran $s): array
    {
        return [
            'id' => $s->id,
            'nama' => $s->user ? $s->user->nama : '-',
            'rumah' => $s->user ? $s->user->rumah : '-',
            'jenis' => $s->kategori ? $s->kategori->nama : '-',
            'berat' => ((float) $s->berat == (int) $s->berat ? (int) $s->berat : (float) $s->berat) . ' ' . $s->satuan,
            'tgl' => $s->tgl ? $s->tgl->locale('id')->translatedFormat('d M Y') : '-',
            'catatan' => $s->catatan,
            'foto' => $s->foto ? (str_starts_with($s->foto, 'http') ? $s->foto : asset('storage/' . $s->foto)) : null,
            'status' => $s->status,
            'poin' => (int) $s->poin,
        ];
    }
}
