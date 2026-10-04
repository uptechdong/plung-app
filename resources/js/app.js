// Entry point JavaScript untuk Vite.
//
// File ini sengaja TIDAK berisi apa-apa. Seluruh logika aplikasi PLUNG! ada
// di dalam <script> inline pada resources/views/app.blade.php dan
// head.blade.php -- bukan di berkas terpisah. head.blade.php juga hanya
// memanggil `@vite(['resources/css/app.css'])`, tanpa JS.
//
// Sebelumnya file ini meng-import './bootstrap' yang memasang axios ke
// window.axios, padahal aplikasi memakai fetch() lewat fungsi apiFetch() dan
// tidak pernah memanggil axios sama sekali. Akibatnya axios tetap ikut
// ter-bundle ke public/build walaupun tidak pernah dimuat browser.
//
// Berkas ini sengaja dibiarkan ada (kosong) karena laravel-vite-plugin
// tetap memperlakukannya sebagai entry point saat build.