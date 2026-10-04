<?php

namespace App\Http\Controllers;

use App\Models\Penukaran;
use App\Models\RiwayatAktivitas;
use App\Models\Setoran;
use App\Models\User;
use App\Services\PeringkatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class WargaController extends Controller
{
    public function __construct(private readonly PeringkatService $peringkat)
    {
    }

    /**
     * Daftar warga.
     *
     * Selain data dasar, ini mengirim `total_sampah` (Kg dari setoran
     * terverifikasi) dan `peringkat`. Keduanya dipakai card "Warga Aktif Pilah
     * Sampah" supaya urutannya sama persis dengan dashboard warga dan tidak
     * bergantung pada poin yang berubah-ubah karena penukaran.
     */
    public function index(): JsonResponse
    {
        $daftarPeringkat = $this->peringkat->semuaPeringkat();

        $warga = User::query()
            ->where('role', 'warga')
            ->orderBy('id')
            ->select('id', 'nama', 'rumah', 'wa', 'username', 'poin')
            ->selectSub(
                Setoran::query()
                    ->selectRaw('COALESCE(SUM(berat), 0)')
                    ->whereColumn('setoran.user_id', 'users.id')
                    ->where('setoran.status', 'diterima'),
                'total_sampah'
            )
            ->get()
            ->map(fn ($w) => [
                'id' => $w->id,
                'nama' => $w->nama,
                'rumah' => $w->rumah,
                'wa' => $w->wa,
                'username' => $w->username,
                'poin' => $w->poin,
                'total_sampah' => (float) $w->total_sampah,
                'peringkat' => $daftarPeringkat[$w->id] ?? null,
            ]);

        return response()->json(['success' => true, 'data' => $warga]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama' => 'required|string',
            'rumah' => 'required|string',
            'wa' => 'required|string',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string',
        ]);

        $warga = User::create([
            'nama' => $data['nama'],
            'rumah' => $data['rumah'],
            'wa' => $data['wa'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'poin' => 0,
            'role' => 'warga',
        ]);

        return response()->json(['success' => true, 'data' => $warga]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'nama' => 'required|string',
            'rumah' => 'required|string',
            'wa' => 'required|string',
            'username' => 'required|string|unique:users,username,' . $user->id,
            'password' => 'nullable|string',
        ]);

        $user->fill([
            'nama' => $data['nama'],
            'rumah' => $data['rumah'],
            'wa' => $data['wa'],
            'username' => $data['username'],
        ]);

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return response()->json(['success' => true, 'data' => $user]);
    }

    public function destroy(User $user): JsonResponse
    {
        if ($user->role !== 'warga') {
            return response()->json(['success' => false, 'message' => 'Tidak dapat menghapus pengurus'], 403);
        }

        $jumlah = [
            'riwayat' => RiwayatAktivitas::where('user_id', $user->id)->delete(),
            'penukaran' => Penukaran::where('user_id', $user->id)->delete(),
            'setoran' => Setoran::where('user_id', $user->id)->delete(),
        ];

        $user->delete();

        return response()->json([
            'success' => true,
            'data' => ['transaksi_dihapus' => $jumlah],
        ]);
    }
}
