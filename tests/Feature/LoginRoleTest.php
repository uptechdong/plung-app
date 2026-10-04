<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Login WAJIB memverifikasi role, bukan cuma username + kata sandi.
 *
 * Ini memblokir akun warga yang mencoba masuk lewat tab "Pengurus RT"
 * (dan sebaliknya). Tanpa itu, tab login hanya hiasan.
 *
 * Catatan: pakai DatabaseTransactions, BUKAN RefreshDatabase. Migrasi proyek
 * ini memakai ALTER TABLE ... MODIFY COLUMN (khusus MySQL) sehingga tidak
 * bisa jalan di sqlite in-memory milik RefreshDatabase. Semua perubahan test
 * dibungkus transaksi dan di-rollback, jadi database dev tetap utuh.
 */
class LoginRoleTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * phpunit.xml memaksa DB_CONNECTION=sqlite dan DB_DATABASE=:memory:,
     * tapi migrasi proyek ini hanya jalan di MySQL. Kita baca nilai
     * MySQL langsung dari berkas .env lalu override keduanya.
     *
     * Aman karena DatabaseTransactions membungkus semua query test dalam satu
     * transaksi yang di-rollback di akhir, jadi database dev tidak tersentuh.
     */
    protected function setUp(): void
    {
        foreach ($this->koneksiDariDotEnv() as $key => $value) {
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
            putenv($key . '=' . $value);
        }

        parent::setUp();
    }

    /**
     * Ambil DB_* dari .env tanpa lewat env(), karena env() sudah dikunci
     * oleh nilai phpunit.xml.
     *
     * @return array<string, string>
     */
    private function koneksiDariDotEnv(): array
    {
        $file = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.env';
        $ambil = static function (string $kunci) use ($file): ?string {
            if (! is_readable($file)) {
                return null;
            }
            foreach (file($file, FILE_IGNORE_NEW_LINES) as $baris) {
                $baris = trim($baris);
                if ($baris === '' || str_starts_with($baris, '#')) {
                    continue;
                }
                [$k, $v] = array_pad(explode('=', $baris, 2), 2, '');
                if (trim($k) === $kunci) {
                    return trim(trim($v), "\"'");
                }
            }
            return null;
        };

        $hasil = ['DB_CONNECTION' => 'mysql'];
        foreach (['DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'] as $k) {
            $v = $ambil($k);
            if ($v !== null) {
                $hasil[$k] = $v;
            }
        }

        return $hasil;
    }

    private function buat(string $role): User
    {
        return User::create([
            'nama'     => 'Uji ' . $role,
            'username' => $role . '_uji',
            'password' => 'rahasia123',
            'role'     => $role,
            'poin'     => 0,
        ]);
    }

    private function login(string $username, string $role)
    {
        return $this->postJson('/api/login', [
            'username' => $username,
            'password' => 'rahasia123',
            'role'     => $role,
        ]);
    }

    public function test_warga_ditolak_saat_membuka_tab_pengurus(): void
    {
        $this->buat('warga');

        $res = $this->login('warga_uji', 'pengurus');

        $res->assertStatus(401);
        $this->assertSame(
            'Username atau kata sandi yang Anda masukkan salah',
            $res->json('message')
        );
    }

    public function test_pengurus_ditolak_saat_membuka_tab_warga(): void
    {
        $this->buat('pengurus');

        $this->login('pengurus_uji', 'warga')->assertStatus(401);
    }

    public function test_role_sesuai_masih_berhasil_login(): void
    {
        $this->buat('warga');
        $this->buat('pengurus');

        $resWarga = $this->login('warga_uji', 'warga');
        $resWarga->assertOk();
        $resWarga->assertJsonPath('success', true);
        $resWarga->assertJsonPath('user.role', 'warga');
        $this->assertNotEmpty($resWarga->json('token'));

        $resPengurus = $this->login('pengurus_uji', 'pengurus');
        $resPengurus->assertOk();
        $resPengurus->assertJsonPath('user.role', 'pengurus');
        $this->assertNotEmpty($resPengurus->json('token'));
    }

    public function test_kata_sandi_salah_tetap_ditolak(): void
    {
        $this->buat('warga');

        $res = $this->postJson('/api/login', [
            'username' => 'warga_uji',
            'password' => 'salah',
            'role'     => 'warga',
        ]);

        $res->assertStatus(401);
    }

    public function test_role_wajib_dikirim(): void
    {
        $this->buat('warga');

        $this->postJson('/api/login', [
            'username' => 'warga_uji',
            'password' => 'rahasia123',
        ])->assertStatus(422)->assertJsonValidationErrors('role');
    }

    public function test_role_tidak_dikenal_ditolak(): void
    {
        $this->buat('warga');

        $this->login('warga_uji', 'admin')->assertStatus(422);
    }
}
