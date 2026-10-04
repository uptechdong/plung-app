{{--
  ============================================================================
  RANGKA UTAMA APLIKASI PLUNG!
  ============================================================================

  File ini hanya kerangka (rangka halaman + urutan @include). Isi tiap bagian
  dipisah ke file sendiri supaya mudah dibaca dan dirawat:

    partials/layout/    head, sidebar, topbar
    partials/modal/     4 modal (login, notifikasi, detail setoran, kelola warga)
    partials/halaman/   12 halaman aplikasi, satu file per halaman

  Footer aplikasi sengaja tetap inline di file ini (sebelum penutup </main>),
  tidak dipisah ke partial, karena isinya satu blok statis tanpa logika.

  Untuk mengubah tampilan, buka file yang relevan di folder partials --
  tidak perlu lagi menggulir satu file 3.000 baris.

  CATATAN PENTING
  --------------
  JavaScript di bawah masih menyatu dalam satu blok <script> SENGAJA.
  19 fungsi (navigate, setLoginTab, konfirmasiTukarPoin, dan lainnya) dipanggil
  langsung dari atribut onclick= di HTML, jadi WAJIB tetap berada di lingkup
  global. Memecahnya menjadi ES module akan membuatnya tidak global dan seluruh
  aplikasi ikut rusak. Karena itu HTML boleh dipecah, JS belum.
  ============================================================================
--}}
@include('app.partials.layout.head')

{{-- ======================= MODAL ======================= --}}
@include('app.partials.modal.modal-login')
@include('app.partials.modal.modal-notifikasi')
@include('app.partials.modal.modal-detail-setoran')
@include('app.partials.modal.modal-warga')

{{-- ==================== LAYOUT DASHBOARD ==================== --}}
  <!-- ================= 5. LAYOUT DASHBOARD UTAMA ================= -->
  <div id="main-app" class="max-w-[1600px] mx-auto min-h-screen flex flex-col flex-1 w-full hidden">
    
    <div class="flex-1 flex flex-col lg:flex-row">
      {{-- Latar gelap di belakang drawer. Hanya muncul di layar kecil
           (di desktop sidebar selalu terlihat, jadi tidak perlu). --}}
      <div id="sidebar-backdrop" class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden" onclick="tutupSidebar()" aria-hidden="true"></div>

      <!-- SIDEBAR NAVIGASI
           Di layar kecil (below lg) sidebar jadi drawer: keluar canvas di kiri
           dan dibuka lewat tombol titik tiga. Di layar lebar kembali seperti
           semula, menempel di samping konten. -->
      <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] -translate-x-full lg:static lg:translate-x-0 lg:z-auto lg:w-64 lg:max-w-none bg-white dark:bg-darkcard border-r border-slate-200/70 dark:border-emerald-900/60 p-5 flex flex-col justify-between shrink-0 overflow-y-auto transition-transform duration-300 ease-out">
        <div class="space-y-6">
          
          <div class="flex items-center gap-3 px-2">
            <div class="bg-brand-600 text-white p-2.5 rounded-2xl shrink-0 shadow-sm">
              <i data-lucide="leaf" class="w-6 h-6"></i>
            </div>
            <div>
              <span class="font-black text-2xl tracking-tight text-slate-900 dark:text-white block leading-tight">PLUNG!</span>
            </div>

            {{-- Tombol tutup, hanya untuk drawer di layar kecil. --}}
            <button type="button" onclick="tutupSidebar()" class="lg:hidden ml-auto p-2 -mr-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:text-emerald-200 dark:hover:bg-brand-950 transition-colors" aria-label="Tutup menu">
              <i data-lucide="x" class="w-5 h-5"></i>
            </button>
          </div>

          {{-- Menu navigasi. Satu file per kelompok agar mudah dirawat. --}}
@include('app.partials.layout.sidebar-warga')
@include('app.partials.layout.sidebar-pengurus')
        </div>
      </aside>


      <!-- KONTEN UTAMA -->
      <main class="flex-1 p-5 lg:p-8 space-y-6 overflow-y-auto flex flex-col justify-between">
        
        <div class="space-y-6">
  @include('app.partials.layout.topbar')

            {{-- ============ HALAMAN APLIKASI ============
                 12 halaman, satu file per halaman. --}}
            @include('app.partials.halaman.dashboard-warga')
            @include('app.partials.halaman.dashboard-rt')
            @include('app.partials.halaman.setor')
            @include('app.partials.halaman.panduan')
            @include('app.partials.halaman.tukar')
            @include('app.partials.halaman.riwayat')
            @include('app.partials.halaman.edit-profil')
            @include('app.partials.halaman.daftar-warga')
            @include('app.partials.halaman.verifikasi')
            @include('app.partials.halaman.verifikasi-kupon')
            @include('app.partials.halaman.kelola-reward')
            @include('app.partials.halaman.laporan')
        </div>

        <!-- FOOTER APLIKASI -->
        <footer class="pt-8 pb-3 text-center border-t border-slate-200/50 dark:border-emerald-900/40 text-[11px] font-semibold text-slate-400 dark:text-emerald-400">
          PLUNG! © 2026 Sistem Pemilahan Sampah Mandiri. All rights reserved
        </footer>

      </main>
    </div>

  </div>


  <!-- LAYAR BOOT
       Tampil sampai session check selesai. Sebelumnya halaman login langsung
       terlihat di frame pertama (auth-screen tidak punya class hidden),
       lalu ikut disembunyikan setelah /me dijawab. Akibatnya setiap refresh
       selalu kedip lewat form login walaupun token masih valid.
       Layar ini menggantikan kilat tersebut. -->
  <div id="boot-screen" class="fixed inset-0 z-[60] bg-slate-50 dark:bg-brand-950 flex flex-col items-center justify-center gap-4">
    <div class="w-10 h-10 rounded-full border-4 border-slate-200 dark:border-brand-900 border-t-brand-600 animate-spin"></div>
    <p class="text-xs font-bold text-slate-400 dark:text-emerald-400 tracking-wide">Memuat data...</p>
  </div>


  <!-- JAVASCRIPT SYSTEM ENGINE -->
  <script>
    // Panggil dalam try/catch. Kalau CDN lucide gagal dimuat, seluruh
    // script tidak boleh ikut mati karena pemanggilan ini ada di baris
    // pertama sebelum semua fungsi didefinisikan.
    function renderIkon() {
      try {
        if (window.lucide) window.lucide.createIcons();
      } catch (e) { /* ikon tidak kritis, abaikan */ }
    }
    renderIkon();

    // Script CDN memakai defer, jadi lucide belum tersedia saat baris di atas
    // berjalan. Sekali semua resource selesai dimuat (event "load"), gambar
    // ulang ikonnya supaya tidak ada area kosong berganti '<i>' mentah.
    window.addEventListener('load', renderIkon);

    // Jaring pengaman untuk layar boot (lihat #boot-screen di markup).
    //
    // Layar boot menutupi seluruh layar, jadi kalau karena satu dan lain hal
    // checkSession() tidak sempat selesai -- misalnya ada error di kode yang
    // dieksekusi sebelum IIFE di bawah -- pengguna akan terjebak menatap
    // spinner selamanya tanpa tombol logout pun, karena kerangka aplikasi
    // tidak pernah ditampilkan.
    //
    // Satu timeout ini menutup celah tersebut: apa pun yang terjadi, maksimal
    // 8 detik layar boot pasti hilang.
    setTimeout(function () {
      const boot = document.getElementById('boot-screen');
      if (boot && !boot.classList.contains('hidden')) {
        console.warn('Layar boot tidak tertutup otomatis dalam 8 detik; dipaksa ditutup.');
        tampilkanLogin();
      }
    }, 8000);

    // API Configuration
    const API_BASE_URL = '/api';
    let authToken = localStorage.getItem('plung_token') || null;
    
    let userPoin = 0; 
    let activeLoginTab = 'warga';
    let currentUserSession = null;
    let uploadedFotoDataUrl = null;
    // Promise kompresi foto profil yang sedang berjalan. Disimpan supaya
    // simpanProfil bisa menunggu selesai sebelum mengirim, supaya foto mentah
    // tidak ikut terkirim saat user menekan tombol dengan cepat.
    let uploadedFotoProses = null;

    // Hasil kompresi foto setoran, null = belum siap atau kompresi gagal.
    // Disimpan terpisah dari Promise-nya supaya handleSubmitSetoran tahu
    // berkas mana yang harus dikirim.
    let setorFotoSiap = null;
    let setorFotoProses = null;
    let previousActivePage = 'dashboard-warga';
    let currentActivePageId = 'dashboard-warga';

    let chartBulananWargaInstance = null;
    let chartProporsiWargaInstance = null;
    let chartTrenRTInstance = null;
    let chartKomposisiRTInstance = null;

    // Data arrays
    let listWarga = [];
    let katalogRewards = [];
    // False saat daftar hadiah sedang dimuat ulang dari database. dipakai
    // agar tombol Edit/Hapus tidak diklik pada data yang sudah basi.
    let rewardsLoaded = false;
    // Nomor urut permintaan daftar hadiah. Hanya respons terbaru yang boleh
    // menulis ke katalogRewards, supaya respons lama yang baru sampai tidak
    // bisa menimpa data hasil edit/hapus (race condition).
    let rewardsFetchSeq = 0;
  // Jumlah permintaan /rewards yang sedang berjalan. Dipakai supaya
  // penyegaran otomatis tidak menumpuk permintaan.
  let muatHadiahAktif = 0;
    // Nomor urut yang sama untuk daftar kupon verifikasi.
    let kuponFetchSeq = 0;
    let verifikasiPending = [];
    // Nomor urut permintaan untuk daftar warga & antrean verifikasi. Pola yang
    // sama seperti rewardsFetchSeq: kalau pengguna menekan menu berkali-kali
    // dengan cepat, hanya respons TERBARU yang boleh menulis ke state, supaya
    // respons lama yang telat datang tidak menimpa data yang lebih baru.
    let wargaFetchSeq = 0;
    let verifikasiFetchSeq = 0;
    let listKuponVerifikasi = [];
    let listKategoriSampah = [];

    // API Helper with auth token
    async function apiFetch(url, options = {}) {
      const headers = {
        'Accept': 'application/json',
        ...options.headers
      };
      
      // Only set Content-Type for non-FormData requests
      if (!(options.body instanceof FormData)) {
        headers['Content-Type'] = 'application/json';
      }
      
      if (authToken) {
        headers['Authorization'] = 'Bearer ' + authToken;
      }
      
      let response;

      try {
        response = await fetch(API_BASE_URL + url, { ...options, headers });
      } catch (err) {
        console.error('API Error:', err);
        throw new Error('Gagal terhubung ke server.');
      }

      // 401 pada endpoint /login berarti kombinasi username/password salah,
      // BUKAN sesi berakhir. Jangan logout (dan jangan tukar pesannya) di sini.
      if (response.status === 401 && authToken && url !== '/login') {
        localStorage.removeItem('plung_token');
        authToken = null;
        tampilkanLogin();
        throw new Error('Sesi berakhir');
      }

      // Respons mungkin bukan JSON (mis. halaman error server).
      let data;
      try {
        data = await response.json();
      } catch (err) {
        throw new Error('Respons server tidak valid.');
      }

      // Status HTTP gagal HARUS dilempar, kalau tidak UI akan menampilkan
      // "berhasil" padahal data tidak tersimpan di database.
      if (!response.ok) {
        const err = new Error(apiErrorMessage(data, response.status));
        err.status = response.status;
        throw err;
      }

      // Backend bisa balas 200 tapi success:false (mis. stok habis).
      if (data && data.success === false) {
        const err = new Error(data.message || 'Permintaan gagal diproses.');
        err.status = response.status;
        throw err;
      }

      return data;
    }

    /**
     * Ambil pesan error yang bisa ditampilkan ke user dari respons API.
     */
    function apiErrorMessage(data, status) {
      // Pesan validasi bawaan Laravel berbahasa Inggris dan menyebut angka
      // dalam kilobyte ("must not be greater than 2048 kilobytes"). Ganti
      // untuk field foto dengan kalimat yang bisa langsung dipahami warga.
      if (data && data.errors && Array.isArray(data.errors.foto)) {
        const pesanFoto = String(data.errors.foto[0] || '');
        if (/greater than|max:/i.test(pesanFoto)) {
          return 'Foto terlalu besar. Maksimal 3 MB - coba ambil ulang foto dengan kualitas lebih rendah.';
        }
        if (/mimes|image format|must be a file of type/i.test(pesanFoto)) {
          return 'Format foto tidak didukung. Gunakan foto JPG atau PNG.';
        }
        return pesanFoto;
      }

      if (data && data.errors) {
        const pertama = Object.values(data.errors)[0];
        if (Array.isArray(pertama) && pertama.length) {
          return String(pertama[0]);
        }
      }

      if (data && data.message) {
        // Pesan validasi bawaan Laravel ("validation.required") kurang
        // informatif, ganti pakai kalimat yang dimengerti user.
        if (String(data.message).startsWith('validation.')) {
          return 'Periksa kembali isian formulir Anda.';
        }
        return data.message;
      }

      if (status === 404) return 'Data tidak ditemukan.';
      if (status === 422) return 'Periksa kembali isian formulir Anda.';
      if (status === 419) return 'Sesi Anda sudah berakhir, silakan masuk kembali.';

      return 'Terjadi kesalahan pada server.';
    }

    // Initialize data from API
    async function loadInitialData() {
      try {
        // Warga tidak pernah memakai daftar warga, antrean verifikasi, maupun
        // kupon -- semuanya milik menu Secretariat. Hanya katalog hadiah yang
        // dipakai (menu Tukar Poin). Fetch 4 endpoint untuk semua role membuat
        // boot warga jauh lebih lambat, terutama di `php artisan serve` yang
        // memproses request satu per satu sehingga request paralel jadi antre.
        const adalahPengurus = currentUserSession && currentUserSession.role === 'pengurus';

        const [wargaRes, rewardsRes, verifRes, kuponRes] = await Promise.all([
          adalahPengurus ? apiFetch('/warga') : null,
          apiFetch('/rewards'),
          adalahPengurus ? apiFetch('/verifikasi/setoran') : null,
          adalahPengurus ? apiFetch('/verifikasi/kupon') : null,
        ]);

        if (wargaRes && wargaRes.data) listWarga = wargaRes.data;
        if (rewardsRes && rewardsRes.data) {
          katalogRewards = rewardsRes.data;
          // Tandai data sudah siap, supaya editReward/hapusReward tidak
          // menunggu loadRewards() yang tidak perlu dipanggil lagi.
          rewardsLoaded = true;
        }
        if (verifRes && verifRes.data) verifikasiPending = verifRes.data;
        if (kuponRes && kuponRes.data) listKuponVerifikasi = kuponRes.data;
        
        // Update badge counts
        const antreanEl = document.getElementById('antrean-verifikasi-count');
        if (antreanEl) antreanEl.innerText = verifikasiPending.length;
        const badgeEl = document.getElementById('badge-pending-count');
        if (badgeEl) badgeEl.innerText = verifikasiPending.length;
        
        const kuponPending = listKuponVerifikasi.filter(k => !k.diambil).length;
        const kuponBadge = document.getElementById('badge-kupon-pending-count');
        if (kuponBadge) kuponBadge.innerText = kuponPending;
      } catch (err) {
        console.warn('Gagal memuat data awal:', err);
      }
    }

    // Muat ulang daftar hadiah dari database (dipakai sebelum buka menu
    // Edit Katalog Hadiah & Tukar Poin supaya data selalu sinkron).
    async function loadRewards() {
      rewardsLoaded = false;
      muatHadiahAktif++;
      // Nomor urut permintaan ini. Permintaan sebelumnya yang belum selesai
      // otomatis dianggap basi dan hasilnya dibuang.
      const seq = ++rewardsFetchSeq;
      try {
        const res = await apiFetch('/rewards');

        // Ada permintaan lebih baru yang sudah jalan -- jangan timpa
        // datanya dengan hasil lama ini.
        if (seq !== rewardsFetchSeq) return;

        if (res.data) {
          katalogRewards = res.data;
          updatePoinDisplay();

          // Render SETELAH data sampai, supaya tidak sempat menggambar
          // daftar dari data lama yang sudah tidak ada di database.
          // KEDUA list dirender tanpa syarat halaman aktif, supaya halaman
          // Edit Katalog Hadiah (petugas) dan Penukaran Poin (warga) selalu
          // menampilkan katalog yang sama meski salah satunya sedang tersembunyi.
          renderRewardPengurusList();
          renderKatalogWarga();
        }
      } catch (err) {
        console.warn('Gagal memuat katalog hadiah:', err);
      } finally {
        // Hanya permintaan terakhir yang berhak menandai data sudah ready.
        if (seq === rewardsFetchSeq) rewardsLoaded = true;
        muatHadiahAktif--;
      }
    }

    // ---- Penyegaran katalog hadiah otomatis ----
    // Aplikasi ini SPA: tab yang dibiarkan terbuka tidak pernah memuat ulang
    // dokumen, sehingga katalognya bisa tertinggal dari perubahan di sesi
    // lain. Akibatnya Edit Katalog Hadiah (petugas) dan Penukaran Poin (warga)
    // terlihat berbeda padahal datanya sama. Karena itu katalog disegarkan:
    //   1) saat tab kembali terlihat / jendela difokuskan / dipulihkan,
    //   2) secara berkala selama salah satu dari dua halaman itu terbuka.
    let segarkanKatalogTerakhir = 0;
    function segarkanKatalog(paksa) {
      if (currentActivePageId !== 'kelola-reward' && currentActivePageId !== 'tukar') return;
      // Jangan menumpuk permintaan kalau sedang ada yang jalan, misalnya
      // ketika petugas sedang menyimpan hadiah.
      if (muatHadiahAktif > 0) return;
      const sekarang = Date.now();
      if (!paksa && sekarang - segarkanKatalogTerakhir < 15000) return;
      segarkanKatalogTerakhir = sekarang;
      loadRewards();
    }

    document.addEventListener('visibilitychange', () => {
      if (document.visibilityState === 'visible') segarkanKatalog(true);
    });
    window.addEventListener('focus', () => segarkanKatalog(true));
    window.addEventListener('pageshow', () => segarkanKatalog(true));
    setInterval(() => {
      if (document.visibilityState === 'visible') segarkanKatalog(false);
    }, 30000);

    async function loadKategoriSampah() {
      try {
        const res = await apiFetch('/kategori-sampah');
        if (res.data) {
          listKategoriSampah = res.data;
          populateKategoriSelect();
        }
      } catch (err) {
        console.warn('Gagal memuat kategori:', err);
      }
    }

    function populateKategoriSelect() {
      const select = document.getElementById('setor-kategori');
      if (!select) return;
      select.innerHTML = '<option value=""> Pilih Jenis Sampah </option>';
      listKategoriSampah.forEach(k => {
        const opt = document.createElement('option');
        opt.value = k.id;
        opt.textContent = `${k.nama} - ${k.poin_per_unit} Poin/${k.satuan}`;
        // Disimpan di option supaya form bisa menyesuaikan satuan & menghitung
        // estimasi poin tanpa perlu fetch ulang.
        opt.dataset.satuan = k.satuan;
        opt.dataset.poinPerUnit = k.poin_per_unit;
        opt.dataset.satuanOpsi = JSON.stringify(k.satuan_opsi || [k.satuan]);
        select.appendChild(opt);
      });
      updateSatuanDanEstimasiSetor();
    }

    
    function updateSatuanDanEstimasiSetor() {
      const selectKategori = document.getElementById('setor-kategori');
      const selectSatuan = document.getElementById('setor-satuan-berat');
      const inputBerat = document.getElementById('setor-berat');
      const kolomPoin = document.getElementById('setor-poin');
      const labelJumlah = document.getElementById('setor-label-jumlah');
      if (!selectKategori || !selectSatuan || !inputBerat) return;

      const opt = selectKategori.options[selectKategori.selectedIndex];
      const kategori = opt && opt.value ? listKategoriSampah.find(k => String(k.id) === String(opt.value)) : null;

      // Opsi satuan mengikuti kategori. Default: satuan dasar kategori.
      const opsi = kategori ? (kategori.satuan_opsi || [kategori.satuan]) : ['Kg', 'Gram'];
      const sebelumnya = selectSatuan.value;
      selectSatuan.innerHTML = opsi.map(s => `<option value="${s}">${s}</option>`).join('');
      if (opsi.includes(sebelumnya)) {
        selectSatuan.value = sebelumnya;
      } else {
        selectSatuan.value = opsi[0];
      }

      // Dropdown satuan disembunyikan kalau cuma ada satu pilihan (B3 -> Unit).
      // Menampilkan dropdown yang isinya cuma satu opsi hanya menambah klik
      // tanpa memberi pilihan apa pun. Satuannya sudah jelas dari judul kolom
      // ("Jumlah Unit") dan placeholder, jadi tidak ada informasi yang hilang.
      // Kolom inputnya tetap `flex-1`, jadi otomatis melebar penuh.
      if (opsi.length > 1) {
        selectSatuan.classList.remove('hidden');
      } else {
        selectSatuan.classList.add('hidden');
      }

      // Judul kolom dan placeholder ikut menyesuaikan. B3 dihitung per Unit,
      // bukan per kilogram, jadi menulis "Jumlah Berat" dan "Masukkan berat
      // sampah" di sana akan membuat warga mengira yang diminta kilo.
      const dasarKg = kategori ? String(kategori.satuan).toLowerCase() === 'kg' : true;
      if (labelJumlah) {
        labelJumlah.innerText = dasarKg ? 'Jumlah Berat' : 'Jumlah ' + (kategori ? kategori.satuan : '');
      }
      inputBerat.placeholder = dasarKg
        ? 'Masukkan berat sampah'
        : 'Masukkan jumlah ' + String(kategori ? kategori.satuan : '').toLowerCase();

      // Kolom poin selalu ditulis ulang, supaya tidak pernah menyisakan angka
      // dari kategori atau satuan sebelumnya.
      const tulisPoin = (nilai) => { if (kolomPoin) kolomPoin.value = nilai; };

      if (!kategori) {
        tulisPoin('0');
        return;
      }

      const angka = parseFloat(String(inputBerat.value).replace(',', '.'));
      if (!isFinite(angka) || angka <= 0) {
        tulisPoin('0');
        return;
      }

      const satuan = selectSatuan.value;
      const faktor = { kg: 1, gram: 0.001, gr: 0.001, unit: 1, buah: 1, biji: 1, pcs: 1 }[String(satuan).toLowerCase()] ?? 1;
      const beratDasar = Math.round((dasarKg ? angka * faktor : angka) * 100) / 100;
      const poin = Math.round(beratDasar * kategori.poin_per_unit);

      tulisPoin(String(poin));
    }

    /* ================= FUNGSI SEARCH UNIVERSAL ================= */
    function handleGlobalSearch(query) {
      const q = query.toLowerCase().trim();
      
      if (currentActivePageId === 'dashboard-warga') {
        filterDualView('tbl-dash-warga-body', '#cards-dash-warga .dash-card', 'cards-dash-warga', 'msg-empty-dash-warga', q, 'Riwayat setoran tidak ditemukan untuk kata kunci "{q}"');
      } else if (currentActivePageId === 'dashboard-rt') {
        filterDualView('tbl-dash-rt-body', '#cards-dash-rt .dash-card', 'cards-dash-rt', 'msg-empty-dash-rt', q, 'Transaksi tidak ditemukan untuk kata kunci "{q}"');
      } else if (currentActivePageId === 'riwayat') {
        filterRiwayat(q);
      } else if (currentActivePageId === 'daftar-warga') {
        renderDaftarWarga(q);
      } else if (currentActivePageId === 'verifikasi-kupon') {
        loadKuponPengurus();
        renderKuponPengurus(q);
      } else if (currentActivePageId === 'laporan') {
        loadBulanLaporan();
      } else if (currentActivePageId === 'verifikasi') {
        renderVerifikasi(q);
      } else if (currentActivePageId === 'tukar') {
        renderKatalogWarga(q);
      } else if (currentActivePageId === 'kelola-reward') {
        renderRewardPengurusList(q);
      } else if (currentActivePageId === 'panduan') {
        filterPanduanCards(q);
      }
    }

    // Memunculkan/menghapus baris "Data tidak ditemukan" pada sebuah tbody.
    function setFilterEmptyState(tbodyId, visibleCount, keyword) {
      const tbody = document.getElementById(tbodyId);
      if (!tbody) return;
      let noDataRow = tbody.querySelector('.no-search-results-row');
      if (visibleCount === 0) {
        if (!noDataRow) {
          const colSpan = rowCellCount(tbody);
          noDataRow = document.createElement('tr');
          noDataRow.className = 'no-search-results-row';
          noDataRow.innerHTML = '<td colspan="' + colSpan + '" class="py-6 text-center text-slate-400 dark:text-emerald-400 font-medium">Data tidak ditemukan sesuai kata kunci "' + keyword + '"</td>';
          tbody.appendChild(noDataRow);
        }
      } else {
        if (noDataRow) noDataRow.remove();
      }
    }

    // Halaman dengan dua tampilan dari data yang sama: tabel (tablet/desktop)
    // dan kartu (HP). Keduanya harus disaring bersamaan, kalau tidak kartu di
    // HP akan tetap menampilkan semua data walau tabelnya sudah tersaring.
    // Tabel disembunyikan di HP dan kartu disembunyikan di tablet, jadi tiap
    // tampilan juga butuh pesan "tidak ditemukan" sendiri.
    function filterDualView(tbodyId, cardSelector, cardWrapId, msgId, keyword, emptyText) {
      const rows = document.querySelectorAll('#' + tbodyId + ' tr:not(.no-search-results-row):not(.empty-state-row)');
      const cards = document.querySelectorAll(cardSelector);
      let visible = 0;

      rows.forEach(row => {
        const show = row.innerText.toLowerCase().includes(keyword);
        row.style.display = show ? '' : 'none';
        if (show) visible++;
      });
      cards.forEach(card => {
        card.style.display = card.innerText.toLowerCase().includes(keyword) ? '' : 'none';
      });

      setFilterEmptyState(tbodyId, visible, keyword);

      const wrap = document.getElementById(cardWrapId);
      if (!wrap) return;
      let msg = document.getElementById(msgId);
      if (visible === 0) {
        if (!msg) {
          msg = document.createElement('div');
          msg.id = msgId;
          msg.className = 'rounded-2xl border border-dashed border-slate-200 dark:border-emerald-800/70 p-6 text-center text-xs font-medium text-slate-400 dark:text-emerald-400';
          msg.innerText = emptyText.replace('{q}', keyword);
          wrap.appendChild(msg);
        }
      } else if (msg) {
        msg.remove();
      }
    }

    function filterRiwayat(keyword) {
      filterDualView(
        'tbl-riwayat-body',
        '#cards-riwayat .riwayat-card',
        'cards-riwayat',
        'msg-empty-riwayat-cards',
        keyword,
        'Riwayat tidak ditemukan untuk kata kunci "{q}"'
      );
    }

    function rowCellCount(tbody) {
      if (!tbody) return 5;
      const firstRow = tbody.querySelector('tr:not(.no-search-results-row)');
      return firstRow ? firstRow.children.length : 5;
    }

    function filterPanduanCards(keyword) {
      const cards = document.querySelectorAll('#container-panduan-cards .panduan-card');
      let visible = 0;
      cards.forEach(card => {
        const text = card.innerText.toLowerCase();
        if (text.includes(keyword)) {
          card.style.display = '';
          visible++;
        } else {
          card.style.display = 'none';
        }
      });

      const container = document.getElementById('container-panduan-cards');
      let emptyMsg = document.getElementById('msg-empty-panduan');
      if (visible === 0) {
        if (!emptyMsg) {
          emptyMsg = document.createElement('div');
          emptyMsg.id = 'msg-empty-panduan';
          emptyMsg.className = 'col-span-full text-center py-8 text-slate-400 dark:text-emerald-400 font-medium';
          emptyMsg.innerText = 'Panduan tidak ditemukan untuk kata kunci "' + keyword + '"';
          container.appendChild(emptyMsg);
        }
      } else {
        if (emptyMsg) emptyMsg.remove();
      }
    }

    // ---------------------------------------------------------------------
// KOMPRESI GAMBAR DI BROWSER
// ---------------------------------------------------------------------
// Foto dari kamera HP sekarang berukuran 3-8 MB, sedangkan batas upload
// server 3 MB. Mengirim file mentah hampir selalu gagal untuk foto kamera.
// Jadi gambar dikompres dulu di browser: sisi terpanjang dibatasi 1280px
// lalu di-encode ulang jadi JPEG. Foto 5 MB biasanya turun jadi 150-300 KB,
// cukup jelas untuk bukti sampah, dan jauh lebih cepat di jaringan seluler.
//
// Mengembalikan Promise<File>. Berkas asli tidak diubah.
function kompresGambar(file, maksSisi = 1280, kualitas = 0.82) {
  return new Promise((resolve, reject) => {
    if (!file || !file.type.startsWith('image/')) {
      reject(new Error('Berkas bukan gambar.'));
      return;
    }

    const url = URL.createObjectURL(file);
    const img = new Image();

    img.onload = () => {
      URL.revokeObjectURL(url);

      // Hanya diperkecil. Foto kecil dikirim apa adanya supaya tidak
      // menjadi lebih buruk dari aslinya.
      const sisiTerpanjang = Math.max(img.width, img.height);
      const skala = sisiTerpanjang > maksSisi ? maksSisi / sisiTerpanjang : 1;
      const lebar = Math.max(1, Math.round(img.width * skala));
      const tinggi = Math.max(1, Math.round(img.height * skala));

      const canvas = document.createElement('canvas');
      canvas.width = lebar;
      canvas.height = tinggi;

      const ctx = canvas.getContext('2d');
      if (!ctx) { reject(new Error('Browser tidak mendukung canvas.')); return; }

      // Isi latar putih dulu. Tanpa ini, foto PNG yang tembus transparan
      // akan berubah menjadi kotak hitam begitu diubah ke JPEG.
      ctx.fillStyle = '#ffffff';
      ctx.fillRect(0, 0, lebar, tinggi);
      ctx.imageSmoothingEnabled = true;
      ctx.imageSmoothingQuality = 'high';
      ctx.drawImage(img, 0, 0, lebar, tinggi);

      canvas.toBlob((blob) => {
        if (!blob) { reject(new Error('Gagal memproses gambar.')); return; }
        const namaAsli = (file.name || 'foto').replace(/\.[^.]+$/, '') || 'foto';
        resolve(new File([blob], namaAsli + '.jpg', { type: 'image/jpeg', lastModified: Date.now() }));
      }, 'image/jpeg', kualitas);
    };

    img.onerror = () => {
      URL.revokeObjectURL(url);
      // HEIC dari iPhone kadang tidak bisa di-decode browser. Penanggil
      // memakai berkas mentah sebagai cadangan dan membiarkan validasi
      // server yang memutuskan.
      reject(new Error('Gambar tidak bisa diproses oleh browser.'));
    };

    img.src = url;
  });
}

function hasilKeDataUrl(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = (e) => resolve(e.target.result);
    reader.onerror = () => reject(new Error('Gagal membaca berkas.'));
    reader.readAsDataURL(file);
  });
}

function formatUkuran(byte) {
  if (!Number.isFinite(byte)) return '-';
  if (byte < 1024) return byte + ' B';
  if (byte < 1024 * 1024) return Math.round(byte / 1024) + ' KB';
  return (byte / (1024 * 1024)).toFixed(1) + ' MB';
}

    // Foto profil dikirim sebagai base64 di dalam JSON (lihat simpanProfil).
    // Base64 menambah ukuran sekitar 33%, jadi foto 5 MB menjadi 6,7 MB di
    // payload. Karena itu foto dikompres lebih dulu di browser.
    function previewUploadedFoto(event) {
      const file = event.target.files[0];
      if (!file) return;

      const previewImg = document.getElementById('ep-preview-foto');
      const previewIcon = document.getElementById('ep-preview-icon');
      const infoEl = document.getElementById('ep-foto-info');

      // Preview berkas mentah supaya responsif; kompresi jalan di belakang.
      previewImg.src = URL.createObjectURL(file);
      previewImg.classList.remove('hidden');
      previewIcon.classList.add('hidden');
      if (infoEl) infoEl.innerText = 'Memproses foto...';

      // uploadedFotoProses menyimpan Promise-nya supaya simpanProfil bisa
      // menunggu hasil kompresi sebelum mengirim. Kalau user menekan tombol
      // ganti berkas, proses yang lebih baru menimpa yang lama.
      uploadedFotoProses = kompresGambar(file)
        .then((hasil) => hasilKeDataUrl(hasil).then((dataUrl) => {
          uploadedFotoDataUrl = dataUrl;
          if (infoEl) {
            infoEl.innerText = 'Akan dikirim ' + formatUkuran(hasil.size) +
              ' (dari ' + formatUkuran(file.size) + ')';
          }
        }))
        .catch(() => hasilKeDataUrl(file).then((dataUrl) => {
          // Kompresi gagal (mis. HEIC): pakai berkas mentah apa adanya,
          // validasi server yang akan menolak kalau kelewat besar.
          uploadedFotoDataUrl = dataUrl;
          if (infoEl) {
            infoEl.innerText = formatUkuran(file.size) + ' (tidak dikompres)';
          }
        }));
    }

    function applyUserProfileImage(photoUrl) {
      const avatarImg = document.getElementById('user-avatar-img');
      const defaultIcon = document.getElementById('user-avatar-default-icon');

      if (photoUrl) {
        avatarImg.src = photoUrl;
        avatarImg.classList.remove('hidden');
        defaultIcon.classList.add('hidden');
      } else {
        avatarImg.classList.add('hidden');
        defaultIcon.classList.remove('hidden');
      }
    }

    function getGreetingByTime() {
      const hour = new Date().getHours();
      if (hour >= 4 && hour < 11) return "Selamat Pagi";
      if (hour >= 11 && hour < 15) return "Selamat Siang";
      if (hour >= 15 && hour < 18) return "Selamat Sore";
      return "Selamat Malam";
    }

    function toggleProfileDropdown() {
      const menu = document.getElementById('profile-dropdown-menu');
      menu.classList.toggle('hidden');
    }

    function closeProfileDropdown() {
      document.getElementById('profile-dropdown-menu').classList.add('hidden');
    }

    document.addEventListener('click', function(e) {
      const dropdown = document.getElementById('profile-dropdown-menu');
      const button = e.target.closest('button[onclick="toggleProfileDropdown()"]');
      if (!button && dropdown && !dropdown.contains(e.target)) {
        dropdown.classList.add('hidden');
      }
    });

    /**
     * Perbarui seluruh penulisan saldo poin di seluruh halaman.
     *
     * Menu "Tukar Poin" SENGAJA tidak pernah dikunci, berapa pun saldo poin
     * warga. Alasannya, halaman katalog sudah menampilkan harga tiap hadiah dan
     * konfirmasiTukarPoin() yang menjelaskan berapa poin yang kurang kalau
     * warga tetap menekan tombol Tukar. Menu yang mati justru menutup informasi
     * yang justru dicari warga, tanpa memberi jalan keluar.
     */
    function updatePoinDisplay() {
      document.querySelectorAll('.user-poin-val').forEach(el => {
        el.innerText = userPoin;
      });

      // Class nav-tukar dan banner-btn-tukar sengaja tidak disentuh di sini.
      // Jangan menambahkan `cursor-not-allowed` pada keduanya: navigate() memakai
      // class itu untuk menentukan menu aktif, sehingga menu yang terkunci juga
      // gagal menampilkan tanda aktifnya.
    }

    function setLoginTab(role) {
      activeLoginTab = role;
      const tabWarga = document.getElementById('tab-login-warga');
      const tabPengurus = document.getElementById('tab-login-pengurus');

      if (role === 'warga') {
        tabWarga.className = 'flex-1 py-3 rounded-xl bg-brand-600 text-white transition-all flex items-center justify-center gap-2';
        tabPengurus.className = 'flex-1 py-3 rounded-xl text-slate-600 dark:text-emerald-300 hover:text-brand-600 transition-all flex items-center justify-center gap-2';
      } else {
        tabPengurus.className = 'flex-1 py-3 rounded-xl bg-brand-600 text-white transition-all flex items-center justify-center gap-2';
        tabWarga.className = 'flex-1 py-3 rounded-xl text-slate-600 dark:text-emerald-300 hover:text-brand-600 transition-all flex items-center justify-center gap-2';
      }
    }

    async function handleLoginSubmit(e) {
      e.preventDefault();
      const user = document.getElementById('login-username').value.trim();
      const pass = document.getElementById('login-password').value.trim();
      const card = document.getElementById('login-card');

      try {
        const res = await apiFetch('/login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          // Kirim tab yang sedang aktif. Backend memakai role ini untuk
          // membatasi login, jadi warga yang menyamar lewat tab
          // "Pengurus RT" akan ditolak.
          body: JSON.stringify({ username: user, password: pass, role: activeLoginTab })
        });

        if (!res.success) {
          card.classList.remove('animate-shake');
          void card.offsetWidth;
          card.classList.add('animate-shake');
          showModernModal('login-failed', 'Login Gagal', res.message || 'Username atau kata sandi yang Anda masukkan salah');
          return;
        }

        authToken = res.token;
        localStorage.setItem('plung_token', res.token);
        currentUserSession = res.user;
        userPoin = res.user.poin || 0;

        showModernModal('success', 'Login Berhasil', 'Selamat datang di aplikasi PLUNG!', () => {
          setupUserSystem();
        });
      } catch (err) {
        card.classList.remove('animate-shake');
        void card.offsetWidth;
        card.classList.add('animate-shake');
        // Tampilkan pesan asli dari server, mis. "Username atau password yang
        // Anda masukkan salah." Pesan jaringan generik hanya untuk kegagalan
        // jaringan yang sebenarnya (apiFetch sudah merapikannya).
        showModernModal('login-failed', 'Login Gagal',
          (err && err.message) ? err.message : 'Terjadi kesalahan jaringan. Silakan coba lagi.');
      }
    }

    // Satu tempat untuk berpindah antara tiga tampilan: boot, login, dan aplikasi.
    // disembunyikan) perlu disembunyikan dari awal supaya tidak pernah
    // terlihat di frame pertama -- itulah sumber kilat halaman login saat
    // refresh, karena form login dulu tampil sebagai kondisi awal.
    function tampilkanLogin() {
      const boot = document.getElementById('boot-screen');
      const auth = document.getElementById('auth-screen');
      const main = document.getElementById('main-app');
      if (boot) boot.classList.add('hidden');
      if (auth) auth.classList.remove('hidden');
      if (main) main.classList.add('hidden');
    }

    function tampilkanAplikasi() {
      const boot = document.getElementById('boot-screen');
      const auth = document.getElementById('auth-screen');
      const main = document.getElementById('main-app');
      if (boot) boot.classList.add('hidden');
      if (auth) auth.classList.add('hidden');
      if (main) main.classList.remove('hidden');
    }

    async function setupUserSystem() {
      // Kerangka aplikasi ditampilkan sebelum data dimuat, jadi pengguna
      // langsung melihat tata letak halaman dan tidak mengira masih loading.
      tampilkanAplikasi();

      const navWarga = document.getElementById('nav-group-warga');
      const navPengurus = document.getElementById('nav-group-pengurus');
      const nameEl = document.getElementById('user-display-name');
      const dropName = document.getElementById('dropdown-user-name');
      const dropRole = document.getElementById('dropdown-user-role');

      if (currentUserSession && currentUserSession.foto) {
        applyUserProfileImage(currentUserSession.foto);
      } else {
        applyUserProfileImage(null);
      }

      nameEl.innerText = currentUserSession.nama;
      dropName.innerText = currentUserSession.nama;

      // Keduanya tidak saling bergantung, jadi jalankan berbaris. Sebelumnya
      // di-await berurutan, menambah satu putaran penuh (~1 detik) ke boot.
      await Promise.all([loadInitialData(), loadKategoriSampah()]);

      if (currentUserSession.role === 'pengurus') {
        navWarga.classList.add('hidden');
        navPengurus.classList.remove('hidden');
        dropRole.innerText = currentUserSession.rumah || "Pengurus RT 36/RW 09";

        renderTop3Warga(listWarga);
        renderKuponPengurus();
        renderRewardPengurusList();
        navigate('dashboard-rt');
      } else {
        navPengurus.classList.add('hidden');
        navWarga.classList.remove('hidden');
        dropRole.innerText = "Warga RT 36/RW 09";

        const greeting = getGreetingByTime();
        document.getElementById('warga-greeting-text').innerText = greeting + ', ' + currentUserSession.nama;

        navigate('dashboard-warga');
      }

      updatePoinDisplay();
      updateRiwayatCount();
      renderKatalogWarga();
      renderVerifikasi();
      renderDaftarWarga();
      renderIkon();
    }

    /**
     * Card "Warga Aktif Pilah Sampah" (Top 3 dengan medali) di dashboard petugas.
     *
     * Sumber urutan: TOTAL SAMPAH TERVERIFIKASI (Kg), sama persis dengan
     * peringkat di dashboard warga. Poin sengaja tidak dipakai karena
     * naik-turun setiap kali warga menukar hadiah sehingga urutan ikut bergeser
     * padahal tidak ada sampah baru yang masuk.
     *
     * @param {Array} daftar daftar warga; boleh dari /api/warga atau data.top3
     */
    function renderTop3Warga(daftar) {
      const container = document.getElementById('top-3-warga-container');
      if (!container) return;

      const semua = Array.isArray(daftar) ? daftar : [];

      const teratas = semua
        .filter(w => Number(w.total_sampah) > 0)
        .sort((a, b) => {
          const sampahA = Number(a.total_sampah) || 0;
          const sampahB = Number(b.total_sampah) || 0;
          if (sampahA !== sampahB) return sampahB - sampahA;   // paling banyak di atas
          return (a.id || 0) - (b.id || 0);                    // pemecah seri: id, stabil
        })
        .slice(0, 3);

      // Siluet medali pita (bukan angka), polos tanpa garis tepi dan tanpa
      // bayangan. Peringkat tidak lagi ditulis di dalam ikon, tapi tetap
      // terbaca dari urutan daftar -- yang diurut total sampah -- dan dari
      // warna emas/perak/perunggu.
      const medalBadges = [
        '<div class="w-8 h-8 flex items-center justify-center shrink-0"><svg class="w-8 h-8" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13 4 L20 14 L27 4" stroke="#FBBF24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><circle cx="20" cy="25" r="11" fill="#F59E0B"/><path d="M20 18.5 L21.9 22.9 L26.5 23.3 L23.1 26.5 L24 31.1 L20 28.8 L16 31.1 L16.9 26.5 L13.5 23.3 L18.1 22.9 Z" fill="#FFFFFF"/></svg></div>',
        '<div class="w-8 h-8 flex items-center justify-center shrink-0"><svg class="w-8 h-8" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13 4 L20 14 L27 4" stroke="#CBD5E1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><circle cx="20" cy="25" r="11" fill="#94A3B8"/><path d="M20 18.5 L21.9 22.9 L26.5 23.3 L23.1 26.5 L24 31.1 L20 28.8 L16 31.1 L16.9 26.5 L13.5 23.3 L18.1 22.9 Z" fill="#FFFFFF"/></svg></div>',
        '<div class="w-8 h-8 flex items-center justify-center shrink-0"><svg class="w-8 h-8" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13 4 L20 14 L27 4" stroke="#FDBA74" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><circle cx="20" cy="25" r="11" fill="#EA580C"/><path d="M20 18.5 L21.9 22.9 L26.5 23.3 L23.1 26.5 L24 31.1 L20 28.8 L16 31.1 L16.9 26.5 L13.5 23.3 L18.1 22.9 Z" fill="#FFFFFF"/></svg></div>'
      ];

      if (teratas.length === 0) {
        container.innerHTML = '<p class="text-xs text-slate-400 dark:text-emerald-400 text-center py-4 font-medium">Belum ada warga yang menyetorkan sampah</p>';
        return;
      }

      container.innerHTML = teratas.map((w, index) =>
        '<div class="p-3 bg-slate-50 dark:bg-brand-950 rounded-2xl border border-slate-200/80 dark:border-emerald-800/80 flex justify-between items-center transition-all hover:bg-slate-100/70 dark:hover:bg-brand-900/40">' +
          '<div class="flex items-center gap-3">' +
            (medalBadges[index] || '') +
            '<div>' +
              '<p class="font-extrabold text-slate-900 dark:text-white text-xs">' + w.nama + '</p>' +
              '<p class="text-[10px] text-slate-400 dark:text-emerald-400 font-semibold">' + (w.rumah || '-') + '</p>' +
            '</div>' +
          '</div>' +
          '<span class="font-black text-brand-600 dark:text-brand-300 text-xs">' +
            ANGKA_DESIMAL.format(Number(w.total_sampah) || 0) + ' Kg</span>' +
        '</div>'
      ).join('');
    }

    function updateRiwayatCount() {
      const rows = document.querySelectorAll('#tbl-riwayat-body tr:not(.no-search-results-row):not(.empty-state-row)').length;
      document.getElementById('badge-total-aktivitas').innerText = rows + ' Aktivitas';
    }

    function confirmLogout() {
      showModernModal('confirm', 'Konfirmasi Logout', 'Keluar dari aplikasi PLUNG!?', async () => {
        try {
          await apiFetch('/logout', { method: 'POST' });
        } catch (err) {
          // Ignore logout API errors
        }
        localStorage.removeItem('plung_token');
        authToken = null;
        currentUserSession = null;
        tampilkanLogin();
        document.getElementById('login-username').value = '';
        document.getElementById('login-password').value = '';
      });
    }

    function navigate(pageId) {
      // Kalau sedang di halaman Edit Katalog Hadiah, kosongkan form agar
      // id item lama tidak ikut tersimpan saat pindah halaman.
      if (currentActivePageId === 'kelola-reward' && pageId !== 'kelola-reward') {
        resetFormReward();
      }

      // Di layar kecil sidebar berupa drawer yang menutupi konten. Kalau tidak
      // ditutup di sini, warga menekan menu dan halaman yang terbuka justru
      // tetap tertutup drawer di atasnya.
      tutupSidebar();

      currentActivePageId = pageId;

      if (pageId !== 'edit-profil') {
        previousActivePage = pageId;
      }

      document.querySelectorAll('.page-content').forEach(p => {
        p.classList.add('hidden');
        p.classList.remove('animate-slide-up');
      });

      const target = document.getElementById('page-' + pageId);
      if (target) {
        target.classList.remove('hidden');
        void target.offsetWidth;
        target.classList.add('animate-slide-up');
      }

      // Lazy-load data when navigating.
      // Setiap kali menu dibuka, datanya diambil lagi dari server. Tanpa ini
      // pengguna harus refresh browser (F5) untuk melihat perubahan yang
      // dibuat dari halaman atau perangkat lain.
      if (pageId === 'dashboard-warga' && currentUserSession && currentUserSession.role === 'warga') {
        loadDashboardWarga();
      } else if (pageId === 'dashboard-rt' && currentUserSession && currentUserSession.role === 'pengurus') {
        loadDashboardRT();
      } else if (pageId === 'riwayat') {
        loadRiwayat();
      } else if (pageId === 'verifikasi-kupon') {
        // Selalu ambil daftar kupon terbaru dari database, supaya kupon
        // yang baru saja ditukar warga langsung terlihat di halaman ini.
        loadKuponPengurus();
      } else if (pageId === 'daftar-warga') {
        // Nama, no. rumah, no. WA, dan username bisa berubah kapan saja, jadi
        // tabel harus di-refresh dari database, bukan mengandalkan hasil
        // pemuatan saat login.
        loadWarga();
      } else if (pageId === 'verifikasi') {
        // Antrean setoran terus bertambah dari warga, jadi selalu ambil
        // yang terbaru. Angka badge di sidebar ikut terupdate lewat
        // renderVerifikasi() di dalam loader ini.
        loadVerifikasiSetoran();
      } else if (pageId === 'laporan') {
        // Daftar bulan yang tersedia ikut berubah setiap ada setoran baru.
        loadBulanLaporan();
      }

      const searchInput = document.getElementById('global-search-input');
      if (searchInput) {
        searchInput.value = '';
        handleGlobalSearch('');
      }

      // Muat data hadiah SETELAH handleGlobalSearch, karena fungsi search
      // me-render daftar secara sinkron. loadRewards() memuat ulang dari
      // database lalu me-render ulang, jadi daftar akhir selalu yang benar.
      if (pageId === 'kelola-reward' || pageId === 'tukar') {
        loadRewards();
      }

      document.querySelectorAll('.nav-btn').forEach(btn => {
        if (!btn.classList.contains('cursor-not-allowed')) {
          btn.className = 'nav-btn smooth-zoom w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all active-press text-slate-600 dark:text-emerald-200 hover:bg-slate-100 dark:hover:bg-brand-950 font-bold';
        }
      });

      const activeNav = document.getElementById('nav-' + pageId);
      if (activeNav && !activeNav.classList.contains('cursor-not-allowed')) {
        activeNav.className = 'nav-btn smooth-zoom w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all active-press bg-brand-600 text-white font-extrabold shadow-sm';
      }
    }

    /* ============ BADGE TREN DASHBOARD (WARGA) ============ */
    // Gaya badge tren diseragamkan untuk card Poin, Sampah, dan Peringkat.
    // Tren naik = hijau, tren turun = merah, tidak berubah = abu-abu.
    const TREND_BADGE_BASE = 'px-2.5 py-0.5 font-extrabold text-[10px] rounded-full flex items-center gap-1 border border-transparent dark:border-emerald-800';
    const TREND_BADGE_COLORS = {
      up: 'bg-emerald-100 dark:bg-emerald-950/90 text-brand-700 dark:text-brand-300',
      down: 'bg-red-100 dark:bg-red-950/90 text-red-700 dark:text-red-300',
      same: 'bg-slate-100 dark:bg-slate-950/90 text-slate-700 dark:text-slate-300'
    };
    const CHIP_INFO_BASE = 'text-xs font-extrabold px-3 py-1 rounded-xl text-brand-600 dark:text-brand-300 bg-brand-50 dark:bg-brand-950 border border-transparent dark:border-emerald-800/60';
    const ANGKA_DESIMAL = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 1 });

    /**
     * Badge card "Total Poin Saya".
     *
     * Satu arah saja, mengikuti pergerakan poin TERAKHIR:
     *   naik  = setoran yang terakhir diverifikasi petugas
     *   turun = penukaran hadiah yang terakhir
     *
     * Jangan pernah menampilkan dua panah sekaligus. Kalau warga menyetor lalu
     * menukar hadiah, kartu hanya boleh memuat satu ikon: yang paling baru.
     * Badge kumulatif (total masuk + total keluar) akan memunculkan dua panah
     * sekaligus, padahal kartu ini hanya punya satu ruang kecil untuk tren.
     *
     * Kalau warga belum pernah punya poin, tidak ada panah sama sekali, hanya
     * satu pil abu-abu supaya kartu tidak terlihat kosong.
     *
     * Badge ini sengaja tanpa tooltip. Atribut `title` bawaan browser hanya
     * muncul saat mouse diarahkan dan butuh sekitar 1 detik, sedangkan di HP
     * tidak muncul sama sekali karena tidak ada hover. Memaksakan keterangan
     * lewat `title` jadi tidak berguna di layar sentuh, jadi informasinya
     * cukup ditunjukkan lewat ikon dan angkanya saja.
     */
    function setPoinBadge(trend) {
      const badge = document.getElementById('trend-poin-badge');
      if (!badge) return;

      const t = trend || {};
      const dir = t.direction === 'up' || t.direction === 'down' ? t.direction : 'same';
      const nilai = Math.max(0, Math.abs(Number(t.value) || 0));

      badge.removeAttribute('title');

      if (dir === 'same' || nilai === 0) {
        badge.className = TREND_BADGE_BASE + ' ' + TREND_BADGE_COLORS.same;
        badge.innerHTML = '<i data-lucide="minus" class="w-3 h-3"></i> <span>0</span>';
        renderIkon();
        return;
      }

      const naik = dir === 'up';
      const warna = naik ? TREND_BADGE_COLORS.up : TREND_BADGE_COLORS.down;
      const ikon = naik ? 'trending-up' : 'trending-down';
      const awalan = naik ? '+' : '-';

      badge.className = TREND_BADGE_BASE + ' ' + warna;
      badge.innerHTML = '<i data-lucide="' + ikon + '" class="w-3 h-3"></i>'
        + ' <span>' + awalan + nilai.toLocaleString('id-ID') + '</span>';
      renderIkon();
    }

    /**
     * Isi badge tren: ikon naik/turun, warna, dan nilai.
     *
     * @param {string} badgeId    id elemen badge
     * @param {string} dir        'up' | 'down' | 'same'
     * @param {string} text       teks nilai, mis. '+2', '-1', '0', '+100%'
     * @param {string} [sameIcon] ikon yang dipakai saat dir = 'same'
     */
    function setTrendBadge(badgeId, dir, text, sameIcon = 'minus') {
      const badge = document.getElementById(badgeId);
      if (!badge) return;

      const icon = dir === 'up' ? 'trending-up' : dir === 'down' ? 'trending-down' : sameIcon;
      const colorClass = TREND_BADGE_COLORS[dir] || TREND_BADGE_COLORS.same;

      badge.className = TREND_BADGE_BASE + ' ' + colorClass;
      badge.removeAttribute('title');
      badge.innerHTML = '<i data-lucide="' + icon + '" class="w-3 h-3"></i> <span>' + text + '</span>';
      renderIkon();
    }

    /**
     * Isi chip informasi (bukan tren): persentase porsi warga dari total sampah RT.
     */
    function setInfoChip(badgeId, text) {
      const badge = document.getElementById(badgeId);
      if (!badge) return;

      badge.className = CHIP_INFO_BASE;
      badge.removeAttribute('title');
      badge.innerText = text;
    }

    async function loadDashboardWarga() {
      try {
        const data = await apiFetch('/dashboard/warga');
        userPoin = data.poin;
        
        // Update stat cards
        document.querySelectorAll('.user-poin-val').forEach(el => el.innerText = data.poin);
        document.getElementById('warga-total-sampah').innerText = data.total_sampah;
        
        // Update peringkat - hanya tampil jika ada poin
        const peringkatEl = document.getElementById('warga-peringkat');
        const totalWargaEl = document.getElementById('warga-total-warga');
        if (data.peringkat !== null && data.peringkat !== undefined) {
            peringkatEl.innerText = data.peringkat;
            totalWargaEl.innerText = data.total_warga;
            peringkatEl.parentElement.classList.remove('hidden');
        } else {
            peringkatEl.innerText = '-';
            totalWargaEl.innerText = '-';
            peringkatEl.parentElement.classList.add('hidden');
        }

        // Badge card Poin: satu ikon, mengikuti pergerakan poin terakhir.
        // naik = setoran terverifikasi, turun = tukar hadiah.
        setPoinBadge(data.trend_poin);

        if (data.trend_sampah) {
          const sampahDir = data.trend_sampah.direction;
          const sampahPct = Math.abs(Math.round(data.trend_sampah.percentage));
          const sampahLabel = (sampahDir === 'up' ? '+' : sampahDir === 'down' ? '-' : '') + sampahPct + '%';
          setTrendBadge('trend-sampah-badge', sampahDir, sampahLabel);
        }
        if (data.trend_peringkat) {
          const dir = data.trend_peringkat.direction;
          const val = Math.abs(data.trend_peringkat.value);
          const label = dir === 'up' ? '+' + val : dir === 'down' ? '-' + val : '0';
          setTrendBadge('trend-peringkat-badge', dir, label);
        }

        // Chip persentase kontribusi warga terhadap total sampah RT terkumpul
        // (bukan tren, jadi tanpa ikon naik/turun)
        if (data.trend_bulanan) {
          setInfoChip('trend-poin-percentage', ANGKA_DESIMAL.format(data.trend_bulanan.value || 0) + '%');
        }

        // Update chart
        initChartBulanan(data.bulanan);
        initChartProporsi(data.proporsi);

        // Update riwayat table + kartu HP
        const tbody = document.getElementById('tbl-dash-warga-body');
        const dashCards = document.getElementById('cards-dash-warga');
        const riwayat = (data.riwayat_terakhir && data.riwayat_terakhir.length > 0) ? data.riwayat_terakhir : [];

        if (tbody) {
          tbody.innerHTML = riwayat.length > 0
            ? riwayat.map(r =>
                '<tr class="hover:bg-slate-50 dark:hover:bg-brand-950/60 transition-colors">' +
                  // Semua kolom rata tengah supaya sejajar dengan judulnya.
                  '<td class="py-3 text-center text-slate-400 dark:text-emerald-300">' + r.tanggal + '</td>' +
                  '<td class="py-3 text-center font-extrabold text-slate-900 dark:text-white">' + r.kategori + '</td>' +
                  '<td class="py-3 text-center">' + r.berat + '</td>' +
                  '<td class="py-3 text-center">' + statusBadge(r.status) + '</td>' +
                  '<td class="py-3 text-center font-extrabold ' + (r.poin >= 0 ? 'text-brand-600 dark:text-brand-300' : 'text-rose-500') + '">' + (r.poin >= 0 ? '+' : '') + r.poin + ' pts</td>' +
                '</tr>'
              ).join('')
            : '<tr class="empty-state-row"><td colspan="5" class="py-6 text-center text-slate-400 dark:text-emerald-400 text-xs font-medium">Belum ada transaksi</td></tr>';
        }

        // Versi kartu untuk layar HP. Susunannya sengaja dibuat sama dengan
        // kartu di halaman Riwayat (tanggal, poin, lalu detail, lalu badge)
        // supaya tidak terlihat seperti dua format berbeda untuk data yang
        // sama. Dibangun dari data yang sama dengan tabel, jadi tidak mungkin
        // menampilkan angka yang berbeda di antara dua tampilan.
        if (dashCards) {
          dashCards.innerHTML = riwayat.length > 0
            ? riwayat.map(r => {
                const poinCls = r.poin >= 0 ? 'text-brand-600 dark:text-brand-300' : 'text-rose-500';
                return '<div class="dash-card rounded-2xl border border-slate-200/80 dark:border-emerald-800/70 bg-slate-50/70 dark:bg-brand-950/40 p-4 space-y-2.5">' +
                  '<div class="flex items-start justify-between gap-3">' +
                    '<div class="min-w-0">' +
                      '<p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-emerald-400">' + r.tanggal + '</p>' +
                      '<p class="text-sm font-extrabold text-slate-900 dark:text-white leading-tight mt-0.5 break-words">' + r.kategori + '</p>' +
                    '</div>' +
                    '<span class="shrink-0 font-extrabold text-xs ' + poinCls + '">' + (r.poin >= 0 ? '+' : '') + r.poin + ' pts</span>' +
                  '</div>' +
                  '<p class="text-xs text-slate-500 dark:text-emerald-300/80 leading-relaxed break-words">' + r.berat + '</p>' +
                  '<div>' + statusBadge(r.status) + '</div>' +
                '</div>';
              }).join('')
            : '<div class="rounded-2xl border border-dashed border-slate-200 dark:border-emerald-800/70 p-6 text-center text-xs font-medium text-slate-400 dark:text-emerald-400">Belum ada transaksi</div>';
        }

        updatePoinDisplay();
      } catch (err) {
        console.warn('Gagal memuat dashboard warga:', err);
      }
    }

    async function loadDashboardRT() {
      try {
        const data = await apiFetch('/dashboard/pengurus');
        
        document.getElementById('total-warga-count').innerText = data.total_warga;
        document.getElementById('rt-total-sampah').innerText = data.total_sampah;
        document.getElementById('antrean-verifikasi-count').innerText = data.antrean;

        // Update trend badges
        if (data.trend_warga) {
          document.getElementById('trend-warga-text').innerText = (data.trend_warga.direction === 'up' ? '+' : '') + (data.trend_warga.value || 0);
        }
        if (data.trend_sampah_rt) {
          document.getElementById('trend-sampah-rt-text').innerText = (data.trend_sampah_rt.direction === 'up' ? '+' : '') + data.trend_sampah_rt.percentage + '%';
        }

        // Update charts
        initChartTrenRT(data.bulanan);
        initChartKomposisiRT(data.komposisi);

        // Update top 3
        renderTop3Warga(data.top3);

        // Update transaksi table + kartu HP
        const tbody = document.getElementById('tbl-dash-rt-body');
        const dashCards = document.getElementById('cards-dash-rt');
        const transaksi = (data.transaksi_terbaru && data.transaksi_terbaru.length > 0) ? data.transaksi_terbaru : [];

        if (tbody) {
          tbody.innerHTML = transaksi.length > 0
            ? transaksi.map(t =>
                '<tr class="hover:bg-slate-50 dark:hover:bg-brand-950/60 transition-colors">' +
                  // Semua kolom rata tengah supaya sejajar dengan judulnya.
                  '<td class="py-3 text-center text-slate-400 dark:text-emerald-300">' + t.tanggal + '</td>' +
                  '<td class="py-3 text-center font-extrabold text-slate-900 dark:text-white">' + t.nama + '</td>' +
                  '<td class="py-3 text-center text-slate-500 dark:text-emerald-300/80">' + t.rincian + '</td>' +
                  '<td class="py-3 text-center">' + statusBadge(t.status) + '</td>' +
                  '<td class="py-3 text-center font-extrabold ' + (t.poin >= 0 ? 'text-brand-600 dark:text-brand-300' : 'text-rose-500') + '">' + (t.poin >= 0 ? '+' : '') + t.poin + ' pts</td>' +
                '</tr>'
              ).join('')
            : '<tr class="empty-state-row"><td colspan="5" class="py-6 text-center text-slate-400 dark:text-emerald-400 text-xs font-medium">Belum ada transaksi warga</td></tr>';
        }

        // Versi kartu untuk layar HP, disusun sama persis dengan kartu di
        // halaman Riwayat: tanggal dan poin di baris pertama, lalu detail,
        // lalu badge status. "Nama Warga" menempati posisi judul, "Rincian"
        // menempati posisi baris detail.
        if (dashCards) {
          dashCards.innerHTML = transaksi.length > 0
            ? transaksi.map(t => {
                const poinCls = t.poin >= 0 ? 'text-brand-600 dark:text-brand-300' : 'text-rose-500';
                return '<div class="dash-card rounded-2xl border border-slate-200/80 dark:border-emerald-800/70 bg-slate-50/70 dark:bg-brand-950/40 p-4 space-y-2.5">' +
                  '<div class="flex items-start justify-between gap-3">' +
                    '<div class="min-w-0">' +
                      '<p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-emerald-400">' + t.tanggal + '</p>' +
                      '<p class="text-sm font-extrabold text-slate-900 dark:text-white leading-tight mt-0.5 break-words">' + t.nama + '</p>' +
                    '</div>' +
                    '<span class="shrink-0 font-extrabold text-xs ' + poinCls + '">' + (t.poin >= 0 ? '+' : '') + t.poin + ' pts</span>' +
                  '</div>' +
                  '<p class="text-xs text-slate-500 dark:text-emerald-300/80 leading-relaxed break-words">' + t.rincian + '</p>' +
                  '<div>' + statusBadge(t.status) + '</div>' +
                '</div>';
              }).join('')
            : '<div class="rounded-2xl border border-dashed border-slate-200 dark:border-emerald-800/70 p-6 text-center text-xs font-medium text-slate-400 dark:text-emerald-400">Belum ada transaksi warga</div>';
        }
      } catch (err) {
        console.warn('Gagal memuat dashboard RT:', err);
      }
    }

    function statusBadge(status) {
      const s = (status || '').toLowerCase();
      if (s === 'diterima') return '<span class="px-2.5 py-1 bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 font-extrabold text-[10px] rounded-full border border-transparent dark:border-emerald-800">Diterima</span>';
      if (s === 'ditolak') return '<span class="px-2.5 py-1 bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 font-extrabold text-[10px] rounded-full border border-transparent dark:border-rose-800">Ditolak</span>';
      if (s === 'diproses' || s === 'proses') return '<span class="px-2.5 py-1 bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 font-extrabold text-[10px] rounded-full border border-transparent dark:border-blue-800">Proses</span>';
      if (s === 'pending') return '<span class="px-2.5 py-1 bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 font-extrabold text-[10px] rounded-full border border-transparent dark:border-amber-800/60">Pending</span>';
      if (s === 'belum_ditukar') return '<span class="px-2.5 py-1 bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 font-extrabold text-[10px] rounded-full border border-transparent dark:border-amber-800/60">Belum Diterima</span>';
      if (s === 'sudah_ditukar') return '<span class="px-2.5 py-1 bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 font-extrabold text-[10px] rounded-full border border-transparent dark:border-emerald-800">Sudah Ditukar</span>';
      return '<span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-950 text-slate-700 dark:text-slate-300 font-extrabold text-[10px] rounded-full border border-transparent dark:border-slate-800">' + (s || '-') + '</span>';
    }

    async function loadRiwayat() {
      try {
        const res = await apiFetch('/riwayat');
        const tbody = document.getElementById('tbl-riwayat-body');
        const cards = document.getElementById('cards-riwayat');
        const rows = (res.data && res.data.length > 0) ? res.data : [];

        if (tbody) {
          tbody.innerHTML = rows.length > 0
            ? rows.map(r =>
                '<tr>' +
                  '<td class="py-3.5 text-slate-400 dark:text-emerald-300">' + r.tanggal + '</td>' +
                  '<td class="py-3.5 font-extrabold text-slate-900 dark:text-white">' + (r.jenis || '').replace(/_/g, ' ') + '</td>' +
                  '<td class="py-3.5 text-slate-500 dark:text-emerald-300/80">' + r.rincian + '</td>' +
                  '<td class="py-3.5">' + statusBadge(r.status) + '</td>' +
                  '<td class="py-3.5 text-right font-extrabold ' + (r.poin >= 0 ? 'text-brand-600 dark:text-brand-300' : 'text-rose-500') + '">' + (r.poin >= 0 ? '+' : '') + r.poin + ' pts</td>' +
                '</tr>'
              ).join('')
            : '<tr class="empty-state-row"><td colspan="5" class="py-6 text-center text-slate-400 dark:text-emerald-400 text-xs font-medium">Belum ada aktivitas warga</td></tr>';
        }

        // Versi kartu untuk layar HP. Sengaja dibangun dari data yang sama
        // dengan tabel di atas supaya isi, badge status, dan poinnya tidak
        // mungkin berbeda di antara dua tampilan.
        if (cards) {
          cards.innerHTML = rows.length > 0
            ? rows.map(r => {
                const poinCls = r.poin >= 0 ? 'text-brand-600 dark:text-brand-300' : 'text-rose-500';
                return '<div class="riwayat-card rounded-2xl border border-slate-200/80 dark:border-emerald-800/70 bg-slate-50/70 dark:bg-brand-950/40 p-4 space-y-2.5">' +
                  '<div class="flex items-start justify-between gap-3">' +
                    '<div class="min-w-0">' +
                      '<p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-emerald-400">' + r.tanggal + '</p>' +
                      '<p class="text-sm font-extrabold text-slate-900 dark:text-white leading-tight mt-0.5 break-words">' + (r.jenis || '').replace(/_/g, ' ') + '</p>' +
                    '</div>' +
                    '<span class="shrink-0 font-extrabold text-xs ' + poinCls + '">' + (r.poin >= 0 ? '+' : '') + r.poin + ' pts</span>' +
                  '</div>' +
                  '<p class="text-xs text-slate-500 dark:text-emerald-300/80 leading-relaxed break-words">' + r.rincian + '</p>' +
                  '<div>' + statusBadge(r.status) + '</div>' +
                '</div>';
              }).join('')
            : '<div class="rounded-2xl border border-dashed border-slate-200 dark:border-emerald-800/70 p-6 text-center text-xs font-medium text-slate-400 dark:text-emerald-400">Belum ada aktivitas warga</div>';
        }

        updateRiwayatCount();
      } catch (err) {
        console.warn('Gagal memuat riwayat:', err);
      }
    }

    function openEditProfilFromMenu() {
      const headingTitle = document.getElementById('ep-title-heading');
      const headingSub = document.getElementById('ep-subtitle-heading');
      const labelRumah = document.getElementById('ep-label-rumah');
      const previewImg = document.getElementById('ep-preview-foto');
      const previewIcon = document.getElementById('ep-preview-icon');

      uploadedFotoDataUrl = currentUserSession ? currentUserSession.foto : null;
      if (uploadedFotoDataUrl) {
        previewImg.src = uploadedFotoDataUrl;
        previewImg.classList.remove('hidden');
        previewIcon.classList.add('hidden');
      } else {
        previewImg.classList.add('hidden');
        previewIcon.classList.remove('hidden');
      }

      if (currentUserSession.role === 'pengurus') {
        headingTitle.innerText = "Edit Profil Pengurus RT";
        headingSub.innerText = "Perbarui profil dan akses akun pengurus RT";
        labelRumah.innerText = "Jabatan / Wilayah RT";
      } else {
        headingTitle.innerText = "Edit Profil Warga";
        headingSub.innerText = "Perbarui data profil dan kata sandi Anda";
        labelRumah.innerText = "Nomor Rumah / Blok";
      }

      document.getElementById('ep-nama').value = currentUserSession.nama || "";
      document.getElementById('ep-rumah').value = currentUserSession.rumah || "";
      document.getElementById('ep-wa').value = currentUserSession.wa || "";
      document.getElementById('ep-username').value = currentUserSession.username || "";
      document.getElementById('ep-password').value = "";

      navigate('edit-profil');
    }

    async function simpanProfilUser(e) {
      e.preventDefault();
      const namaBaru = document.getElementById('ep-nama').value.trim();
      const rumahJabatanBaru = document.getElementById('ep-rumah').value.trim();
      const waBaru = document.getElementById('ep-wa').value.trim();
      const userBaru = document.getElementById('ep-username').value.trim();
      const passBaru = document.getElementById('ep-password').value.trim();

      const payload = {
        nama: namaBaru,
        rumah: rumahJabatanBaru,
        wa: waBaru,
        username: userBaru
      };
      if (passBaru !== "") payload.password = passBaru;

      // Tunggu kompresi foto selesai dulu. Tanpa ini, menekan tombol simpan
      // saat kompresi masih berjalan akan mengirim data URL mentah yang
      // ukurannya jauh lebih besar dari batas server.
      if (uploadedFotoProses) {
        try {
          await uploadedFotoProses;
        } catch (err) {
          // previewUploadedFoto sudah memakai berkas mentah sebagai cadangan,
          // jadi di sini cukup lanjut saja.
        }
      }
      if (uploadedFotoDataUrl) payload.foto = uploadedFotoDataUrl;

      try {
        const res = await apiFetch('/profile', {
          method: 'PUT',
          body: JSON.stringify(payload)
        });

        if (res.success) {
          currentUserSession = res.user;
          document.getElementById('user-display-name').innerText = namaBaru;
          document.getElementById('dropdown-user-name').innerText = namaBaru;
          applyUserProfileImage(res.user.foto);

          const greeting = getGreetingByTime();
          document.getElementById('warga-greeting-text').innerText = greeting + ', ' + namaBaru;

          showModernModal('success', 'Profil Diperbarui', 'Data profil berhasil disimpan', () => {
            navigate(previousActivePage || 'dashboard-warga');
          });
        } else {
          showModernModal('danger', 'Gagal', res.message || 'Gagal menyimpan profil.');
        }
      } catch (err) {
        showModernModal('danger', 'Gagal', 'Terjadi kesalahan jaringan.');
      }
    }

    function showModernModal(type, title, message, onConfirm = null) {
      const modal = document.getElementById('modal-notif');
      const cardBox = document.getElementById('notif-card-box');
      const iconBox = document.getElementById('notif-icon-box');
      const titleEl = document.getElementById('notif-title');
      const msgEl = document.getElementById('notif-message');
      const actionsEl = document.getElementById('notif-actions');

      titleEl.innerText = title;
      msgEl.innerText = message;

      cardBox.className = 'bg-white dark:bg-darkcard rounded-3xl border border-slate-100 dark:border-emerald-800/80 p-6 w-full max-w-sm space-y-4 shadow-2xl text-center animate-modal-pop transition-colors';

      if (type === 'login-failed' || type === 'danger') {
        cardBox.className = 'bg-rose-50 dark:bg-rose-950/90 rounded-3xl border-2 border-rose-500 p-6 w-full max-w-sm space-y-4 shadow-2xl text-center animate-modal-pop';
        iconBox.className = 'w-12 h-12 rounded-2xl flex items-center justify-center mx-auto bg-rose-500 text-white shadow-md';
        iconBox.innerHTML = '<i data-lucide="alert-circle" class="w-6 h-6"></i>';
        
        actionsEl.innerHTML = '<button id="modal-btn-ok" class="w-full py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs active-press shadow-sm">Mengerti</button>';
        document.getElementById('modal-btn-ok').onclick = () => {
          closeModernModal();
          if (onConfirm) onConfirm();
        };
      } else if (type === 'success') {
        iconBox.className = 'w-12 h-12 rounded-2xl flex items-center justify-center mx-auto bg-emerald-100 dark:bg-brand-950 text-brand-600 dark:text-brand-300 border border-transparent dark:border-emerald-800';
        iconBox.innerHTML = '<i data-lucide="check-circle-2" class="w-6 h-6"></i>';
        
        actionsEl.innerHTML = '<button id="modal-btn-ok" class="w-full py-2.5 bg-brand-600 text-white font-bold rounded-xl text-xs active-press shadow-sm">OK</button>';
        document.getElementById('modal-btn-ok').onclick = () => {
          closeModernModal();
          if (onConfirm) onConfirm();
        };
      } else {
        iconBox.className = 'w-12 h-12 rounded-2xl flex items-center justify-center mx-auto bg-amber-100 dark:bg-amber-950 text-amber-600 dark:text-amber-400 border border-transparent dark:border-amber-800';
        iconBox.innerHTML = '<i data-lucide="help-circle" class="w-6 h-6"></i>';

        actionsEl.innerHTML = '<button id="modal-btn-cancel" class="flex-1 py-2.5 bg-slate-100 dark:bg-brand-950 text-slate-700 dark:text-emerald-200 font-bold rounded-xl text-xs active-press">Batal</button><button id="modal-btn-ok" class="flex-1 py-2.5 bg-brand-600 text-white font-bold rounded-xl text-xs active-press shadow-sm">Ya</button>';
        document.getElementById('modal-btn-cancel').onclick = closeModernModal;
        document.getElementById('modal-btn-ok').onclick = () => {
          closeModernModal();
          if (onConfirm) onConfirm();
        };
      }

      modal.classList.remove('hidden');
      renderIkon();
    }

    function closeModernModal() {
      document.getElementById('modal-notif').classList.add('hidden');
    }

    // Chart initialization functions
    function initChartBulanan(chartData) {
      const ctx = document.getElementById('chartBulananWarga');
      if (!ctx) return;
      if (chartBulananWargaInstance) chartBulananWargaInstance.destroy();
      
      const isDark = document.documentElement.classList.contains('dark');
      const gridColor = isDark ? 'rgba(6, 95, 70, 0.4)' : 'rgba(226, 232, 240, 0.6)';
      const textColor = isDark ? '#a7f3d0' : '#64748b';

      chartBulananWargaInstance = new Chart(ctx.getContext('2d'), {
        type: 'bar',
        data: {
          labels: chartData.labels,
          datasets: [{
            label: 'Kontribusi Pilah',
            data: chartData.data,
            backgroundColor: '#059669',
            borderRadius: 6,
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          animation: { duration: 1000, easing: 'easeInOutQuart' },
          plugins: {
            legend: { display: false },
            // Tooltip bawaan Chart.js menulis "label: nilai", jadi hasilnya
            // "Kontribusi Pilah (Kg): 12". Satuan Kg dipindah ke akhir kalimat
            // supaya terbaca sebagai bagian dari nilainya, bukan dari judul.
            tooltip: {
              callbacks: {
                // Judul bawaan Chart.js berisi nama bulan ("Sep"). Bulan itu
                // sudah terlihat dari bar yang sedang disorot dan dari sumbu X,
                // jadi tidak perlu ditulis ulang di dalam tooltip.
                title: () => '',
                label: (ctx) => 'Kontribusi Pilah : ' + ANGKA_DESIMAL.format(ctx.parsed.y) + ' Kg'
              }
            }
          },
          scales: {
            y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor } },
            x: { grid: { display: false }, ticks: { color: textColor } }
          }
        }
      });
    }

    function initChartProporsi(chartData) {
      const ctx = document.getElementById('chartProporsiWarga');
      if (!ctx) return;
      if (chartProporsiWargaInstance) chartProporsiWargaInstance.destroy();
      
      const isDark = document.documentElement.classList.contains('dark');
      const textColor = isDark ? '#a7f3d0' : '#64748b';

      chartProporsiWargaInstance = new Chart(ctx.getContext('2d'), {
        type: 'doughnut',
        data: {
          labels: chartData.labels,
          datasets: [{
            data: chartData.data,
            backgroundColor: chartData.colors
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          animation: { duration: 1000, easing: 'easeInOutQuart' },
          plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10, weight: 'bold' }, color: textColor } },
            // Porsi lebih masuk akal ditampilkan sebagai persen. Kalau
            // ditampilkan dalam Kg, warga sulit membandingkan kategorinya
            // karena satuan yang sama dipakai di semua irisan.
            tooltip: {
              callbacks: {
                // Chart.js secara bawaan mencetak nama kategori sebagai judul
                // tooltip (baris tebal). Karena baris `label` di bawah sudah
                // memuat nama itu juga, kategori jadi tertulis dua kali.
                // Judul dikosongkan supaya hanya satu baris yang tampil.
                title: () => '',
                label: (ctx) => {
                  const total = ctx.dataset.data.reduce((jumlah, nilai) => jumlah + nilai, 0);
                  const persen = total > 0 ? (ctx.parsed / total) * 100 : 0;

                  return ctx.label + ' : ' + ANGKA_DESIMAL.format(persen) + '%';
                }
              }
            }
          }
        }
      });
    }

    function initChartTrenRT(chartData) {
      const ctx = document.getElementById('chartTrenRT');
      if (!ctx) return;
      if (chartTrenRTInstance) chartTrenRTInstance.destroy();
      
      const isDark = document.documentElement.classList.contains('dark');
      const gridColor = isDark ? 'rgba(6, 95, 70, 0.4)' : 'rgba(226, 232, 240, 0.6)';
      const textColor = isDark ? '#a7f3d0' : '#64748b';

      chartTrenRTInstance = new Chart(ctx.getContext('2d'), {
        type: 'bar',
        data: {
          labels: chartData.labels,
          datasets: [{
            label: 'Total Sampah RT',
            data: chartData.data,
            backgroundColor: '#10b981',
            borderRadius: 6,
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          animation: { duration: 1000, easing: 'easeInOutQuart' },
          plugins: {
            legend: { display: false },
            // Sama seperti bar chart warga: nama bulan tidak ditulis di
            // tooltip, satuan Kg pindah ke akhir kalimat.
            tooltip: {
              callbacks: {
                title: () => '',
                label: (ctx) => 'Total Sampah RT : ' + ANGKA_DESIMAL.format(ctx.parsed.y) + ' Kg'
              }
            }
          },
          scales: {
            y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor } },
            x: { grid: { display: false }, ticks: { color: textColor } }
          }
        }
      });
    }

    function initChartKomposisiRT(chartData) {
      const ctx = document.getElementById('chartKomposisiRT');
      if (!ctx) return;
      if (chartKomposisiRTInstance) chartKomposisiRTInstance.destroy();
      
      const isDark = document.documentElement.classList.contains('dark');
      const textColor = isDark ? '#a7f3d0' : '#64748b';

      chartKomposisiRTInstance = new Chart(ctx.getContext('2d'), {
        type: 'pie',
        data: {
          labels: chartData.labels,
          datasets: [{
            data: chartData.data,
            backgroundColor: chartData.colors
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          animation: { duration: 1000, easing: 'easeInOutQuart' },
          plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10, weight: 'bold' }, color: textColor } },
            // Sama seperti pie warga: judul dikosongkan supaya nama kategori
            // tidak tertulis dua kali, dan nilainya ditulis sebagai persen.
            tooltip: {
              callbacks: {
                title: () => '',
                label: (ctx) => {
                  const total = ctx.dataset.data.reduce((jumlah, nilai) => jumlah + nilai, 0);
                  const persen = total > 0 ? (ctx.parsed / total) * 100 : 0;

                  return ctx.label + ' : ' + ANGKA_DESIMAL.format(persen) + '%';
                }
              }
            }
          }
        }
      });
    }

    function setAturanKataSandi(isTambah) {
      const input = document.getElementById('mw-password');
      input.required = isTambah;
      input.placeholder = isTambah ? 'sandi' : 'Kosongkan jika tidak diubah';
    }

    function openModalTambahWarga() {
      document.getElementById('mw-title').innerHTML = '<i data-lucide="user-plus" class="w-5 h-5 text-brand-600 dark:text-brand-400"></i> Tambah Warga';
      document.getElementById('mw-id').value = '';
      document.getElementById('mw-nama').value = '';
      document.getElementById('mw-rumah').value = '';
      document.getElementById('mw-wa').value = '';
      document.getElementById('mw-username').value = '';
      document.getElementById('mw-password').value = '';
      setAturanKataSandi(true);
      document.getElementById('modal-warga-form').classList.remove('hidden');
      renderIkon();
    }

    function openModalEditWarga(id) {
      const w = listWarga.find(item => item.id === id);
      if(!w) return;

      document.getElementById('mw-title').innerHTML = '<i data-lucide="user-cog" class="w-5 h-5 text-brand-600 dark:text-brand-400"></i> Edit Warga';
      document.getElementById('mw-id').value = w.id;
      document.getElementById('mw-nama').value = w.nama;
      document.getElementById('mw-rumah').value = w.rumah;
      document.getElementById('mw-wa').value = w.wa;
      document.getElementById('mw-username').value = w.username;
      document.getElementById('mw-password').value = '';
      setAturanKataSandi(false);
      document.getElementById('modal-warga-form').classList.remove('hidden');
      renderIkon();
    }

    function closeModalWarga() {
      document.getElementById('modal-warga-form').classList.add('hidden');
    }

    async function simpanWargaForm(e) {
      e.preventDefault();
      const id = document.getElementById('mw-id').value;
      const payload = {
        nama: document.getElementById('mw-nama').value,
        rumah: document.getElementById('mw-rumah').value,
        wa: document.getElementById('mw-wa').value,
        username: document.getElementById('mw-username').value,
        password: document.getElementById('mw-password').value
      };

      try {
        if (id) {
          await apiFetch('/warga/' + id, { method: 'PUT', body: JSON.stringify(payload) });
          showModernModal('success', 'Data Diperbarui', 'Data ' + payload.nama + ' berhasil disimpan');
        } else {
          await apiFetch('/warga', { method: 'POST', body: JSON.stringify(payload) });
          showModernModal('success', 'Warga Ditambah', 'Akun ' + payload.nama + ' berhasil didaftarkan');
        }

        // Reload warga data
        const res = await apiFetch('/warga');
        if (res.data) listWarga = res.data;
        
        renderDaftarWarga();
        renderTop3Warga(listWarga);
        closeModalWarga();
      } catch (err) {
        // Pakai pesan dari server (mis. "username sudah digunakan" atau
        // "kolom password wajib diisi") supaya penyebab gagalnya jelas.
        showModernModal('danger', 'Gagal', err.message || 'Gagal menyimpan data warga.');
      }
    }

    async function hapusWarga(id) {
      const w = listWarga.find(item => item.id === id);
      if(!w) return;

      showModernModal('confirm', 'Hapus Warga', 'Hapus akun ' + w.nama
        + '? ', async () => {
        try {
          const res = await apiFetch('/warga/' + id, { method: 'DELETE' });
          listWarga = listWarga.filter(item => item.id !== id);
          renderDaftarWarga();
          renderTop3Warga(listWarga);

          // Laporkan berapa transaksi yang ikut terhapus.
          const t = (res.data && res.data.transaksi_dihapus) || {};
          const total = (t.setoran || 0) + (t.penukaran || 0) + (t.riwayat || 0);
          const info = total > 0
            ? ' Beserta ' + total + ' transaksi (setoran, kupon, riwayat).'
            : '';

          showModernModal('success', 'Terhapus', 'Data warga berhasil dihapus' + info);
        } catch (err) {
          showModernModal('danger', 'Gagal', 'Gagal menghapus data warga');
        }
      });
    }

    // Tombol Edit/Hapus dipakai dua kali: di kolom Aksi tabel dan di baris
    // bawah kartu HP. Dipisah supaya perubahannya tidak harus diulang di
    // dua tempat dan tidak sampai berbeda.
    function wargaAksiButtons(w) {
        // Ukuran 36x36 px (p-2.5 + ikon w-4). Sebelumnya 28x28 px, terlalu
        // kecil untuk jari di layar sentuh: panduan aksesibilitas menyarankan
        // area sentuh minimal 44x44 px, dan 28 px gampang diketuk keliru.
        return '<button onclick="openModalEditWarga(' + w.id + ')" title="Edit" aria-label="Edit data warga" class="p-2.5 bg-slate-100 dark:bg-brand-950 hover:bg-slate-200 dark:hover:bg-brand-900 text-slate-700 dark:text-emerald-200 rounded-xl active-press inline-flex items-center justify-center"><i data-lucide="pencil" class="w-4 h-4"></i></button>' +
            '<button onclick="hapusWarga(' + w.id + ')" title="Hapus" aria-label="Hapus warga" class="p-2.5 bg-rose-100 dark:bg-rose-950 hover:bg-rose-200 dark:hover:bg-brand-900 text-rose-600 dark:text-rose-400 rounded-xl active-press inline-flex items-center justify-center"><i data-lucide="trash-2" class="w-4 h-4"></i></button>';
    }

    function renderDaftarWarga(filterQuery) {
        const tbody = document.getElementById('tbl-daftar-warga-body');
        const cards = document.getElementById('cards-daftar-warga');
        if (!tbody && !cards) return;
        document.getElementById('total-warga-count').innerText = listWarga.length;

        const q = (filterQuery || '').toLowerCase();
        const filtered = listWarga.filter(w => {
            const fullText = (w.nama + ' ' + w.rumah + ' ' + w.wa + ' ' + w.username).toLowerCase();
            return fullText.includes(q);
        });

        // Urutkan berdasarkan nomor rumah
        filtered.sort((a, b) => {
            const rumahA = (a.rumah || '').toString().toLowerCase();
            const rumahB = (b.rumah || '').toString().toLowerCase();
            return rumahA.localeCompare(rumahB, undefined, { numeric: true, sensitivity: 'base' });
        });

        const kosongWarga = '<div class="rounded-2xl border border-dashed border-slate-200 dark:border-emerald-800/70 p-6 text-center text-xs font-medium text-slate-400 dark:text-emerald-400">Data warga tidak ditemukan.</div>';

        if (filtered.length === 0) {
            if (tbody) tbody.innerHTML = '<tr class="empty-state-row"><td colspan="6" class="py-6 text-center text-slate-400 dark:text-emerald-400 font-medium">Data warga tidak ditemukan.</td></tr>';
            if (cards) cards.innerHTML = kosongWarga;
            return;
        }

        if (tbody) {
            tbody.innerHTML = filtered.map(w =>
                '<tr class="hover:bg-slate-50 dark:hover:bg-brand-950/60 transition-colors">' +
                    '<td class="py-3 text-center font-extrabold text-slate-900 dark:text-white">' + w.nama + '</td>' +
                    '<td class="py-3 text-center text-slate-500 dark:text-emerald-300 font-semibold">' + (w.rumah || '-') + '</td>' +
                    '<td class="py-3 text-center text-emerald-600 dark:text-emerald-400 font-bold">' + (w.wa || '-') + '</td>' +
                    // Tanpa "@" supaya sama persis dengan yang diketik di form Akses Akun.
                    '<td class="py-3 text-center font-sans text-brand-600 dark:text-brand-300 font-extrabold">' + (w.username || '-') + '</td>' +
                    '<td class="py-3 text-center font-extrabold text-brand-600 dark:text-brand-300">' + w.poin + ' pts</td>' +
                    '<td class="py-3 text-center">' +
                        // Dibungkus inline-flex agar dua tombol tetap rapat berdampingan
                        // di tengah kolom, bukan berdesakan ke kanan.
                        '<div class="inline-flex items-center gap-1">' + wargaAksiButtons(w) + '</div>' +
                    '</td>' +
                '</tr>'
            ).join('');
        }

        // Kartu untuk HP, tiga baris. Ketiganya terisi penuh supaya tidak ada
        // ruang kosong: baris 1 nama + poin, baris 2 nomor WA saja, baris 3
        // tombol Edit/Hapus + nomor rumah di kanan.
        //
        // Username SENGAJA tidak ditampilkan di kartu. Di HP ruangnya sempit
        // dan username bukan data yang perlu dipindai petugas; kolomnya
        // masih ada di tabel untuk layar lebar.
        if (cards) {
            cards.innerHTML = filtered.map(w =>
                '<div class="warga-card rounded-2xl border border-slate-200/80 dark:border-emerald-800/70 bg-slate-50/70 dark:bg-brand-950/40 p-3.5 space-y-2">' +
                    // Baris 1: nama di kiri, total poin di paling kanan.
                    '<div class="flex items-center justify-between gap-2">' +
                        '<p class="text-sm font-extrabold text-slate-900 dark:text-white leading-tight min-w-0 break-words">' + w.nama + '</p>' +
                        '<span class="shrink-0 font-extrabold text-xs text-brand-600 dark:text-brand-300">' + w.poin + ' pts</span>' +
                    '</div>' +
                    // Baris 2: nomor WA apa adanya, tanpa label.
                    '<p class="text-xs text-slate-500 dark:text-emerald-300/80 leading-relaxed break-words">' + (w.wa || '-') + '</p>' +
                    // Baris 3: tombol di kiri, nomor rumah di paling kanan.
                    '<div class="flex items-center justify-between gap-2">' +
                        '<div class="flex items-center gap-2">' + wargaAksiButtons(w) + '</div>' +
                        '<span class="shrink-0 text-[10px] font-bold text-slate-500 dark:text-emerald-300 bg-slate-100 dark:bg-brand-900/60 rounded-md px-1.5 py-0.5">' + (w.rumah || '-') + '</span>' +
                    '</div>' +
                '</div>'
            ).join('');
        }
        renderIkon();
    }

   
    function kartuKatalog(item, aksiHtml) {
      const isStokHampirHabis = item.stok > 0 && item.stok <= 3;

      return '<div class="border border-slate-200/80 dark:border-emerald-800/80 rounded-xl p-4 space-y-2 bg-slate-50/50 dark:bg-brand-950/60 shadow-sm flex flex-col justify-between">' +
        '<div>' +
          '<div class="flex justify-between items-start gap-2">' +
            '<h3 class="font-extrabold text-slate-900 dark:text-white text-xs">' + item.nama + '</h3>' +
            (isStokHampirHabis ? '<span class="px-2 py-0.5 bg-rose-100 dark:bg-rose-950 text-rose-600 dark:text-rose-400 font-extrabold text-[9px] rounded-md border border-rose-200 dark:border-rose-800 shrink-0">Stok hampir habis!</span>' : '') +
          '</div>' +
          '<p class="text-slate-400 dark:text-emerald-400 text-xs font-medium mt-0.5">' + item.ket + '</p>' +
          '<p class="text-[11px] font-bold ' + (item.stok > 0 ? 'text-slate-500 dark:text-emerald-300' : 'text-rose-500') + ' mt-1">Sisa Stok: ' + item.stok + '</p>' +
        '</div>' +
        '<div class="flex justify-between items-center pt-3 border-t border-slate-100 dark:border-emerald-800/60 mt-2">' +
          '<span class="font-black text-brand-600 dark:text-brand-300">' + item.poin + ' pts</span>' +
          aksiHtml +
        '</div>' +
      '</div>';
    }

    function renderKatalogWarga(filterQuery) {
      const container = document.getElementById('container-katalog-warga');
      if (!container) return;
      
      const q = (filterQuery || '').toLowerCase();
      const filtered = katalogRewards.filter(item => {
        const text = (item.nama + ' ' + item.ket).toLowerCase();
        return text.includes(q);
      });

      if (filtered.length === 0) {
        container.innerHTML = '<div class="col-span-full text-center py-8 text-slate-400 dark:text-emerald-400 font-medium">Hadiah tidak ditemukan.</div>';
        return;
      }

      container.innerHTML = filtered.map(item => {
        // Tombol pada tiap kartu sudah membawa keterangannya sendiri, jadi warga
        // tidak perlu menekan dulu untuk tahu apakah poinnya cukup atau tidak.
        // Dua alasan tidak bisa ditukar, keduanya ditulis apa adanya:
        //   - stok habis
        //   - poin warga masih kurang dari harga hadiah
        //
        // Notifikasi pop-up "Poin Kurang" sengaja dihilangkan. Kalau tombolnya
        // sudah tulis "Poin Kurang" dan tidak bisa diklik, pop-up hanya memaksa
        // warga menekan tombol yang memang tidak ada gunanya.
        const tidakBisaDitukar = 'px-3.5 py-1.5 bg-slate-200 dark:bg-emerald-950 text-slate-400 dark:text-emerald-600 border border-transparent dark:border-emerald-900 rounded-lg text-xs font-bold cursor-not-allowed';

        let aksi;
        if (item.stok <= 0) {
          aksi = '<button disabled class="' + tidakBisaDitukar + '">Stok Habis</button>';
        } else if (userPoin < item.poin) {
          aksi = '<button disabled class="' + tidakBisaDitukar + '">Poin Kurang</button>';
        } else {
          aksi = '<button onclick="konfirmasiTukarPoin(' + item.id + ')" class="px-3.5 py-1.5 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-xs font-bold shadow-sm active-press">Tukar</button>';
        }

        return kartuKatalog(item, aksi);
      }).join('');
      renderIkon();
    }

    function renderRewardPengurusList(filterQuery) {
      const container = document.getElementById('container-reward-pengurus-list');
      if(!container) return;

      const q = (filterQuery || '').toLowerCase();
      const filtered = katalogRewards.filter(item => {
        const text = (item.nama + ' ' + item.ket).toLowerCase();
        return text.includes(q);
      });

      if (filtered.length === 0) {
        container.innerHTML = '<div class="col-span-full text-center py-8 text-slate-400 dark:text-emerald-400 font-medium">Item hadiah tidak ditemukan</div>';
        return;
      }

      container.innerHTML = filtered.map(item => {
       
        const aksi = '<div class="flex gap-1.5">' +
          '<button onclick="editReward(' + item.id + ')" class="px-2.5 py-1.5 bg-slate-100 dark:bg-brand-950 hover:bg-slate-200 dark:hover:bg-brand-900 text-slate-700 dark:text-emerald-200 rounded-lg active-press font-bold inline-flex items-center gap-1"><i data-lucide="pencil" class="w-3.5 h-3.5 text-brand-600 dark:text-brand-400"></i> Edit</button>' +
          '<button onclick="hapusReward(' + item.id + ')" class="px-2.5 py-1.5 bg-rose-100 dark:bg-rose-950 hover:bg-rose-200 dark:hover:bg-rose-900 text-rose-600 dark:text-rose-400 rounded-lg active-press font-bold inline-flex items-center gap-1"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus</button>' +
        '</div>';

        return kartuKatalog(item, aksi);
      }).join('');
      renderIkon();
    }

    function editReward(id) {
      // id bisa datang sebagai number (dari onclick) atau string (dari form),
      // jadi samakan tipe dulu sebelum dibandingkan.
      const item = katalogRewards.find(r => String(r.id) === String(id));

      if (!item) {
        // Daftar masih kosong sama sekali (user baru buka menu saat
        // loadRewards() masih berjalan). Tunggu dulu -- JANGAN buka form
        // kosong, karena id kosong nanti disalahartikan sebagai "tambah
        // baru" sehingga item yang diedit tidak ikut berubah.
        if (!katalogRewards.length && !rewardsLoaded) {
          loadRewards().then(() => {
            if (rewardsLoaded) editReward(id);
          });
          return;
        }

        // Daftar di layar sudah basi, muat ulang dari database.
        loadRewards().then(() => {
          renderRewardPengurusList();
          showModernModal('danger', 'Tidak Ditemukan', 'Item hadiah tersebut sudah tidak ada. Daftar telah dimuat ulang.');
        });
        return;
      }

      // Isi form langsung dari data yang sudah ada. Sengaja TIDAK menunggu
      // loadRewards() yang sedang berjalan: menunggu membuat form tetap
      // kosong sehingga simpanan user terbaca sebagai data baru.
      document.getElementById('reward-id').value = item.id;
      document.getElementById('reward-nama').value = item.nama;
      document.getElementById('reward-poin').value = item.poin;
      document.getElementById('reward-stok').value = item.stok;
      document.getElementById('reward-ket').value = item.ket;

      document.getElementById('reward-form-title').innerText = "Edit Item Hadiah";
      document.getElementById('btn-submit-reward').innerText = "Simpan Perubahan";
      document.getElementById('btn-cancel-reward-edit').classList.remove('hidden');
    }

    function resetFormReward() {
      document.getElementById('reward-id').value = '';
      document.getElementById('reward-nama').value = '';
      document.getElementById('reward-poin').value = '';
      document.getElementById('reward-stok').value = '';
      document.getElementById('reward-ket').value = '';

      document.getElementById('reward-form-title').innerText = "Tambah Item Hadiah";
      document.getElementById('btn-submit-reward').innerText = "Simpan Item";
      document.getElementById('btn-cancel-reward-edit').classList.add('hidden');
    }

    async function hapusReward(id) {
      const item = katalogRewards.find(r => String(r.id) === String(id));

      if (!item) {
        // Daftar masih kosong sama sekali -> tunggu datanya dulu, jangan
        // tampilkan dialog konfirmasi untuk data yang belum kita kenal.
        if (!katalogRewards.length && !rewardsLoaded) {
          loadRewards().then(() => {
            if (rewardsLoaded) hapusReward(id);
          });
          return;
        }

        loadRewards().then(() => {
          renderRewardPengurusList();
          showModernModal('danger', 'Tidak Ditemukan', 'Item hadiah tersebut sudah tidak ada. Daftar telah dimuat ulang.');
        });
        return;
      }

      showModernModal('confirm', 'Hapus Hadiah', 'Hapus ' + item.nama + ' dari katalog?'
        + ' Semua kupon barang ini ikut dibatalkan, dan poin yang sudah dipotong'
        + ' dikembalikan ke warga terkait.', async () => {
        try {
          const res = await apiFetch('/rewards/' + item.id, { method: 'DELETE' });

          // Hilangkan dari daftar yang SEDANG TAMPIL sebelum ambil ulang dari
          // database, supaya item yang baru saja dihapus langsung hilang
          // walaupun GET /rewards berikutnya gagal atau lambat.
          katalogRewards = katalogRewards.filter(r => String(r.id) !== String(item.id));
          renderRewardPengurusList();
          renderKatalogWarga();
          updatePoinDisplay();

          // Muat ulang dari database lewat loadRewards() agar respons basi
          // dari permintaan lama tidak bisa mengembalikan item ini ke layar.
          await loadRewards();

          renderRewardPengurusList();
          renderKatalogWarga();
          updatePoinDisplay();

          // Kupon barang ini sudah dibatalkan dan poinya dikembalikan oleh
          // server; laporkan angkanya lewat modal supaya petugas tahu.
          const kuponHapus = (res.data && res.data.kupon_terhapus) || 0;
          const poinKembali = (res.data && res.data.poin_dikembalikan) || 0;
          const info = kuponHapus > 0
            ? ' ' + kuponHapus + ' kupon ikut dibatalkan' + (poinKembali > 0 ? ' dan ' + poinKembali + ' poin dikembalikan ke warga.' : '.')
            : '';

          showModernModal('success', 'Terhapus', 'Item hadiah berhasil dihapus' + info);
        } catch (err) {
          if (err.status === 404) {
            await loadRewards();
            renderRewardPengurusList();
          }
          showModernModal('danger', 'Gagal', err.message || 'Gagal menghapus hadiah');
        }
      });
    }

    async function handleTambahReward(e) {
      e.preventDefault();
      const id = document.getElementById('reward-id').value;
      const poin = parseInt(document.getElementById('reward-poin').value);
      const stok = parseInt(document.getElementById('reward-stok').value);

      // Validasi di sisi klien supaya tidak mengirim request yang pasti gagal.
      if (isNaN(poin) || poin < 0) {
        showModernModal('danger', 'Gagal', 'Poin hadiah harus berupa angka dan tidak boleh negatif.');
        return;
      }

      if (isNaN(stok) || stok < 0) {
        showModernModal('danger', 'Gagal', 'Jumlah stok harus berupa angka dan tidak boleh negatif.');
        return;
      }

      const payload = {
        nama: document.getElementById('reward-nama').value,
        poin: poin,
        stok: stok,
        ket: document.getElementById('reward-ket').value
      };

      try {
        const sedangEdit = id !== '' && id !== null && id !== undefined;

        // Form masih dalam mode Edit tapi id kosong -- artinya form ter-reset
        // di tengah jalan. Kalau dibiarkan, request ini jadi TAMBAH baru dan
        // item yang diedit tidak berubah sama sekali.
        if (!sedangEdit && document.getElementById('reward-form-title').innerText === 'Edit Item Hadiah') {
          showModernModal('danger', 'Gagal', 'Sesi edit terputus sehingga id item hilang. Silakan klik tombol Edit sekali lagi.');
          return;
        }

        let hasilSimpan = null;

        if (sedangEdit) {
          // Pastikan id benar-benar masih ada di katalog yang dimuat
          // terbaru, supaya tidak mengirim request ke data yang sudah hilang.
          const masihAda = katalogRewards.some(r => String(r.id) === String(id));
          if (!masihAda) {
            await loadRewards();
            resetFormReward();
            renderRewardPengurusList();
            showModernModal('danger', 'Gagal', 'Item yang Anda edit sudah tidak ada. Daftar telah dimuat ulang, silakan pilih item lain.');
            return;
          }

          hasilSimpan = await apiFetch('/rewards/' + id, { method: 'PUT', body: JSON.stringify(payload) });
        } else {
          hasilSimpan = await apiFetch('/rewards', { method: 'POST', body: JSON.stringify(payload) });
        }

        // Terapkan hasil server ke daftar yang SEDANG TAMPIL lebih dulu, baru
        // ambil ulang dari database. Dengan begitu data yang tampil ikut
        // berubah seketika setelah simpanan berhasil -- bahkan kalau GET
        // /rewards berikutnya gagal atau lambat, user tidak lagi melihat
        // data lama padahal pesan "Berhasil" sudah muncul.
        if (hasilSimpan && hasilSimpan.data) {
          const baru = hasilSimpan.data;
          const itemBaru = {
            id: baru.id,
            nama: baru.nama,
            poin: Number(baru.poin),
            stok: Number(baru.stok),
            ket: baru.ket
          };
          const posisi = katalogRewards.findIndex(r => String(r.id) === String(baru.id));
          if (posisi >= 0) katalogRewards[posisi] = itemBaru;
          else katalogRewards.push(itemBaru);
          rewardsLoaded = true;

          renderRewardPengurusList();
          renderKatalogWarga();
          updatePoinDisplay();
        }

        // Muat ulang data hadiah dari database sebagai penyesuaian akhir.
        // Lewat loadRewards() supaya respons basi dari permintaan yang lebih
        // lama tidak dapat menimpa perubahan yang baru saja disimpan.
        await loadRewards();

        resetFormReward();
        renderRewardPengurusList();
        renderKatalogWarga();
        updatePoinDisplay();

        showModernModal('success', 'Berhasil', sedangEdit ? 'Item hadiah diperbarui' : 'Hadiah baru ditambahkan');
      } catch (err) {
        // 404 = data yang diedit sudah tidak ada. Segarkan daftar supaya
        // user tidak melihat data basi lagi.
        if (err.status === 404) {
          await loadRewards();
          resetFormReward();
          renderRewardPengurusList();
        }
        showModernModal('danger', 'Gagal', err.message || 'Gagal menyimpan hadiah.');
      }
    }

    async function konfirmasiTukarPoin(rewardId) {
      const item = katalogRewards.find(r => r.id === rewardId);
      if (!item) return;

      // Katalog sudah menandai hadiah yang tidak terjangkau dengan tombol
      // "Poin Kurang" / "Stok Habis" yang tidak bisa diklik, jadi warga tidak
      // pernah sampai ke sini untuk hadiah yang memang belum bisa diambil.
      // Dua pengecekan di bawah hanya jaring pengaman kalau data di layar
      // basi -- misalnya poin berubah karena dipakai petugas dari akun lain.
      // Keduanya diam-diam membatalkan aksi; pesan aslinya sudah tercetak
      // di tombolnya, jadi tidak perlu diulang lewat pop-up lagi.
      if (item.stok <= 0 || userPoin < item.poin) return;

      showModernModal('confirm', 'Tukar Hadiah', 'Tukar ' + item.poin + ' poin dengan ' + item.nama + '?', async () => {
        try {
          const res = await apiFetch('/rewards/' + rewardId + '/tukar', { method: 'POST' });
          
          if (res.success) {
            userPoin -= item.poin;
            item.stok -= 1;
            
            // Reload data
            await loadInitialData();
            await loadKategoriSampah();
            
            updatePoinDisplay();
            renderKatalogWarga();
            renderRewardPengurusList();
            renderKuponPengurus();
            
            showModernModal('success', 'Berhasil', res.message || 'Kupon ' + item.nama + ' telah terbit Silakan ambil di Balai RT');
          } else {
            showModernModal('danger', 'Gagal', res.message || 'Gagal menukar poin');
          }
        } catch (err) {
          showModernModal('danger', 'Gagal', 'Terjadi kesalahan jaringan');
        }
      });
    }

    async function loadKuponPengurus() {
      const seq = ++kuponFetchSeq;
      try {
        const res = await apiFetch('/verifikasi/kupon');

        // Respons lama tidak boleh menimpa daftar yang lebih baru.
        if (seq !== kuponFetchSeq) return;

        if (res.data) listKuponVerifikasi = res.data;
      } catch (err) {
        console.warn('Gagal memuat kupon:', err);
      } finally {
        // Render SETELAH data sampai. Tanpa ini halaman Verifikasi Kupon
        // selalu menggambar daftar lama (hasil load saat login), sehingga
        // kupon baru hasil penukaran warga tidak pernah muncul.
        if (seq === kuponFetchSeq) {
          const input = document.getElementById('global-search-input');
          renderKuponPengurus(input ? input.value : '');
        }
      }
    }

    /**
     * Muat ulang daftar warga dari database.
     *
     * Dipanggil setiap kali menu "Kelola Warga" dibuka. Tanpa ini, listWarga
     * hanya berisi hasil pemuatan saat login, jadi warga yang baru ditambahkan
     * atau datanya baru diubah (dari tab lain / perangkat lain) tidak terlihat
     * sampai browser di-refresh.
     */
    async function loadWarga() {
      const seq = ++wargaFetchSeq;
      try {
        const res = await apiFetch('/warga');

        // Jangan timpa dengan respons basi.
        if (seq !== wargaFetchSeq) return;

        if (res.data) listWarga = res.data;
      } catch (err) {
        console.warn('Gagal memuat daftar warga:', err);
      } finally {
        // Render setelah data sampai, bukan sebelum, supaya tidak sempat
        // menggambar daftar lama. Isi pencarian yang sedang diketik ikut
        // dipertahankan supaya ketikan pengguna tidak hilang.
        if (seq === wargaFetchSeq) {
          const input = document.getElementById('global-search-input');
          renderDaftarWarga(input ? input.value : '');
        }
      }
    }

    /**
     * Muat ulang antrean verifikasi setoran.
     *
     * Sama seperti loadWarga: tanpa pemuatan ulang, setoran baru dari warga
     * tidak akan muncul di menu "Verifikasi" sampai browser di-refresh, dan
     * angka antrean di sidebar ikut basi.
     */
    async function loadVerifikasiSetoran() {
      const seq = ++verifikasiFetchSeq;
      try {
        const res = await apiFetch('/verifikasi/setoran');

        if (seq !== verifikasiFetchSeq) return;

        if (res.data) verifikasiPending = res.data;
      } catch (err) {
        console.warn('Gagal memuat antrean verifikasi:', err);
      } finally {
        if (seq === verifikasiFetchSeq) {
          const input = document.getElementById('global-search-input');
          renderVerifikasi(input ? input.value : '');
        }
      }
    }

    // Badge status dan tombol aksi dipakai dua kali: di kolom tabel dan di
    // kartu HP. Dipisah supaya keduanya tidak sampai berbeda.
    function kuponStatusBadge(diambil) {
        return diambil
            ? '<span class="px-2.5 py-1 bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 font-extrabold text-[10px] rounded-full border border-transparent dark:border-emerald-800">Sudah Diambil</span>'
            : '<span class="px-2.5 py-1 bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 font-extrabold text-[10px] rounded-full border border-transparent dark:border-amber-800/60">Belum Diambil</span>';
    }

    // Tombol aksi kupon. Tanpa argumen `kartu` hasilnya untuk tabel desktop
    // (kecil, lebar natural). Dengan `kartu = true` ukurannya dinaikkan dan
    // melebar supaya enak ditek di layar HP.
    function kuponAksiButton(k, kartu) {
        const aksi = 'toggleStatusKupon(\'' + k.id + '\', ' + (k.diambil ? 'false' : 'true') + ')';
        const label = k.diambil ? 'Batalkan' : 'Konfirmasi Ambil';
        const ikon = k.diambil ? 'rotate-ccw' : 'check';

        if (kartu) {
            return '<button onclick="' + aksi + '" class="flex-1 justify-center rounded-xl px-4 py-2.5 font-bold text-sm active-press shadow-sm inline-flex items-center gap-1.5 ' +
                (k.diambil
                    ? 'bg-slate-100 dark:bg-brand-950 text-slate-600 dark:text-emerald-300 border border-slate-200 dark:border-emerald-800'
                    : 'bg-brand-600 hover:bg-brand-700 text-white') +
                '"><i data-lucide="' + ikon + '" class="w-4 h-4"></i> ' + label + '</button>';
        }

        return k.diambil
            ? '<button onclick="' + aksi + '" class="px-3 py-1.5 bg-slate-100 dark:bg-brand-950 text-slate-600 dark:text-emerald-300 rounded-lg font-bold text-xs active-press shadow-sm border border-transparent dark:border-emerald-800 inline-flex items-center gap-1"><i data-lucide="' + ikon + '" class="w-3.5 h-3.5"></i> ' + label + '</button>'
            : '<button onclick="' + aksi + '" class="px-3 py-1.5 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-bold text-xs active-press shadow-sm inline-flex items-center gap-1"><i data-lucide="' + ikon + '" class="w-3.5 h-3.5"></i> ' + label + '</button>';
    }

    function renderKuponPengurus(filterQuery) {
        const tbody = document.getElementById('tbl-verifikasi-kupon-body');
        const cards = document.getElementById('cards-verifikasi-kupon');
        if (!tbody && !cards) return;

        const pendingKupon = listKuponVerifikasi.filter(k => !k.diambil).length;
        const kuponBadge = document.getElementById('badge-kupon-pending-count');
        if (kuponBadge) kuponBadge.innerText = pendingKupon;

        const q = (filterQuery || '').toLowerCase();
        const filtered = listKuponVerifikasi.filter(k => {
            const text = (k.kode_kupon + ' ' + k.nama + ' ' + k.rumah + ' ' + k.barang).toLowerCase();
            return text.includes(q);
        });

        const kosongKupon = '<div class="rounded-2xl border border-dashed border-slate-200 dark:border-emerald-800/70 p-6 text-center text-xs font-medium text-slate-400 dark:text-emerald-400">Data kupon tidak ditemukan</div>';

        if (filtered.length == 0) {
            if (tbody) tbody.innerHTML = '<tr class="empty-state-row"><td colspan="6" class="py-6 text-center text-slate-400 dark:text-emerald-400 font-medium">Data kupon tidak ditemukan</td></tr>';
            if (cards) cards.innerHTML = kosongKupon;
            return;
        }

        if (tbody) {
            tbody.innerHTML = filtered.map(k =>
                '<tr class="hover:bg-slate-50 dark:hover:bg-brand-950/60 transition-colors">' +
                    // Semua kolom dibuat center supaya isi tabel terlihat rapi.
                    '<td class="py-3.5 text-center font-sans font-bold text-slate-900 dark:text-white text-xs">' + k.kode_kupon + '</td>' +
                    '<td class="py-3.5 text-center font-extrabold text-slate-900 dark:text-white">' + k.nama + ' <span class="text-slate-400 dark:text-emerald-400 font-normal">(' + (k.rumah || '-') + ')</span></td>' +
                    '<td class="py-3.5 text-center font-bold text-slate-700 dark:text-emerald-200">' + k.barang + '</td>' +
                    '<td class="py-3.5 text-center text-slate-400 dark:text-emerald-300">' + k.tgl + '</td>' +
                    '<td class="py-3.5 text-center">' + kuponStatusBadge(k.diambil) + '</td>' +
                    '<td class="py-3.5 text-center">' + kuponAksiButton(k) + '</td>' +
                '</tr>'
            ).join('');
        }

        // Kartu untuk HP. Susunannya dari atas: tanggal penukaran di kiri dan
        // status di kanan, nama warga sebagai judul, jenis hadiah sebagai
        // detail, lalu tombol aksi melebar dengan kode kupon di sisinya.
        // Nomor rumah sengaja tidak ditampilkan di sini supaya judul kartu
        // tetap ringkas saat petugas menyisir antrean -- nomornya masih ada
        // di kolom "Nama Warga" pada tabel desktop.
        if (cards) {
            cards.innerHTML = filtered.map(k =>
                '<div class="kupon-card rounded-2xl border border-slate-200/80 dark:border-emerald-800/70 bg-slate-50/70 dark:bg-brand-950/40 p-4 space-y-2.5">' +
                    '<div class="flex items-start justify-between gap-3">' +
                        '<p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-emerald-400 pt-1">' + k.tgl + '</p>' +
                        '<div class="shrink-0">' + kuponStatusBadge(k.diambil) + '</div>' +
                    '</div>' +
                    '<p class="text-sm font-extrabold text-slate-900 dark:text-white leading-tight break-words">' + k.nama + '</p>' +
                    '<p class="text-xs font-bold text-slate-700 dark:text-emerald-200 leading-relaxed break-words">' + k.barang + '</p>' +
                    '<div class="flex items-center gap-3">' +
                        kuponAksiButton(k, true) +
                        '<span class="shrink-0 font-sans text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-emerald-400">' + k.kode_kupon + '</span>' +
                    '</div>' +
                '</div>'
            ).join('');
        }
        renderIkon();
    }

    async function toggleStatusKupon(idKupon, statusAmbil) {
      try {
        // Yang diteruskan ke fungsi ini adalah Penukaran.id (mis. 170), bukan
        // kode kupon. Kode kupon harus dicari dari daftar, dan itu harus
        // dilakukan SEBELUM daftar di-reload di bawah.
        const kupon = listKuponVerifikasi.find(k => String(k.id) === String(idKupon));
        const kode = kupon ? kupon.kode_kupon : idKupon;

        await apiFetch('/verifikasi/kupon/' + idKupon, {
          method: 'PUT',
          body: JSON.stringify({ diambil: statusAmbil })
        });
        
        // Reload kupon data
        const res = await apiFetch('/verifikasi/kupon');
        if (res.data) listKuponVerifikasi = res.data;
        
        renderKuponPengurus();

        if (statusAmbil) {
          showModernModal('success', 'Diperbarui', 'Status kupon ' + kode + ' diperbarui');
        } else {
          showModernModal('success', 'Diperbarui', 'Status penukaran ' + kode + ' dibatalkan');
        }
      } catch (err) {
        showModernModal('danger', 'Gagal', 'Gagal memperbarui status kupon.');
      }
    }

    function renderVerifikasi(filterQuery) {
      const container = document.getElementById('container-verifikasi');
      if (!container) return;
      
      const badgeEl = document.getElementById('badge-pending-count');
      if (badgeEl) badgeEl.innerText = verifikasiPending.length;

      const q = (filterQuery || '').toLowerCase();
      const filtered = verifikasiPending.filter(item => {
        const text = (item.nama + ' ' + item.rumah + ' ' + item.jenis + ' ' + item.catatan).toLowerCase();
        return text.includes(q);
      });

      if (filtered.length == 0) {
        container.innerHTML = '<p class="text-slate-400 dark:text-emerald-400 text-xs py-4 text-center font-medium">Belum ada laporan verifikasi</p>';
        return;
      }

      container.innerHTML = filtered.map(item => 
        '<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl border border-slate-200/80 dark:border-emerald-800/70 bg-slate-50/70 dark:bg-brand-950/40">' +
          // min-w-0 wajib: tanpa itu, teks panjang (mis. nama kategori yang
          // panjang) menahan kolom ini melebar dan mendorong tombol ke luar kotak.
          '<div class="min-w-0">' +
            '<p class="font-extrabold text-slate-900 dark:text-white text-sm leading-tight break-words">' + item.nama + ' (' + (item.rumah || '-') + ')</p>' +
            '<p class="text-xs text-slate-500 dark:text-emerald-300/80 font-medium mt-0.5 break-words">' + item.jenis + ' - ' + item.berat + '</p>' +
          '</div>' +
          // flex-wrap: tiga tombol ini totalnya +-266px, sedangkan ruang di
          // layar 360px hanya +-244px. Sebelumnya `shrink-0` memaksa mereka
          // tetap satu baris sehingga keluar dari kotak. Dengan wrap, kalau
          // tidak cukup ruang tombol turun ke baris berikutnya.
          '<div class="flex flex-wrap gap-2 sm:shrink-0">' +
            '<button onclick="bukaModalDetailSetoran(' + item.id + ')" class="px-3 py-2 bg-slate-200 dark:bg-brand-950 text-slate-800 dark:text-emerald-200 hover:bg-slate-300 border border-transparent dark:border-emerald-800 rounded-xl font-bold text-xs active-press shadow-sm flex items-center gap-1.5"><i data-lucide="eye" class="w-4 h-4 text-brand-600 dark:text-brand-400"></i> Detail</button>' +
            '<button onclick="prosesVerifikasi(' + item.id + ', true)" class="px-3.5 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-bold text-xs active-press shadow-sm flex items-center gap-1.5"><i data-lucide="check" class="w-4 h-4"></i> Terima</button>' +
            '<button onclick="prosesVerifikasi(' + item.id + ', false)" class="px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold text-xs active-press shadow-sm flex items-center gap-1.5"><i data-lucide="x" class="w-4 h-4"></i> Tolak</button>' +
          '</div>' +
        '</div>'
      ).join('');
      renderIkon();
    }

    function bukaModalDetailSetoran(id) {
      const item = verifikasiPending.find(v => v.id === id);
      if(!item) return;

      const imgBukti = document.getElementById('detail-img-bukti');
      const imgBuktiEmpty = document.getElementById('detail-img-bukti-empty');
      if (item.foto) {
        imgBukti.src = item.foto;
        imgBukti.classList.remove('hidden');
        imgBuktiEmpty.classList.add('hidden');
        imgBuktiEmpty.classList.remove('flex');
      } else {
        imgBukti.classList.add('hidden');
        imgBuktiEmpty.classList.remove('hidden');
        imgBuktiEmpty.classList.add('flex');
      }
      document.getElementById('detail-nama-warga').innerText = item.nama + ' (' + (item.rumah || '-') + ')';
      document.getElementById('detail-tgl-kirim').innerText = item.tgl;
      document.getElementById('detail-kategori').innerText = item.jenis;
      document.getElementById('detail-berat').innerText = item.berat;
      document.getElementById('detail-catatan').innerText = item.catatan;

      document.getElementById('btn-modal-terima').onclick = () => {
        closeModalDetailSetoran();
        prosesVerifikasi(item.id, true);
      };
      document.getElementById('btn-modal-tolak').onclick = () => {
        closeModalDetailSetoran();
        prosesVerifikasi(item.id, false);
      };

      document.getElementById('modal-detail-setoran').classList.remove('hidden');
      renderIkon();
    }

    function closeModalDetailSetoran() {
      document.getElementById('modal-detail-setoran').classList.add('hidden');
    }

    async function prosesVerifikasi(id, isAcc) {
      const setoran = verifikasiPending.find(s => s.id === id);
      try {
        await apiFetch('/verifikasi/setoran/' + id, {
          method: 'PUT',
          body: JSON.stringify({ status: isAcc ? 'diterima' : 'ditolak' })
        });

        verifikasiPending = verifikasiPending.filter(item => item.id !== id);
        renderVerifikasi();
        
        // Update badge
        const badgeEl = document.getElementById('badge-pending-count');
        if (badgeEl) badgeEl.innerText = verifikasiPending.length;
        const antreanEl = document.getElementById('antrean-verifikasi-count');
        if (antreanEl) antreanEl.innerText = verifikasiPending.length;
        
        showModernModal('success', 'Berhasil', isAcc ? 'Setoran diterima' : 'Setoran ditolak');
      } catch (err) {
        showModernModal('danger', 'Gagal', 'Gagal memproses verifikasi.');
      }
    }

    async function loadBulanLaporan() {
      try {
        const res = await apiFetch('/laporan/bulan');
        const select = document.getElementById('laporan-bulan');
        if (select && res.data) {
          // Keep "Semua Periode" option, remove others
          select.innerHTML = '<option value="">Pilih Bulan</option><option value="all">Semua Periode</option>';
          res.data.forEach(b => {
            const opt = document.createElement('option');
            opt.value = b.value;
            opt.textContent = b.label;
            select.appendChild(opt);
          });
        }
      } catch (err) {
        console.warn('Gagal memuat daftar bulan:', err);
      }
    }

    async function handleCetakLaporan(e) {
      e.preventDefault();
      const bulan = document.getElementById('laporan-bulan').value;

      if (!bulan) {
        showModernModal('danger', 'Peringatan', 'Silakan pilih periode bulan terlebih dahulu.');
        return;
      }

      // Redirect to export URL (format selalu PDF)
      const url = '/laporan/cetak?bulan=' + encodeURIComponent(bulan) + '&format=pdf';
      window.open(url, '_blank');
    }

    async function simulasiSetor(e) {
      e.preventDefault();
      
      const kategoriId = document.getElementById('setor-kategori').value;
      const berat = document.getElementById('setor-berat').value;
      const satuan = document.getElementById('setor-satuan-berat').value;
      const catatan = document.getElementById('setor-catatan').value;
      const fotoInput = document.getElementById('setor-foto');
      
      if (!kategoriId) {
        showModernModal('danger', 'Peringatan', 'Silakan pilih kategori sampah terlebih dahulu.');
        return;
      }
      
      try {
        const formData = new FormData();
        formData.append('kategori_id', kategoriId);
        formData.append('berat', berat);
        formData.append('satuan', satuan);
        formData.append('catatan', catatan);
        // Tunggu kompresi foto selesai lebih dulu, kalau tidak berkas mentah
        // ikut terkirim dan pasti ditolak validasi server.
        if (setorFotoProses) {
          try { await setorFotoProses; } catch (err) { /* pakai berkas mentah */ }
        }
        if (fotoInput && fotoInput.files.length > 0) {
          formData.append('foto', setorFotoSiap || fotoInput.files[0]);
        }
        
        const res = await apiFetch('/setoran', {
          method: 'POST',
          body: formData
        });
        
        if (res.success) {
          showModernModal('success', 'Terkirim', 'Laporan setoran berhasil dikirim', () => {
            // Reset form
            document.getElementById('setor-kategori').value = '';
            document.getElementById('setor-berat').value = '';
            document.getElementById('setor-catatan').value = '';
            updateSatuanDanEstimasiSetor();
            hapusSetorFoto();
            navigate('dashboard-warga');
          });
        } else {
          showModernModal('danger', 'Gagal', res.message || 'Gagal mengirim laporan setoran.');
        }
      } catch (err) {
        // Tampilkan pesan asli dari server, mis. "Berat maksimal 1.000 Kg per
        // setoran" atau "Satuan tidak dikenali". Jangan selalu menyebut
        // "kesalahan jaringan" karena itu menutupi kesalahan input.
        showModernModal('danger', 'Gagal', (err && err.message) || 'Gagal mengirim laporan setoran');
      }
    }

    function previewSetorFoto(event) {
      const file = event.target.files[0];
      if (!file) return;

      const label = document.getElementById('setor-foto-filename');

      // Preview berkas mentah supaya responsif, kompresi jalan di belakang.
      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('setor-foto-placeholder').classList.add('hidden');
        document.getElementById('setor-foto-preview-box').classList.remove('hidden');
        document.getElementById('setor-foto-preview-img').src = e.target.result;
      };
      reader.readAsDataURL(file);

      if (label) label.innerText = 'Memproses foto...';

      // setorFotoProses disimpan supaya handleSubmitSetoran bisa menunggu
      // kompresi selesai. Tanpa itu, menekan tombol kirim sebelum kompresi
      // beres akan mengirim berkas mentah yang pasti ditolak server.
      setorFotoProses = kompresGambar(file)
        .then((hasil) => {
          setorFotoSiap = hasil;
          if (label) {
            label.innerText = hasil.name + ' - ' + formatUkuran(hasil.size) +
              ' (dari ' + formatUkuran(file.size) + ')';
          }
        })
        .catch(() => {
          // Kompresi gagal (mis. HEIC dari iPhone): pakai berkas mentah,
          // validasi server yang menolak kalau kelewat besar.
          setorFotoSiap = null;
          if (label) {
            label.innerText = file.name + ' - ' + formatUkuran(file.size) + ' (tidak dikompres)';
          }
        });
    }

    function hapusSetorFoto(e) {
      if (e) e.stopPropagation();
      const input = document.getElementById('setor-foto');
      input.value = '';
      setorFotoSiap = null;
      setorFotoProses = null;
      document.getElementById('setor-foto-placeholder').classList.remove('hidden');
      document.getElementById('setor-foto-preview-box').classList.add('hidden');
      document.getElementById('setor-foto-preview-img').src = '';
      document.getElementById('setor-foto-filename').innerText = '';
    }

    function toggleDarkMode() {
      document.documentElement.classList.toggle('dark');
    }

    /* ============ DRAWER SIDEBAR (KHUSUS LAYAR KECIL) ============
       Di bawah breakpoint `lg` sidebar jadi drawer: sliding dari kiri dan
       menutupi sebagian layar. Di layar lebar sidebar kembali seperti
       semula di samping konten, dan tombol titik tiga disembunyikan oleh CSS.

       Ambang lebar layar dibaca dari aturan CSS `lg:` (1024px), sama dengan
       yang dipakai kelas `lg:` di markup, supaya tidak ikut berubah kalau
       breakpoint Tailwind diganti.
    */

    /** Lebar layar saat ini sudah besar (>= lg) atau belum. */
    function layarLebar() {
      return window.matchMedia('(min-width: 1024px)').matches;
    }

    function terapkanStatusSidebar(terbuka) {
      const sidebar = document.getElementById('sidebar');
      const backdrop = document.getElementById('sidebar-backdrop');
      const tombol = document.getElementById('sidebar-toggle');
      if (!sidebar) return;

      if (terbuka) {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
      } else {
        sidebar.classList.remove('translate-x-0');
        sidebar.classList.add('-translate-x-full');
      }

      if (backdrop) {
        backdrop.classList.toggle('opacity-0', !terbuka);
        backdrop.classList.toggle('pointer-events-none', !terbuka);
      }

      if (tombol) {
        tombol.setAttribute('aria-expanded', terbuka ? 'true' : 'false');
        tombol.setAttribute('aria-label', terbuka ? 'Tutup menu' : 'Buka menu');
      }

      // Latar halaman di belakang drawer tidak boleh ikut bergulir, kalau tidak
      // menggulir halaman terjadi bersamaan dengan menggeser drawer.
      //
      // Ditulis sebagai nilai mutlak (bukan toggle) supaya selalu konsisten:
      // begitu layar jadi lebar, `overflow` langsung dikosongkan tanpa
      // bergantung pada kelas yang kebetulan terpasang. Kalau atribut ini
      // tertinggal 'hidden', halaman terkunci dan tidak bisa digulir sama sekali.
      document.body.style.overflow = (!layarLebar() && terbuka) ? 'hidden' : '';

      if (layarLebar()) {
        // Di layar lebar sidebar kolom biasa, jadi tidak boleh menyisakan
        // status "dibuka" yang membuat overlay tetap tampil.
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('lg:translate-x-0');
      }
    }

    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      if (!sidebar) return;
      terapkanStatusSidebar(sidebar.classList.contains('-translate-x-full'));
    }

    function bukaSidebar() {
      terapkanStatusSidebar(true);
    }

    function tutupSidebar() {
      terapkanStatusSidebar(false);
    }

    // Kalau jendela diperbesar melewati `lg` sementara drawer sedang terbuka,
    // sidebar sudah tampil sebagai kolom biasa, jadi tombolnya harus disembunyikan
    // dan gulir latar harus dibebaskan kembali.
    window.matchMedia('(min-width: 1024px)').addEventListener('change', function (e) {
      if (e.matches) tutupSidebar();
    });

    // Jaring pengaman untuk perubahan ukuran yang TIDAK melewati 1024px --
    // memutar HP, jendela browser di-drag, atau zoom browser. Yang dipengaruhi
    // zoom adalah lebar viewport CSS, bukan lebar jendela, jadi `resize`
    // dipantau untuk memastikan status drawer dan kunci gulir halaman selalu
    // sesuai ukuran layar saat ini.
    let timerResize = null;
    window.addEventListener('resize', function () {
      clearTimeout(timerResize);
      timerResize = setTimeout(function () {
        if (layarLebar()) tutupSidebar();
      }, 150);
    });

    // Escape menutup drawer, mengikuti kebiasaan menu di Android/iOS.
    document.addEventListener('keydown', function (e) {
      if (e.key !== 'Escape') return;
      const sidebar = document.getElementById('sidebar');
      if (sidebar && !sidebar.classList.contains('-translate-x-full')) {
        tutupSidebar();
      }
    });

    // Set current date. Penjaga null wajib: baris ini berada di luar try/catch
    // dan dieksekusi SEBELUM checkSession(). Kalau id-nya Typo atau topbar
    // dihapus, getElementById mengembalikan null dan seluruh kode setelah baris
    // ini ikut batal -- termasuk checkSession() di bawah.
    const elTanggal = document.getElementById('current-date-text');
    if (elTanggal) {
      elTanggal.innerText = new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    }

    // Pastikan drawer selalu tertutup saat halaman selesai dimuat, walau
    // browser memulihkan posisi gulir atau status DOM dari cache.
    tutupSidebar();

    // Check for existing session on page load
    (async function checkSession() {
      // Tanpa token tidak ada yang perlu ditanyakan ke server.
      if (!authToken) { tampilkanLogin(); return; }

      try {
        const res = await apiFetch('/me');
        if (res && res.success) {
          currentUserSession = res.user;
          userPoin = res.user.poin || 0;
          await setupUserSystem();
        } else {
          // Server menolak token tanpa error -> perlakukan sebagai belum login.
          localStorage.removeItem('plung_token');
          authToken = null;
          tampilkanLogin();
        }
      } catch (err) {
        // Gagal jaringan / server. Token TIDAK dihapus di sini, supaya token
        // yang masih sah tidak hilang hanya karena koneksi sesaat putus.
        console.warn('Gagal memeriksa sesi:', err);
        tampilkanLogin();
      }
    })();
  </script>
</body>
</html>
