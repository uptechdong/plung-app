<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Membuat penukaran tidak pernah meninggalkan jejak yang rusak.
 *
 * Masalahnya:
 *  1. `penukaran.reward_id` memakai ON DELETE CASCADE, jadi menghapus hadiah
 *     ikut menghapus baris penukarannya.
 *  2. Tapi `riwayat_aktivitas` TIDAK punya foreign key ke `penukaran` maupun
 *     `setoran`. Akibatnya baris riwayat untuk penukaran yang sudah terhapus
 *     tetap muncul di halaman Riwayat warga, padahal barangnya sudah tidak
 *     ada di katalog.
 *  3. Poin yang sudah dipotong saat penukaran itu juga tidak pernah
 *     dikembalikan.
 *
 * Yang dilakukan:
 *  1. Hapus riwayat yatim dan kembalikan poinnya dengan menghitung ulang
 *     saldo dari riwayat yang tersisa (dengan begitu saldo selalu cocok
 *     dengan riwayatnya).
 *  2. Simpan nilai poin pada tabel `penukaran` supaya saat hadiah dihapus,
 *     jumlah poin yang harus dikembalikan diketahui dengan tepat.
 *  3. Pasang foreign key ON DELETE CASCADE agar kejadian yang sama tidak
 *     terulang.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->hapusRiwayatYatim();
        $this->tambahKolomPoinPenukaran();
        $this->hitungUlangSaldoPoin();
        $this->pasangForeignKey();
        $this->resetSnapshotPeringkat();
    }

    public function down(): void
    {
        // Riwayat yatim yang sudah dihapus dan poin yang sudah dikembalikan
        // tidak bisa dikembalikan dengan aman.
    }

    /** Buang riwayat yang menunjuk ke penukaran / setoran yang sudah tidak ada. */
    private function hapusRiwayatYatim(): void
    {
        $total = 0;

        // Id diambil dulu dengan select, baru dihapus: DELETE dengan JOIN tidak
        // sah di MySQL.
        $yatimPenukaran = DB::select('SELECT r.id FROM riwayat_aktivitas r
            LEFT JOIN penukaran p ON r.penukaran_id = p.id
            WHERE r.penukaran_id IS NOT NULL AND p.id IS NULL');
        if ($yatimPenukaran) {
            $total += DB::table('riwayat_aktivitas')
                ->whereIn('id', array_column($yatimPenukaran, 'id'))
                ->delete();
        }

        $yatimSetoran = DB::select('SELECT r.id FROM riwayat_aktivitas r
            LEFT JOIN setoran s ON r.setoran_id = s.id
            WHERE r.setoran_id IS NOT NULL AND s.id IS NULL');
        if ($yatimSetoran) {
            $total += DB::table('riwayat_aktivitas')
                ->whereIn('id', array_column($yatimSetoran, 'id'))
                ->delete();
        }

        echo '  riwayat yatim dihapus: ' . $total . PHP_EOL;
    }

    /** Catat berapa poin yang dipotong tiap penukaran, supaya refund-nya tepat. */
    private function tambahKolomPoinPenukaran(): void
    {
        if (Schema::hasColumn('penukaran', 'poin')) {
            return;
        }

        Schema::table('penukaran', function ($table) {
            $table->unsignedInteger('poin')->default(0)->after('reward_id');
        });

        // Isi dari riwayat (nilainya negatif karena pencurangan).
        $baris = DB::table('penukaran as p')
            ->leftJoin('riwayat_aktivitas as r', 'r.penukaran_id', '=', 'p.id')
            ->whereNotNull('r.penukaran_id')
            ->select('p.id', DB::raw('MIN(ABS(r.poin)) AS poin'))
            ->groupBy('p.id')
            ->get();

        foreach ($baris as $b) {
            DB::table('penukaran')->where('id', $b->id)->update(['poin' => (int) $b->poin]);
        }

        echo '  kolom penukaran.poin ditambahkan & diisi: ' . $baris->count() . PHP_EOL;
    }

    /**
     * Hitung ulang saldo poin setiap warga dari riwayat aktivitasnya.
     * Ini yang mengembalikan poin hasil penukaran ke barang yang sudah dihapus:
     * baris riwayat penukaran yang yatim dibuang, sehingga yang menguranginya
     * tidak lagi memotong saldo.
     */
    private function hitungUlangSaldoPoin(): void
    {
        $perubahan = [];

        foreach (DB::table('users')->where('role', 'warga')->orderBy('id')->get() as $user) {
            $total = (int) DB::table('riwayat_aktivitas')
                ->where('user_id', $user->id)
                ->sum('poin');

            if ($total < 0) {
                $total = 0;
            }

            if ($total !== (int) $user->poin) {
                $perubahan[] = sprintf(
                    '%s: %s -> %s (%s%s)',
                    $user->nama,
                    $user->poin,
                    $total,
                    $total - (int) $user->poin >= 0 ? '+' : '',
                    $total - (int) $user->poin
                );
            }

            DB::table('users')->where('id', $user->id)->update(['poin' => $total]);
        }

        foreach ($perubahan as $baris) {
            echo '  saldo poin ' . $baris . PHP_EOL;
        }
    }

    /** CASCADE supaya penghapusan induk ikut membersihkan riwayat. */
    private function pasangForeignKey(): void
    {
        $ada = function (string $kolom) {
            return DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
                               WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'riwayat_aktivitas'
                                 AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL", [$kolom]);
        };

        if (! $ada('penukaran_id')) {
            DB::statement('ALTER TABLE riwayat_aktivitas
                ADD CONSTRAINT riwayat_aktivitas_penukaran_id_foreign
                FOREIGN KEY (penukaran_id) REFERENCES penukaran(id) ON DELETE CASCADE');
        }

        if (! $ada('setoran_id')) {
            DB::statement('ALTER TABLE riwayat_aktivitas
                ADD CONSTRAINT riwayat_aktivitas_setoran_id_foreign
                FOREIGN KEY (setoran_id) REFERENCES setoran(id) ON DELETE CASCADE');
        }

        echo '  foreign key riwayat -> penukaran & setoran dipasang' . PHP_EOL;
    }

    private function resetSnapshotPeringkat(): void
    {
        DB::table('users')->where('role', 'warga')->update(['peringkat_terakhir' => null]);
    }
};
