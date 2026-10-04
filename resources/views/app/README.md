# Struktur `app/partials/`

Pecahan dari `resources/views/app.blade.php` (semula 3.092 baris dalam 1 file).
Tujuan: supaya tiap bagian bisa dibaca, dicari, dan dirawat sendiri tanpa
menggulir satu file raksasa.

## Peta file

### `layout/` — kerangka halaman

| File | Isi |
|---|---|
| `head.blade.php` | `<head>`: meta anti-cache, pengarah alamat kanonik, favicon, Vite, Google Fonts, Lucide, Chart.js, `<style>` |
| `sidebar-warga.blade.php` | Menu **Panel Warga** (`#nav-group-warga`) |
| `sidebar-pengurus.blade.php` | Menu **Panel Pengurus RT** (`#nav-group-pengurus`) |
| `topbar.blade.php` | Bar atas: pencarian global, tanggal, tombol mode gelap, dropdown profil |

> Footer aplikasi tidak punya file sendiri. Markup-nya ada inline di
> `resources/views/app.blade.php`, tepat sebelum penutup `</main>`.

### `modal/` — jendela dialog

| File | Isi |
|---|---|
| `modal-login.blade.php` | Kartu login + tab Warga/Pengurus |
| `modal-notifikasi.blade.php` | Modal umum (sukses / gagal / konfirmasi) |
| `modal-detail-setoran.blade.php` | Detail setoran + tombol Terima / Tolak |
| `modal-warga.blade.php` | Form tambah & edit warga |

### `halaman/` — 12 halaman aplikasi

| File | `id` section | Menu |
|---|---|---|
| `dashboard-warga.blade.php` | `page-dashboard-warga` | Dashboard (warga) |
| `dashboard-rt.blade.php` | `page-dashboard-rt` | Dashboard (petugas) |
| `setor.blade.php` | `page-setor` | Setor Sampah |
| `panduan.blade.php` | `page-panduan` | Panduan Pilah |
| `tukar.blade.php` | `page-tukar` | Tukar Poin |
| `riwayat.blade.php` | `page-riwayat` | Riwayat Aktivitas |
| `edit-profil.blade.php` | `page-edit-profil` | Edit Profil |
| `daftar-warga.blade.php` | `page-daftar-warga` | Kelola Warga |
| `verifikasi.blade.php` | `page-verifikasi` | Verifikasi Setoran |
| `verifikasi-kupon.blade.php` | `page-verifikasi-kupon` | Verifikasi Kupon |
| `kelola-reward.blade.php` | `page-kelola-reward` | Edit Katalog Hadiah |
| `laporan.blade.php` | `page-laporan` | Cetak Laporan |

## Aturan yang perlu dijaga

1. **Satu halaman = satu file.** Kalau menambah halaman baru, buat file di
   `halaman/` lalu tambahkan satu baris `@include` di `app.blade.php`.

2. **Nama `id` section harus sama dengan nama file** (tanpa awalan `page-`).
   `navigate()` mencari section dengan pola `'page-' + pageId`, dan menandai
   menu aktif lewat `getElementById('nav-' + pageId)`. Kalau diubah, navigasi
   dan penandaan menu ikut gagal.

3. **Atribut `onclick` harus memanggil fungsi yang ada di blok `<script>`**
   (lihat bagian bawah `app.blade.php`). Modifikasi yang tempted menyentuh
   `onclick` atau nama fungsi harus cek dua-duanya.

4. **Halaman baru wajib ikut aturan `handleGlobalSearch()`.** Fungsi itu
   memetakan `pageId` ke fungsi render, supaya pencarian global di bar atas
   bekerja di halaman tersebut.

## Yang BELUM dipecah: JavaScript

Seluruh JavaScript masih satu blok `<script>` di bagian bawah `app.blade.php`.

**Ini disengaja.** Ada 23 fungsi yang dipanggil langsung dari atribut
`onclick` / `oninput` / `onchange` (lihat daftar di bawah), dan 20 variabel
`let` di level atas yang dipakai bersama seluruh aplikasi:

```
authToken, userPoin, currentUserSession, katalogRewards, listWarga,
listKategoriSampah, verifikasiPending, listKuponVerifikasi,
chartBulananWargaInstance, ... (dan lainnya)
```

Kalau JavaScript dipecah menjadi **ES module** (cara `@vite` biasa bekerja),
`let` dan `function` akan berhenti menjadi global, dan seluruh aplikasi akan
rusak: setiap klik menu melempar `ReferenceError`.

Kalau nanti benar-benar mau dipecah, dua jalan yang aman:

- **Paling aman:** pecah menjadi beberapa `<script>` klasik (bukan module)
  yang dimuat berurutan di `app.blade.php`. `let` di level atas tetap
  dibagikan antar file, jadi fungsi `onclick` tetap jalan.
- **Lebih bersih tapi lebih kerja:** pecah jadi ES module, lalu expose fungsi
  yang dipanggil `onclick` ke `window` secara eksplisit
  (`window.navigate = navigate`).

Keduanya butuh pengujian ulang pada seluruh halaman — bukan pekerjaan kecil.

### Daftar fungsi yang dipanggil dari HTML

```
bukaModalDetailSetoran  closeModalDetailSetoran  closeModalWarga
confirmLogout           editReward               handleGlobalSearch
hapusReward             hapusSetorFoto            hapusWarga
konfirmasiTukarPoin     navigate                 openEditProfilFromMenu
openModalEditWarga      openModalTambahWarga     previewSetorFoto
previewUploadedFoto     prosesVerifikasi         resetFormReward
setLoginTab             toggleDarkMode           toggleProfileDropdown
toggleStatusKupon       updateSatuanDanEstimasiSetor
```
