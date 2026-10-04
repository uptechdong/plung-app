<?php

namespace App\Services;

use App\Models\Setoran;
use App\Models\User;

/**
 * Satu-satunya sumber kebenaran untuk peringkat warga.
 *
 * Semua tempat yang menampilkan peringkat WAJIB memakai service ini agar angka
 * peringkat dan snapshot `peringkat_terakhir` selalu memakai aturan yang sama.
 *
 * DASAR URUTAN: TOTAL SAMPAH TERVERIFIKASI (Kg) descending, dengan id warga
 * sebagai pemutus seri supaya urutannya selalu konsisten.
 *
 * Sengaja bukan poin. Poin naik dan turun setiap kali warga menukar hadiah, jadi
 * peringkat berbasis poin bisa meloncat naik/turun karena kegiatan yang tidak
 * ada hubungannya dengan kinerja pilah sampah. Total sampah hanya bertambah
 * saat setoran diverifikasi diterima, sehingga lebih stabil dan lebih adil
 * sebagai "peringkat pilah sampah".
 *
 * Warga yang belum punya setoran diterima (total 0) selalu di posisi terakhir.
 */
class PeringkatService
{
    /**
     * Total sampah terverifikasi (Kg) milik satu warga.
     */
    public function totalSampah(User $user): float
    {
        return (float) Setoran::query()
            ->where('user_id', $user->id)
            ->where('status', 'diterima')
            ->sum('berat');
    }

    /**
     * Peta id_user => peringkat untuk seluruh warga, dihitung dalam 1 query.
     *
     * Urutan TIDAK memakai poin sama sekali, hanya total sampah terverifikasi.
     * Poin sengaja dikeluarkan karena naik-turun setiap kali warga menukar
     * hadiah, dan pemecah-seri berbasis poin membuat peringkat bergeser
     * padahal tidak ada sampah baru yang masuk. Urutan dipecah dengan id agar
     * hasilnya selalu sama untuk data yang sama.
     *
     * @return array<int, int>
     */
    public function semuaPeringkat(): array
    {
        $totalWarga = User::where('role', 'warga')->count();

        $warga = User::query()
            ->where('role', 'warga')
            ->select('id')
            ->selectSub(
                Setoran::query()
                    ->selectRaw('COALESCE(SUM(berat), 0)')
                    ->whereColumn('setoran.user_id', 'users.id')
                    ->where('setoran.status', 'diterima'),
                'total_sampah'
            )
            ->get()
            ->sort(function ($a, $b) {
                $sampahA = (float) $a->total_sampah;
                $sampahB = (float) $b->total_sampah;

                if ($sampahA !== $sampahB) {
                    return $sampahB <=> $sampahA;
                }

                return $a->id <=> $b->id;
            })
            ->values();

        $peringkat = [];
        $posisi = 0;

        foreach ($warga as $w) {
            $posisi++;
            $peringkat[$w->id] = (float) $w->total_sampah > 0 ? $posisi : $totalWarga;
        }

        return $peringkat;
    }

    /**
     * Peringkat satu warga. $daftar boleh dipakai ulang agar tidak query ulang.
     *
     * @param  array<int, int>|null  $daftar
     * @param  float|null            $totalSampah  hasil hitungan sebelumnya, biar tidak query dua kali
     */
    public function peringkat(User $user, ?array $daftar = null, ?float $totalSampah = null): int
    {
        $daftar ??= $this->semuaPeringkat();
        $totalSampah ??= $this->totalSampah($user);

        if ($totalSampah <= 0) {
            return count($daftar);
        }

        return $daftar[$user->id] ?? count($daftar);
    }

    /**
     * Tren naik/turun peringkat.
     *
     * Aturan:
     * 1. Warga yang belum menyetorkan sampah terverifikasi selalu berada di
     *    peringkat akhir, dan tren-nya dianggap TETAP. Dengan begitu warga yang
     *    baru ditambahkan atau tidak aktif tidak pernah memunculkan tren
     *    naik/turun palsu.
     * 2. Warga yang sudah menyetorkan memakai snapshot `peringkat_terakhir`
     *    (peringkat sebelum setoran terakhirnya diterima) sebagai pembanding.
     * 3. Snapshot yang tidak valid (kosong, nol, atau melebihi total warga
     *    karena ada warga yang dihapus) dianggap tidak ada, jadi tren TETAP
     *    alih-alih melompat tidak masuk akal.
     *
     * @param  array<int, int>|null  $daftar
     * @return array{direction: string, value: int, peringkat_now: int, peringkat_prev: int}
     */
    public function tren(User $user, ?array $daftar = null): array
    {
        $daftar ??= $this->semuaPeringkat();

        $totalWarga = count($daftar);
        $totalSampah = $this->totalSampah($user);
        $sekarang = $this->peringkat($user, $daftar, $totalSampah);

        if ($totalSampah <= 0) {
            return [
                'direction' => 'same',
                'value' => 0,
                'peringkat_now' => $totalWarga,
                'peringkat_prev' => $totalWarga,
            ];
        }

        $terakhir = (int) ($user->peringkat_terakhir ?? 0);

        if ($terakhir < 1 || $terakhir > $totalWarga) {
            return [
                'direction' => 'same',
                'value' => 0,
                'peringkat_now' => $sekarang,
                'peringkat_prev' => $sekarang,
            ];
        }

        $selisih = $terakhir - $sekarang;

        return [
            'direction' => $selisih > 0 ? 'up' : ($selisih < 0 ? 'down' : 'same'),
            'value' => abs($selisih),
            'peringkat_now' => $sekarang,
            'peringkat_prev' => $terakhir,
        ];
    }

    /**
     * Kunci peringkat saat ini ke kolom `peringkat_terakhir`.
     *
     * Panggil SEBELUM status setoran berubah menjadi "diterima" supaya selisih
     * di dashboard bisa dihitung. Menukar hadiah TIDAK perlu memanggil ini:
     * poin turun tapi total sampah tidak berubah, jadi peringkat pun tidak
     * berubah.
     *
     * Dipanggil sekali untuk semua warga, bukan per warga, supaya hemat query.
     */
    public function snapshot(): void
    {
        foreach ($this->semuaPeringkat() as $userId => $rank) {
            User::whereKey($userId)->update(['peringkat_terakhir' => $rank]);
        }
    }
}
