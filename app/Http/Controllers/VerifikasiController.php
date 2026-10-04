<?php

namespace App\Http\Controllers;

use App\Models\Penukaran;
use App\Models\RiwayatAktivitas;
use App\Models\Setoran;
use App\Services\PeringkatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VerifikasiController extends Controller
{
    public function __construct(private readonly PeringkatService $peringkat)
    {
    }

    public function setoranPending(): JsonResponse
    {
        $setoran = Setoran::with(['user', 'kategori'])
            ->where('status', 'diproses')
            ->terbaru()
            ->get()
            ->map(fn ($s) => [
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
            ]);

        return response()->json(['success' => true, 'data' => $setoran]);
    }

    public function setoranVerifikasi(Request $request, Setoran $setoran): JsonResponse
    {
        $data = $request->validate([
            'status' => 'required|in:diterima,ditolak',
        ]);

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

        return response()->json(['success' => true]);
    }

    public function kupon(): JsonResponse
    {
        $kupon = Penukaran::with(['user', 'reward'])
            ->orderByDesc('tgl')
            ->get()
            ->map(fn ($k) => [
                'id' => $k->id,
                'kode_kupon' => $k->kode_kupon,
                'nama' => $k->user ? $k->user->nama : '-',
                'rumah' => $k->user ? $k->user->rumah : '-',
                'barang' => $k->reward ? $k->reward->nama : '-',
                'tgl' => $k->tgl ? $k->tgl->locale('id')->translatedFormat('d M Y') : '-',
                'diambil' => (bool) $k->diambil,
            ]);

        return response()->json(['success' => true, 'data' => $kupon]);
    }

    public function kuponToggle(Request $request, Penukaran $penukaran): JsonResponse
    {
        $data = $request->validate([
            'diambil' => 'required|boolean',
        ]);
        $penukaran->update(['diambil' => $data['diambil']]);

        RiwayatAktivitas::where('penukaran_id', $penukaran->id)
            ->update(['status' => $data['diambil'] ? 'sudah_ditukar' : 'belum_ditukar']);

        return response()->json(['success' => true, 'diambil' => $penukaran->diambil]);
    }
}
