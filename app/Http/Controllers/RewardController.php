<?php

namespace App\Http\Controllers;

use App\Models\Penukaran;
use App\Models\Reward;
use App\Models\RiwayatAktivitas;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RewardController extends Controller
{
    public function index(): JsonResponse
    {
        $rewards = Reward::orderBy('poin')->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'nama' => $r->nama,
                'poin' => $r->poin,
                'stok' => $r->stok,
                'ket' => $r->ket,
            ]);

        return response()->json(['success' => true, 'data' => $rewards]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama' => 'required|string',
            'poin' => 'required|integer|min:0',
            'stok' => 'required|integer|min:0',
            'ket' => 'required|string',
        ]);

        $reward = Reward::create($data);
        return response()->json(['success' => true, 'data' => $reward]);
    }

    public function update(Request $request, Reward $reward): JsonResponse
    {
        $data = $request->validate([
            'nama' => 'required|string',
            'poin' => 'required|integer|min:0',
            'stok' => 'required|integer|min:0',
            'ket' => 'required|string',
        ]);

        $reward->update($data);
        return response()->json(['success' => true, 'data' => $reward]);
    }

    public function destroy(Reward $reward): JsonResponse
    {
        // Menghapus hadiah berarti membatalkan semua penukaran barang ini:
        // kuponnya dihapus, riwayatnya ikut terhapus (cascade), dan poin yang
        // sudah dipotong dikembalikan ke warga. Tanpa ini, kupon jadi yatim,
        // tetap muncul di Riwayat warga padahal barangnya sudah tidak ada, dan
        // saldo poinnya tidak pernah kembali.
        $kupon = Penukaran::where('reward_id', $reward->id)->get();

        $poinDikembalikan = 0;
        foreach ($kupon as $p) {
            $poin = (int) $p->poin;

            // Cadangan untuk penukaran lama yang belum punya kolom poin.
            if ($poin <= 0) {
                $dariRiwayat = RiwayatAktivitas::where('penukaran_id', $p->id)->value('poin');
                $poin = abs((int) $dariRiwayat);
            }

            if ($poin > 0) {
                User::whereKey($p->user_id)->increment('poin', $poin);
                $poinDikembalikan += $poin;
            }
        }

        $reward->delete();

        return response()->json([
            'success' => true,
            'data' => [
                'kupon_terhapus' => $kupon->count(),
                'poin_dikembalikan' => $poinDikembalikan,
            ],
        ]);
    }

    public function tukar(Reward $reward): JsonResponse
    {
        $user = Auth::user();

        if ($reward->stok <= 0) {
            return response()->json(['success' => false, 'message' => 'Maaf, stok hadiah ini telah habis.'], 400);
        }

        if ($user->poin < $reward->poin) {
            return response()->json(['success' => false, 'message' => 'Poin Anda belum mencukupi.'], 400);
        }

        // Generate kode kupon yang unik
        do {
            $kode = 'KP-' . random_int(100, 999);
        } while (Penukaran::where('kode_kupon', $kode)->exists());

        $penukaran = Penukaran::create([
            'kode_kupon' => $kode,
            'user_id' => $user->id,
            'reward_id' => $reward->id,
            // Dicatat supaya kalau hadiah ini nanti dihapus, jumlah poin yang
            // harus dikembalikan tahu dengan tepat.
            'poin' => $reward->poin,
            'tgl' => now(),
            'diambil' => false,
        ]);

        $reward->decrement('stok');

        // Peringkat TIDAK dikunci di sini. Peringkat dihitung dari total
        // sampah terverifikasi, sedangkan menukar hadiah hanya mengurangi
        // poin dan stok. Kalau peringkat ikut dikunci saat tukar, naik-turun
        // peringkat akan ikut bergerak karena penukaran -- padahal yang
        // seharusnya menentukan peringkat adalah sampah yang disetorkan.
        $user->decrement('poin', $reward->poin);

        RiwayatAktivitas::create([
            'user_id' => $user->id,
            'jenis' => 'penukaran_hadiah',
            'rincian' => $reward->nama,
            'status' => 'belum_ditukar',
            'poin' => -$reward->poin,
            'penukaran_id' => $penukaran->id,
        ]);

        return response()->json([
            'success' => true,
            'kode_kupon' => $kode,
            'message' => "Kupon {$reward->nama} [{$kode}] telah terbit. Silakan ambil di Balai RT.",
        ]);
    }
}
