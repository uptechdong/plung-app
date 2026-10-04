<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Menormalkan satuan pada data setoran yang sudah terlanjur tersimpan.
 *
 * Latar belakang: kolom `satuan` sebelumnya disimpan apa adanya tanpa
 * dipakai saat menghitung poin. Warga yang mengisi "800 Gram" pada kategori
 * berpunit Kg (atau kategori per-unit seperti B3) terhitung 800, sehingga
 * poinnya meledak ~1000x dan ikut merusak peringkat.
 *
 * Yang dilakukan:
 *  1. Berat tiap setoran dinormalkan ke satuan dasar kategorinya
 *     (Gram -> Kg untuk kategori Kg; kategori per-unit tidak dikonversi).
 *  2. Poin dihitung ulang dari berat yang sudah benar.
 *  3. Saldo poin warga disesuaikan dengan selisihnya.
 *  4. `peringkat_terakhir` dikosongkan karena berbasis data lama.
 *
 * Aman dijalankan ulang: hanya menyentuh baris yang satuannya masih berbeda
 * dari satuan dasar kategorinya.
 */
return new class extends Migration
{
    /** Pengali ke satuan dasar. */
    private const FAKTOR = [
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

    public function up(): void
    {
        $kategori = DB::table('kategori_sampah')->get()->keyBy('id');

        $perluDirektif = DB::table('setoran')->get()->filter(function ($s) use ($kategori) {
            $k = $kategori[$s->kategori_id] ?? null;

            return $k !== null
                && strtolower(trim((string) $s->satuan)) !== strtolower(trim((string) $k->satuan));
        });

        if ($perluDirektif->isEmpty()) {
            echo '  tidak ada setoran yang perlu dinormalisasi' . PHP_EOL;

            return;
        }

        $deltaUser = [];
        $laporan = [];

        foreach ($perluDirektif as $s) {
            $k = $kategori[$s->kategori_id];
            $dasar = trim((string) $k->satuan);
            $kunci = strtolower(trim((string) $s->satuan));
            $faktor = self::FAKTOR[$kunci] ?? null;
            $iniBerat = in_array($kunci, self::SATUAN_BERAT, true);

            $beratBaru = ($iniBerat && $faktor !== null)
                ? round((float) $s->berat * $faktor, 2)
                : round((float) $s->berat, 2);

            $poinBaru = (int) round($beratBaru * $k->poin_per_unit);
            $selisih = $poinBaru - (int) $s->poin;

            DB::table('setoran')->where('id', $s->id)->update([
                'berat' => $beratBaru,
                'satuan' => $dasar,
                'poin' => $poinBaru,
            ]);

            DB::table('riwayat_aktivitas')->where('setoran_id', $s->id)->update([
                'poin' => $poinBaru,
                'rincian' => $this->formatBerat($beratBaru) . ' ' . $dasar . ' ' . $k->nama,
            ]);

            $deltaUser[$s->user_id] = ($deltaUser[$s->user_id] ?? 0) + $selisih;

            $laporan[] = sprintf(
                'setoran#%s %s %s %s -> %s %s | poin %s -> %s (%s)',
                $s->id,
                $s->berat,
                $s->satuan,
                $k->nama,
                $this->formatBerat($beratBaru),
                $dasar,
                $s->poin,
                $poinBaru,
                ($selisih >= 0 ? '+' : '') . $selisih
            );
        }

        foreach ($deltaUser as $userId => $delta) {
            if ($delta === 0) {
                continue;
            }

            $user = DB::table('users')->where('id', $userId)->first();
            $saldoBaru = (int) $user->poin + $delta;

            if ($saldoBaru < 0) {
                Log::warning('Normalisasi setoran: saldo poin menjadi negatif, dipatok 0.', [
                    'user_id' => $userId,
                    'poin_sebelum' => (int) $user->poin,
                    'delta' => $delta,
                ]);
                $saldoBaru = 0;
            }

            DB::table('users')->where('id', $userId)->update(['poin' => $saldoBaru]);

            $laporan[] = sprintf('saldo poin %s: %s -> %s (%s)', $user->nama, $user->poin, $saldoBaru, ($delta >= 0 ? '+' : '') . $delta);
        }

        DB::table('users')->where('role', 'warga')->update(['peringkat_terakhir' => null]);

        foreach ($laporan as $baris) {
            echo '  ' . $baris . PHP_EOL;
        }
        Log::info('Normalisasi satuan setoran selesai.', ['laporan' => $laporan]);
    }

    public function down(): void
    {
    }

    private function formatBerat(float $berat): string
    {
        return fmod($berat, 1.0) === 0.0
            ? (string) (int) $berat
            : rtrim(rtrim(number_format($berat, 2, ',', ''), '0'), ',');
    }
};
