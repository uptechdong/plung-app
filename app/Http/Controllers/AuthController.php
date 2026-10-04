<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'role' => 'required|in:warga,pengurus',
        ]);

        $user = User::where('username', $credentials['username'])
            ->where('role', $credentials['role'])
            ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau kata sandi yang Anda masukkan salah',
            ], 401);
        }

        $token = $this->masukSatuPerangkat($user);

        return response()->json([
            'success' => true,
            'user' => $this->formatUser($user),
            'token' => $token,
        ]);
    }

    /**
     * Satu akun hanya boleh aktif di satu perangkat.
     *
     * Setiap login baru membatalkan token milik perangkat lain, sehingga
     * tidak ada dua perangkat yang memakai akun yang sama sekaligus. Ini
     * penting untuk aplikasi sampah: satu KK tidak boleh bisa menyetor
     * dua kali dari dua HP berbeda karena sesi ganda.
     *
     * Token yang dicabut tidak dihapus permanen dari tabel -- tokennya
     * dibuang dari sisi klien (localStorage) saat perangkat lama menutup
     * aplikasi atau menerima 401.
     */
    private function masukSatuPerangkat(User $user): string
    {
        $user->tokens()->delete();

        return $user->createToken('api-token')->plainTextToken;
    }

    public function logout(): JsonResponse
    {
        $user = Auth::user();
        if ($user) {
            $user->tokens()->delete();
        }
        return response()->json(['success' => true]);
    }

    public function me(): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false], 401);
        }
        return response()->json(['success' => true, 'user' => $this->formatUser($user)]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->validate([
            'nama' => 'required|string',
            'rumah' => 'nullable|string',
            'wa' => 'nullable|string',
            'username' => 'required|string|unique:users,username,' . $user->id,
            'password' => 'nullable|string',
            'hapus_foto' => 'nullable|boolean',
        ]);

        $user->nama = $data['nama'];
        $user->rumah = $data['rumah'] ?? null;
        $user->wa = $data['wa'] ?? null;
        $user->username = $data['username'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        if ($request->boolean('hapus_foto')) {
            // Permintaan hapus wins atas berkas baru, supaya tidak ada dua
            // operasi storage dalam satu request. Foto lama dihapus dari disk
            // hanya kalau benar-benar milik pengguna ini (bukan URL_seed
            // dari data awal).
            if ($user->foto && ! str_starts_with($user->foto, 'http') && ! str_starts_with($user->foto, 'data:')) {
                Storage::disk('foto')->delete($user->foto);
            }
            $user->foto = null;
        } elseif ($request->hasFile('foto')) {
            $request->validate([
                'foto' => 'image|max:3072',
            ]);
            if ($user->foto && !str_starts_with($user->foto, 'http') && !str_starts_with($user->foto, 'data:')) {
                Storage::disk('foto')->delete($user->foto);
            }
            $user->foto = $request->file('foto')->store('profil', 'foto');
        } elseif ($request->filled('foto') && str_starts_with($request->input('foto'), 'data:image')) {
            $imageParts = explode(';base64,', $request->input('foto'));
            if (count($imageParts) === 2) {
                $ext = 'png';
                if (preg_match('/data:image\/(.+);base64/', $request->input('foto'), $matches)) {
                    $ext = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
                }
                $decoded = base64_decode($imageParts[1]);

                if (strlen($decoded) > 3 * 1024 * 1024) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Foto profil terlalu besar. Maksimal 3 MB.',
                    ], 422);
                }

                $fileName = 'profil/' . uniqid() . '.' . $ext;
                Storage::disk('foto')->put($fileName, $decoded);
                if ($user->foto && !str_starts_with($user->foto, 'http') && !str_starts_with($user->foto, 'data:')) {
                    Storage::disk('foto')->delete($user->foto);
                }
                $user->foto = $fileName;
            }
        }

        $user->save();

        return response()->json(['success' => true, 'user' => $this->formatUser($user)]);
    }

    private function formatUser(User $user): array
    {
        $fotoUrl = null;
        if ($user->foto) {
            if (str_starts_with($user->foto, 'http') || str_starts_with($user->foto, 'data:')) {
                $fotoUrl = $user->foto;
            } else {
                $fotoUrl = Storage::disk('foto')->url($user->foto);
            }
        }

        return [
            'id' => $user->id,
            'nama' => $user->nama,
            'rumah' => $user->rumah,
            'wa' => $user->wa,
            'username' => $user->username,
            'poin' => (int) $user->poin,
            'foto' => $fotoUrl,
            'role' => $user->role,
        ];
    }
}
