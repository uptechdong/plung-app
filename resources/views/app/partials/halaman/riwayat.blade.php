          <!-- 6. HALAMAN RIWAYAT AKTIVITAS -->
          <section id="page-riwayat" class="page-content hidden space-y-6">
            <div class="bg-white dark:bg-darkcard p-6 rounded-2xl border border-slate-200/70 dark:border-emerald-800/80 space-y-4 shadow-sm">
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between items-start gap-2 border-b border-slate-100 dark:border-emerald-800/60 pb-3">
                <div class="min-w-0">
                  <h2 class="font-bold text-slate-900 dark:text-white text-base">Riwayat Aktivitas Warga</h2>
                  
                </div>
                <span id="badge-total-aktivitas" class="shrink-0 px-3 py-1 bg-emerald-50 dark:bg-brand-950 text-brand-600 dark:text-brand-300 font-extrabold text-xs rounded-xl border border-slate-200 dark:border-emerald-800/80">0 Aktivitas</span>
              </div>

              <div id="cards-riwayat" class="md:hidden space-y-3"></div>

              <div class="hidden md:block overflow-x-auto">
                <table class="w-full md:min-w-[42rem] text-left text-xs">
                  <thead>
                    <tr class="text-slate-400 dark:text-emerald-400 border-b border-slate-100 dark:border-emerald-800/60 font-bold">
                      <th class="pb-2.5">Tanggal</th>
                      <th class="pb-2.5">Jenis Aktivitas</th>
                      <th class="pb-2.5">Rincian</th>
                      <th class="pb-2.5">Status Verifikasi</th>
                      <th class="pb-2.5 text-right">Poin</th>
                    </tr>
                  </thead>
                  <tbody id="tbl-riwayat-body" class="divide-y divide-slate-100 dark:divide-emerald-800/60 font-semibold text-slate-700 dark:text-emerald-100">
                    <tr><td colspan="5" class="py-6 text-center text-slate-400 dark:text-emerald-400 text-xs font-medium">Belum ada aktivitas</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </section>

