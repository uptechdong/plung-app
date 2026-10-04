<?php

namespace Database\Seeders;

use App\Models\KategoriSampah;
use App\Models\Penukaran;
use App\Models\Reward;
use App\Models\RiwayatAktivitas;
use App\Models\Setoran;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $pengurus = User::create([
            'nama' => 'Pak Joko (Ketua RT 36)',
            'rumah' => 'RT 36 / RW 09',
            'wa' => '081122334455',
            'username' => 'rt36',
            'password' => Hash::make('admin'),
            'poin' => 0,
            'foto' => null,
            'role' => 'pengurus',
        ]);

        $siti = User::create([
            'nama' => 'Ibu Siti Aminah',
            'rumah' => 'No. 08',
            'wa' => '081234567890',
            'username' => 'siti_aminah',
            'password' => Hash::make('123'),
            'poin' => 320,
            'foto' => null,
            'role' => 'warga',
        ]);

        $ahmad = User::create([
            'nama' => 'Pak Ahmad Subagjo',
            'rumah' => 'No. 12',
            'wa' => '085678901234',
            'username' => 'ahmad_s',
            'password' => Hash::make('123'),
            'poin' => 265,
            'foto' => null,
            'role' => 'warga',
        ]);

        $budi = User::create([
            'nama' => 'Pak Budi',
            'rumah' => 'No. 03',
            'wa' => '081298765432',
            'username' => 'budi_rt36',
            'password' => Hash::make('123'),
            'poin' => 60,
            'foto' => null,
            'role' => 'warga',
        ]);

        $organik = KategoriSampah::create([
            'nama' => 'Organik (Dapur/Sisa Makanan)',
            'deskripsi' => 'Sampah mudah membusuk untuk bahan kompos.',
            'warna' => '#10b981',
            'poin_per_unit' => 10,
            'satuan' => 'Kg',
        ]);

        $anorganik = KategoriSampah::create([
            'nama' => 'Anorganik (Plastik/Kertas/Botol)',
            'deskripsi' => 'Limbah daur ulang bernilai ekonomi.',
            'warna' => '#f59e0b',
            'poin_per_unit' => 15,
            'satuan' => 'Kg',
        ]);

        $b3 = KategoriSampah::create([
            'nama' => 'B3 (Baterai/Lampu/Obat)',
            'deskripsi' => 'Limbah berbahaya dan beracun.',
            'warna' => '#f43f5e',
            'poin_per_unit' => 25,
            'satuan' => 'Unit',
        ]);

        $residu = KategoriSampah::create([
            'nama' => 'Residu (Pembalut/Popok/Tisu)',
            'deskripsi' => 'Limbah kotor non-daur ulang.',
            'warna' => '#64748b',
            'poin_per_unit' => 5,
            'satuan' => 'Kg',
        ]);

        Reward::create(['nama' => 'Minyak Goreng Pouch 1L', 'poin' => 75, 'stok' => 2, 'ket' => 'Kebutuhan Dapur Utama']);
        Reward::create(['nama' => 'Gula Pasir Kristal 1 Kg', 'poin' => 50, 'stok' => 8, 'ket' => 'Manis Alami Siap Pakai']);
        Reward::create(['nama' => 'Beras Medium Super 2.5 Kg', 'poin' => 120, 'stok' => 3, 'ket' => 'Kualitas Pulen Bersih']);
        Reward::create(['nama' => 'Sabun Cuci Piring Refill 780ml', 'poin' => 40, 'stok' => 15, 'ket' => 'Kebersihan Alat Dapur']);
        Reward::create(['nama' => 'Deterjen Bubuk Cuci 800g', 'poin' => 45, 'stok' => 1, 'ket' => 'Harum & Ampuh Noda']);
        Reward::create(['nama' => 'Teh Celup & Kopi Dus Paket', 'poin' => 30, 'stok' => 12, 'ket' => 'Pendamping Santai Warga']);

        Reward::create(['nama' => 'Telur Ayam Negeri 1 Kg', 'poin' => 55, 'stok' => 15, 'ket' => 'Bahan Pokok Kedua Keluarga']);
        Reward::create(['nama' => 'Air Mineral 600 ml (1 Dus)', 'poin' => 25, 'stok' => 20, 'ket' => 'Kebutuhan Minum Warga']);
        Reward::create(['nama' => 'Gas LPG 3 Kg', 'poin' => 75, 'stok' => 10, 'ket' => 'Gas Masak Harian']);
        Reward::create(['nama' => 'Kecap Manis 520 ml', 'poin' => 20, 'stok' => 18, 'ket' => 'Bumbu Masak Sehari-hari']);
        Reward::create(['nama' => 'Tisu SNI 250 Sheets', 'poin' => 20, 'stok' => 25, 'ket' => 'Kebutuhan Kebersihan Rumah']);
        Reward::create(['nama' => 'Sabun Mandi Cair 450 ml', 'poin' => 35, 'stok' => 12, 'ket' => 'Perawatan Tubuh Warga']);

        Setoran::create([
            'user_id' => $siti->id,
            'kategori_id' => $anorganik->id,
            'berat' => 2.0,
            'satuan' => 'Kg',
            'tgl' => '2026-09-24',
            'catatan' => 'Ditaruh dalam kantong plastik bening di teras depan rumah.',
            'foto' => 'https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?w=600&auto=format&fit=crop&q=80',
            'status' => 'pending',
            'poin' => 30,
        ]);

        Setoran::create([
            'user_id' => $ahmad->id,
            'kategori_id' => $organik->id,
            'berat' => 1.5,
            'satuan' => 'Kg',
            'tgl' => '2026-09-24',
            'catatan' => 'Sampah dapur sudah ditiriskan hingga kering.',
            'foto' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=600&auto=format&fit=crop&q=80',
            'status' => 'pending',
            'poin' => 15,
        ]);

        Setoran::create([
            'user_id' => $budi->id,
            'kategori_id' => $anorganik->id,
            'berat' => 3.0,
            'satuan' => 'Kg',
            'tgl' => '2026-09-22',
            'catatan' => 'Kardus dan kertas bekas.',
            'foto' => null,
            'status' => 'disetujui',
            'poin' => 45,
        ]);

        Setoran::create([
            'user_id' => $siti->id,
            'kategori_id' => $b3->id,
            'berat' => 200,
            'satuan' => 'Gram',
            'tgl' => '2026-09-20',
            'catatan' => 'Baterai bekas remote dan jam.',
            'foto' => null,
            'status' => 'disetujui',
            'poin' => 25,
        ]);

        Setoran::create([
            'user_id' => $ahmad->id,
            'kategori_id' => $residu->id,
            'berat' => 5.0,
            'satuan' => 'Kg',
            'tgl' => '2026-09-19',
            'catatan' => 'Sampah residu mingguan.',
            'foto' => null,
            'status' => 'disetujui',
            'poin' => 25,
        ]);

        $minyak = Reward::where('nama', 'Minyak Goreng Pouch 1L')->first();
        $beras = Reward::where('nama', 'Beras Medium Super 2.5 Kg')->first();

        Penukaran::create([
            'kode_kupon' => 'KP-902',
            'user_id' => $budi->id,
            'reward_id' => $minyak->id,
            'tgl' => '2026-09-20',
            'diambil' => false,
        ]);

        Penukaran::create([
            'kode_kupon' => 'KP-881',
            'user_id' => $siti->id,
            'reward_id' => $beras->id,
            'tgl' => '2026-09-18',
            'diambil' => true,
        ]);

        RiwayatAktivitas::create([
            'user_id' => $budi->id,
            'jenis' => 'setor_sampah',
            'rincian' => '2 Kg Plastik Daur Ulang',
            'status' => 'diproses',
            'poin' => 15,
        ]);

        RiwayatAktivitas::create([
            'user_id' => $budi->id,
            'jenis' => 'penukaran_hadiah',
            'rincian' => 'Minyak Goreng 1 Liter',
            'status' => 'belum_diambil',
            'poin' => -75,
        ]);

        RiwayatAktivitas::create([
            'user_id' => $budi->id,
            'jenis' => 'setor_sampah',
            'rincian' => '3.5 Kg Organik Makanan',
            'status' => 'disetujui',
            'poin' => 35,
        ]);
    }
}
