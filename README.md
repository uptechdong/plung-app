<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

## Menjalankan di Jaringan Lokal (LAN)

Aplikasi berjalan lewat **XAMPP Apache** pada port **8000** dan sudah dikonfigurasi
mendengarkan di semua antarmuka jaringan (`0.0.0.0`), sehingga bisa dibuka dari
HP/tablet/laptop lain yang terhubung ke Wi-Fi yang sama.

### Alamat akses

| Dari perangkat | URL |
| --- | --- |
| PC ini | `http://localhost:8000` |
| Perangkat lain di jaringan | `http://192.168.1.185:8000` |

> Semua panggilan API di frontend memakai URL relatif, jadi tidak perlu
> konfigurasi tambahan di browser perangkat lain.

### Menjalankan

Apache **harus** sudah berjalan sebelum membuka aplikasinya:

```powershell
# Start Apache (hanya perlu sekali; skip kalau XAMPP sudah jalan)
Stop-Process -Name httpd -Force -ErrorAction SilentlyContinue
Start-Process -FilePath "C:\xampp\apache\bin\httpd.exe" -WorkingDirectory "C:\xampp\apache\bin" -WindowStyle Hidden
```

MySQL juga harus aktif (dijalankan lewat XAMPP).

Aset statis memakai hasil build di `public/build`. Kalau ingin mengubah
front-end, build ulang:

```powershell
npm run build
```

### Mode pengembangan dengan hot reload (opsional)

`vite.config.js` otomatis mendeteksi IP LAN dan menuliskan URL yang benar ke
`public/hot`, sehingga HMR bisa dipakai dari perangkat lain:

```powershell
npm run dev
```

Server Vite lalu listen di `0.0.0.0:5173`. Kalau IP PC berubah (DHCP), takeover
otomatis; untuk memaksa IP tertentu:

```powershell
$env:VITE_LAN_HOST = "192.168.1.185"; npm run dev
```

Hapus `public/hot` setelah berhenti memakai mode dev agar kembali ke aset
`public/build`.

### Catatan penting

- **Windows Firewall**: profil jaringan PC ini berstatus *Public* dan
  **Firewall sedang nonaktif**, jadi tidak ada rule yang perlu ditambahkan.
  Jika nanti Firewall diaktifkan, buka port 8000 (dan 5173 untuk Vite) lewat
  PowerShell sebagai Administrator:

  ```powershell
  New-NetFirewallRule -DisplayName "PLUNG Laravel 8000" -Direction Inbound `
      -LocalPort 8000 -Protocol TCP -Action Allow -Profile Any
  ```

- **Pastikan kedua perangkat di Wi-Fi/subnet yang sama** dan tidak ada
  *AP isolation* (client isolation) yang aktif di router.
- **Keamanan**: `APP_DEBUG=true` dan `SESSION_DRIVER=database`. Jangan
  ekspos aplikasi ini ke internet publik — gunakan hanya untuk jaringan lokal.

### Konfigurasi yang relevan

| File | Perubahan |
| --- | --- |
| `.env` | `APP_URL=http://192.168.1.185:8000` |
| `C:\xampp\apache\conf\httpd.conf` | `Listen 8000` |
| `C:\xampp\apache\conf\extra\httpd-vhosts.conf` | vhost `*:8000` → `plung-app/public` |
| `vite.config.js` | `host: 0.0.0.0` + HMR host otomatis dari IP LAN |

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
