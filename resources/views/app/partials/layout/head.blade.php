<!DOCTYPE html>
<html lang="id" class="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  {{-- Halaman aplikasi tidak boleh di-cache: data warga & sesi login harus selalu segar. --}}
  <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <title>PLUNG! - Sistem Pemilahan Sampah Mandiri</title>

  {{-- ===================================================================
       SAMAKAN ALAMAT AKSES APLIKASI
       -------------------------------------------------------------------
       Browser menganggap http://localhost:8000 dan http://127.0.0.1:8000
       sebagai DUA origin yang berbeda. Semua yang disimpan di localStorage
       (termasuk token login "plung_token") tidak ikut antar origin, jadi
       membuka aplikasi lewat alamat berbeda bisa membuat: sudah login di
       satu alamat tapi dianggap belum login di alamat lain, dan data yang
       tampil jadi berbeda-beda.

       Karena itu semua akses lokal diarahkan ke SATU alamat. Setelah ini
       hasilnya selalu sama, apa pun alamat yang diketik di browser.
       (perlu satu kali login ulang saat pertama memakai alamat baru)
  ==================================================================== --}}
  <script>
    (function () {
      var HOST_KANONIK = 'localhost';
      var host = location.hostname;

      if (host === HOST_KANONIK) return;
      if (host !== '127.0.0.1' && host !== '0.0.0.0' && host !== '::1' && host !== '[::1]') return;

      var port = location.port ? (':' + location.port) : '';
      var tujuan = location.protocol + '//' + HOST_KANONIK + port + location.pathname + location.search + location.hash;

      window.__alamatKanonik = { dari: host + port, ke: HOST_KANONIK + port };

      location.replace(tujuan);
    })();
  </script>

  <!-- Favicon: letakkan gambar favicon di public/favicon.png -->
  <link rel="icon" type="image/png" href="/favicon.png">
  
  <!-- Tailwind CSS via Vite -->
  @vite(['resources/css/app.css'])
  
  <!-- Google Fonts & Lucide Icons

       Font, lucide, dan chart.js dimuat lewat CDN. Semuanya diberi defer /
       media="print" supaya TIDAK memblokir parsing HTML: browser lanjut
       menggambar halaman selagi file ini diunduh di belakang. Tanpa itu,
       HTML app (189 KB) baru bisa digambar setelah ~2 detik menunggu CDN.

       Font pakai pola media="print" -> onload="this.media='all'", teknik
       standar untuk stylesheet yang boleh datang belakangan. Teks sempat
       tampil dengan font cadangan sebentar (FOUT), jauh lebih baik daripada
       layar putih kosong sampai CDN selesai. -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  {{-- Poppins untuk seluruh aplikasi, termasuk logo "PLUNG!" di sidebar dan
       di layar login.
       Weight 400-700. Poppins secara bawaan terasa lebih tebal daripada font
       sans biasa, jadi teks dasar dibiarkan di 400 (tipis), dan hanya teks
       yang memang butuh penekanan yang naik ke 600 atau 700.
       Karena logo "PLUNG!" memakai font-black, ia otomatis menjadi bagian
       paling tebal di layar (700), sementara teks lain lebih ringan.
       Plus Jakarta Sans tidak dimuat karena tidak ada elemen yang memakainya. --}}
  <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" media="print" onload="this.media='all'">
  <script src="https://unpkg.com/lucide@latest" defer></script>

  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>

  <style>
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #10b981; border-radius: 99px; }
    .dark ::-webkit-scrollbar-thumb { background: #047857; }
    
    .active-press { transition: transform 0.15s ease; }
    .active-press:active { transform: scale(0.97); }
    
    .smooth-zoom {
      transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s ease;
    }
    .smooth-zoom:hover {
      transform: scale(1.02);
    }
  </style>
</head>
<body class="bg-slate-50/80 dark:bg-brand-950 text-slate-800 dark:text-emerald-100 font-sans antialiased min-h-screen flex flex-col transition-colors duration-300 text-sm">

