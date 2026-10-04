          <!-- ================= 2. DASHBOARD PENGURUS RT ================= -->
          <section id="page-dashboard-rt" class="page-content hidden space-y-6">
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div class="smooth-zoom bg-white dark:bg-darkcard p-5 rounded-2xl border border-slate-200/70 dark:border-emerald-800/80 space-y-3 relative overflow-hidden shadow-sm">
                <div class="flex items-center justify-between relative z-10">
                  <span class="text-xs text-slate-500 dark:text-emerald-300 font-bold">Total Warga Terdaftar</span>
                  <span id="trend-warga-badge" class="shrink-0 px-2.5 py-0.5 bg-emerald-100 dark:bg-emerald-950/90 text-brand-700 dark:text-brand-300 font-extrabold text-[10px] rounded-full flex items-center gap-1 border border-transparent dark:border-emerald-800">
                    <i data-lucide="user-check" class="w-3 h-3 text-brand-700 dark:text-brand-300"></i> <span id="trend-warga-text">-</span>
                  </span>
                </div>
                <div class="flex items-baseline justify-between relative z-10">
                  <p class="text-3xl font-black text-brand-600 dark:text-brand-300 tracking-tight"><span id="total-warga-count">0</span> <span class="text-xs font-semibold text-brand-600 dark:text-brand-300">KK</span></p>
                  <i data-lucide="users" class="w-12 h-12 text-emerald-500/20 dark:text-emerald-400/20 absolute -right-2 -bottom-2 pointer-events-none"></i>
                </div>
              </div>

              <div class="smooth-zoom bg-white dark:bg-darkcard p-5 rounded-2xl border border-slate-200/70 dark:border-emerald-800/80 space-y-3 relative overflow-hidden shadow-sm">
                <div class="flex items-center justify-between relative z-10">
                  <span class="text-xs text-slate-500 dark:text-emerald-300 font-bold">Total Sampah Terkelola</span>
                  <span id="trend-sampah-rt-badge" class="shrink-0 px-2.5 py-0.5 bg-emerald-100 dark:bg-emerald-950/90 text-brand-700 dark:text-brand-300 font-extrabold text-[10px] rounded-full flex items-center gap-1 border border-transparent dark:border-emerald-800">
                    <i data-lucide="trending-up" class="w-3 h-3"></i> <span id="trend-sampah-rt-text">-</span>
                  </span>
                </div>
                <div class="flex items-baseline justify-between relative z-10">
                  <p class="text-3xl font-black text-brand-600 dark:text-brand-300 tracking-tight"><span id="rt-total-sampah">0</span> <span class="text-xs font-semibold text-brand-600 dark:text-brand-300">Kg</span></p>
                  <i data-lucide="recycle" class="w-12 h-12 text-emerald-500/20 dark:text-emerald-400/20 absolute -right-2 -bottom-2 pointer-events-none"></i>
                </div>
              </div>

              <div class="smooth-zoom bg-white dark:bg-darkcard p-5 rounded-2xl border border-slate-200/70 dark:border-emerald-800/80 space-y-3 relative overflow-hidden shadow-sm">
                <div class="flex items-center justify-between relative z-10">
                  <span class="text-xs text-slate-500 dark:text-emerald-300 font-bold">Antrean Verifikasi</span>
                  <span class="shrink-0 px-2.5 py-0.5 bg-amber-100 dark:bg-amber-950/90 text-amber-700 dark:text-amber-300 font-extrabold text-[10px] rounded-full flex items-center gap-1 border border-transparent dark:border-amber-800">
                    <i data-lucide="clock" class="w-3 h-3 text-amber-700 dark:text-amber-300"></i> Pending
                  </span>
                </div>
                <div class="flex items-baseline justify-between relative z-10">
                  <p class="text-3xl font-black text-amber-500 dark:text-amber-400 tracking-tight"><span id="antrean-verifikasi-count">0</span> <span class="text-xs font-semibold text-amber-500 dark:text-amber-400">Laporan</span></p>
                  <i data-lucide="check-square" class="w-12 h-12 text-amber-500/20 dark:text-amber-400/20 absolute -right-2 -bottom-2 pointer-events-none"></i>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
              <div class="smooth-zoom lg:col-span-2 bg-white dark:bg-darkcard p-5 rounded-2xl border border-slate-200/70 dark:border-emerald-800/80 space-y-4 shadow-sm">
                <div>
                  <h3 class="font-bold text-slate-900 dark:text-white text-sm">Volume Pengumpulan Sampah Bulanan (Kg)</h3>
                  
                </div>
                <div class="h-60 relative">
                  <canvas id="chartTrenRT"></canvas>
                </div>
              </div>

              <div class="smooth-zoom bg-white dark:bg-darkcard p-5 rounded-2xl border border-slate-200/70 dark:border-emerald-800/80 space-y-4 shadow-sm">
                <div>
                  <h3 class="font-bold text-slate-900 dark:text-white text-sm">Komposisi Sampah RT (Kg)</h3>
               </div>
                <div class="h-60 relative flex items-center justify-center">
                  <canvas id="chartKomposisiRT"></canvas>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
              <div class="lg:col-span-2 bg-white dark:bg-darkcard p-5 rounded-2xl border border-slate-200/70 dark:border-emerald-800/80 space-y-3 shadow-sm">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm">Transaksi Warga Terbaru</h3>
                    
                  </div>
                  <button onclick="navigate('verifikasi')" class="text-xs text-brand-600 dark:text-brand-300 font-extrabold hover:underline">Kelola Verifikasi</button>
                </div>

                {{-- Di bawah 768px tabel 5 kolom tidak muat dan hanya bisa dibaca
                     lewat geser horizontal yang sering tidak diketahui
                     pengguna. Jadi di HP data dirender sebagai kartu (lihat
                     JS loadDashboardRT), tabel baru muncul di tablet/desktop.
                     Sama seperti dashboard warga. --}}
                <div id="cards-dash-rt" class="md:hidden space-y-3"></div>

                <div class="hidden md:block overflow-x-auto">
                  <table class="w-full md:min-w-[40rem] text-center text-xs whitespace-nowrap">
                    <thead>
                      <tr class="text-slate-400 dark:text-emerald-400 border-b border-slate-100 dark:border-emerald-800/60 font-bold">
                        <th class="pb-2.5 text-center">Tanggal</th>
                        <th class="pb-2.5 text-center">Nama Warga</th>
                        <th class="pb-2.5 text-center">Rincian Sampah</th>
                        <th class="pb-2.5 text-center">Status</th>
                        <th class="pb-2.5 text-center">Poin</th>
                      </tr>
                    </thead>
                    <tbody id="tbl-dash-rt-body" class="divide-y divide-slate-100 dark:divide-emerald-800/60 font-semibold text-slate-700 dark:text-emerald-100">
                      <tr class="empty-state-row"><td colspan="5" class="py-6 text-center text-slate-400 dark:text-emerald-400 text-xs font-medium">Belum ada transaksi warga</td></tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Warga Aktif Pilah Sampah -->
              <div class="smooth-zoom bg-white dark:bg-darkcard p-5 rounded-2xl border border-slate-200/70 dark:border-emerald-800/80 space-y-3 shadow-sm">
                <div class="border-b border-slate-100 dark:border-emerald-800/60 pb-2">
                  <h3 class="font-bold text-slate-900 dark:text-white text-sm">
                    Warga Aktif Pilah Sampah
                  </h3>
                </div>
                
                
                <div id="top-3-warga-container" class="space-y-2.5 pt-1 text-xs"></div>
              </div>
            </div>

          </section>

