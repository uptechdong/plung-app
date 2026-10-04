
          <!-- ================= 1. DASHBOARD WARGA ================= -->
          <section id="page-dashboard-warga" class="page-content space-y-6 hidden">
            
            <div class="bg-brand-600 dark:bg-emerald-900/80 text-white p-6 rounded-3xl border border-brand-700 dark:border-emerald-700/60 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-md relative overflow-hidden">
              <div class="space-y-1 z-10">
                <span class="px-3 py-1 bg-white/20 backdrop-blur-md text-[11px] font-extrabold rounded-full uppercase tracking-wider text-emerald-100">Mari Pilah Sampah Hari Ini!</span>
                <h2 id="warga-greeting-text" class="text-2xl font-black">Selamat Datang di PLUNG!</h2>
                <p class="text-xs text-emerald-100/90 max-w-xl font-medium">Ringkasan aktivitas pemilahan sampah mandiri warga RT 36/RW 09</p>
              </div>
              
              <div class="flex items-center gap-2 w-full md:w-auto shrink-0 z-10">
                <button onclick="navigate('setor')" class="flex-1 md:flex-none px-4 py-3 bg-white text-brand-700 font-extrabold text-xs rounded-xl hover:bg-emerald-50 transition-all flex items-center justify-center gap-1.5 active-press shadow-sm">
                  <i data-lucide="plus-circle" class="w-4 h-4"></i> Setor Sampah
                </button>
                
                <button id="banner-btn-tukar" onclick="navigate('tukar')" class="flex-1 md:flex-none px-4 py-3 rounded-xl font-extrabold text-xs border transition-all flex items-center justify-center gap-1.5 active-press">
                  <i data-lucide="gift" class="w-4 h-4"></i> Tukar Poin
                </button>
              </div>

              <i data-lucide="recycle" class="w-48 h-48 text-emerald-300/20 dark:text-emerald-400/20 absolute -right-6 -bottom-10 pointer-events-none"></i>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div id="card-poin" class="smooth-zoom bg-white dark:bg-darkcard p-5 rounded-2xl border border-slate-200/70 dark:border-emerald-800/80 space-y-3 relative overflow-hidden shadow-sm">
                <div class="flex items-center justify-between relative z-10">
                  <span class="text-xs text-slate-500 dark:text-emerald-300 font-bold">Total Poin Saya</span>
                  <span id="trend-poin-badge" class="shrink-0 px-2.5 py-0.5 bg-slate-100 dark:bg-slate-950/90 text-slate-700 dark:text-slate-300 font-extrabold text-[10px] rounded-full flex items-center gap-1 border border-transparent dark:border-emerald-800">
                    <i data-lucide="minus" class="w-3 h-3"></i> <span>0</span>
                  </span>
                </div>
                <div class="flex items-baseline justify-between relative z-10">
                  <p class="text-3xl font-black text-brand-600 dark:text-brand-300 tracking-tight"><span class="user-poin-val">0</span> <span class="text-xs font-semibold text-brand-600 dark:text-brand-300">pts</span></p>
                  <i data-lucide="coins" class="w-12 h-12 text-emerald-500/20 dark:text-emerald-400/20 absolute -right-2 -bottom-2 pointer-events-none"></i>
                </div>
              </div>

              <div class="smooth-zoom bg-white dark:bg-darkcard p-5 rounded-2xl border border-slate-200/70 dark:border-emerald-800/80 space-y-3 relative overflow-hidden shadow-sm">
                <div class="flex items-center justify-between relative z-10">
                  <span class="text-xs text-slate-500 dark:text-emerald-300 font-bold">Total Sampah Dipilah</span>
                  <span id="trend-sampah-badge" class="shrink-0 px-2.5 py-0.5 bg-emerald-100 dark:bg-emerald-950/90 text-brand-700 dark:text-brand-300 font-extrabold text-[10px] rounded-full flex items-center gap-1 border border-transparent dark:border-emerald-800">
                    <i data-lucide="trending-up" class="w-3 h-3"></i> <span>-</span>
                  </span>
                </div>
                <div class="flex items-baseline justify-between relative z-10">
                  <p class="text-3xl font-black text-brand-600 dark:text-brand-300 tracking-tight"><span id="warga-total-sampah">0</span> <span class="text-xs font-semibold text-brand-600 dark:text-brand-300">Kg</span></p>
                  <i data-lucide="trash-2" class="w-12 h-12 text-emerald-500/20 dark:text-emerald-400/20 absolute -right-2 -bottom-2 pointer-events-none"></i>
                </div>
              </div>

              <div class="smooth-zoom bg-white dark:bg-darkcard p-5 rounded-2xl border border-slate-200/70 dark:border-emerald-800/80 space-y-3 relative overflow-hidden shadow-sm">
                <div class="flex items-center justify-between relative z-10">
                  <span class="text-xs text-slate-500 dark:text-emerald-300 font-bold">Peringkat Pilah Sampah</span>
                  <span id="trend-peringkat-badge" class="shrink-0 px-2.5 py-0.5 bg-emerald-100 dark:bg-emerald-950/90 text-brand-700 dark:text-brand-300 font-extrabold text-[10px] rounded-full flex items-center gap-1 border border-transparent dark:border-emerald-800">
                    <i data-lucide="trending-up" class="w-3 h-3"></i> <span>-</span>
                  </span>
                </div>
                <div class="flex items-baseline justify-between relative z-10">
                  <p class="text-3xl font-black text-brand-600 dark:text-brand-300 tracking-tight"><span id="warga-peringkat">0</span> <span class="text-xs font-semibold text-brand-600 dark:text-brand-300">dari &nbsp;<span id="warga-total-warga">-</span> KK</span></p>
                  <i data-lucide="trophy" class="w-12 h-12 text-emerald-500/20 dark:text-emerald-400/20 absolute -right-2 -bottom-2 pointer-events-none"></i>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
              <div class="smooth-zoom lg:col-span-2 bg-white dark:bg-darkcard p-5 rounded-2xl border border-slate-200/70 dark:border-emerald-800/80 space-y-4 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                  <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm">Kontribusi Pemilahan Bulanan (Kg)</h3>
                    
                  </div>
                  <span id="trend-poin-percentage" class="text-xs font-extrabold text-brand-600 dark:text-brand-300 bg-brand-50 dark:bg-brand-950 px-3 py-1 rounded-xl border border-transparent dark:border-emerald-800/60 shrink-0">-</span>
                </div>
                <div class="h-56 relative">
                  <canvas id="chartBulananWarga"></canvas>
                </div>
              </div>

              <div class="smooth-zoom bg-white dark:bg-darkcard p-5 rounded-2xl border border-slate-200/70 dark:border-emerald-800/80 space-y-4 shadow-sm">
                <div>
                  <h3 class="font-bold text-slate-900 dark:text-white text-sm">Proporsi Pilah Sampah Saya</h3>
                </div>
                <div class="h-56 relative flex items-center justify-center">
                  <canvas id="chartProporsiWarga"></canvas>
                </div>
              </div>
            </div>

            <!-- 5 RIWAYAT SETORAN TERAKHIR (WARGA) -->
            <div class="bg-white dark:bg-darkcard p-5 rounded-2xl border border-slate-200/70 dark:border-emerald-800/80 space-y-3 shadow-sm">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="font-bold text-slate-900 dark:text-white text-sm">Riwayat Setoran Terakhir</h3>
                  
                </div>
                <button onclick="navigate('riwayat')" class="text-xs text-brand-600 dark:text-brand-300 font-extrabold hover:underline">Lihat Semua</button>
              </div>

              {{-- Di bawah 768px tabel 5 kolom tidak muat (~350px setelah
                   padding) dan hanya bisa dibaca lewat geser horizontal yang
                   sering tidak diketahui pengguna. Jadi di HP data dirender
                   sebagai kartu (lihat JS loadDashboardWarga), dan tabel
                   baru muncul di tablet/desktop. --}}
              <div id="cards-dash-warga" class="md:hidden space-y-3"></div>

              {{-- md:min-w-[40rem] (640px) dipakai sebagai lantai agar kolom
                   tidak tergencet, tapi nilainya di bawah area konten
                   terkecil saat tabel tampil (704px), jadi tidak pernah
                   memunculkan gulir horizontal. --}}
              <div class="hidden md:block overflow-x-auto">
                <table class="w-full md:min-w-[40rem] text-center text-xs whitespace-nowrap">
                  <thead>
                    <tr class="text-slate-400 dark:text-emerald-400 border-b border-slate-100 dark:border-emerald-800/60 font-bold">
                      <th class="pb-2.5 text-center">Tanggal</th>
                      <th class="pb-2.5 text-center">Kategori Sampah</th>
                      <th class="pb-2.5 text-center">Berat / Jumlah</th>
                      <th class="pb-2.5 text-center">Status Verifikasi</th>
                      <th class="pb-2.5 text-center">Estimasi Poin</th>
                    </tr>
                  </thead>
                  <tbody id="tbl-dash-warga-body" class="divide-y divide-slate-100 dark:divide-emerald-800/60 font-semibold text-slate-700 dark:text-emerald-100">
                    <tr class="empty-state-row"><td colspan="5" class="py-6 text-center text-slate-400 dark:text-emerald-400 text-xs font-medium">Belum ada transaksi</td></tr>
                  </tbody>
                </table>
              </div>
            </div>

          </section>

