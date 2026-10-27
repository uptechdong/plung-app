          <!-- 6. HALAMAN RIWAYAT AKTIVITAS -->
          <section id="page-riwayat" class="page-content hidden space-y-6">
            <div class="bg-white dark:bg-darkcard p-6 rounded-2xl border border-slate-200/70 dark:border-emerald-800/80 space-y-4 shadow-sm">
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between items-start gap-2 border-b border-slate-100 dark:border-emerald-800/60 pb-3">
                <div class="min-w-0">
                  <h2 class="font-bold text-slate-900 dark:text-white text-base">Riwayat Aktivitas Warga</h2>
                  
                </div>
                <span id="badge-total-aktivitas" class="shrink-0 px-3 py-1 bg-emerald-50 dark:bg-brand-950 text-brand-600 dark:text-brand-300 font-extrabold text-xs rounded-xl border border-slate-200 dark:border-emerald-800/80">0 Aktivitas</span>
              </div>

              <div id="cards-riwayat" class="md:hidden space-y-3">
                <!-- Skeleton: bentuk kartu menyamai kartu riwayat asli supaya
                     tampilan tidak "loncat" saat data dari server datang. -->
                <div class="riwayat-skeleton rounded-2xl border border-slate-200/80 dark:border-emerald-800/70 bg-slate-50/70 dark:bg-brand-950/40 p-4 space-y-2.5">
                  <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 space-y-1.5 flex-1">
                      <div class="h-3 w-20 rounded bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div>
                      <div class="h-3.5 w-28 rounded bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div>
                    </div>
                    <div class="h-3.5 w-12 rounded bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div>
                  </div>
                  <div class="h-3 w-full rounded bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div>
                  <div><div class="h-5 w-20 rounded-full bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div></div>
                </div>
                <div class="riwayat-skeleton rounded-2xl border border-slate-200/80 dark:border-emerald-800/70 bg-slate-50/70 dark:bg-brand-950/40 p-4 space-y-2.5">
                  <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 space-y-1.5 flex-1">
                      <div class="h-3 w-20 rounded bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div>
                      <div class="h-3.5 w-28 rounded bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div>
                    </div>
                    <div class="h-3.5 w-12 rounded bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div>
                  </div>
                  <div class="h-3 w-full rounded bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div>
                  <div><div class="h-5 w-20 rounded-full bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div></div>
                </div>
              </div>

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
                    <tr class="skeleton-row"><td class="py-3.5"><div class="h-3 w-20 rounded bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div></td><td class="py-3.5"><div class="h-3 w-24 rounded bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div></td><td class="py-3.5"><div class="h-3 w-32 rounded bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div></td><td class="py-3.5"><div class="h-5 w-16 rounded-full bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div></td><td class="py-3.5"><div class="h-3 w-12 ml-auto rounded bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div></td></tr>
                    <tr class="skeleton-row"><td class="py-3.5"><div class="h-3 w-20 rounded bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div></td><td class="py-3.5"><div class="h-3 w-24 rounded bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div></td><td class="py-3.5"><div class="h-3 w-32 rounded bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div></td><td class="py-3.5"><div class="h-5 w-16 rounded-full bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div></td><td class="py-3.5"><div class="h-3 w-12 ml-auto rounded bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div></td></tr>
                    <tr class="skeleton-row"><td class="py-3.5"><div class="h-3 w-20 rounded bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div></td><td class="py-3.5"><div class="h-3 w-24 rounded bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div></td><td class="py-3.5"><div class="h-3 w-32 rounded bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div></td><td class="py-3.5"><div class="h-5 w-16 rounded-full bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div></td><td class="py-3.5"><div class="h-3 w-12 ml-auto rounded bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div></td></tr>
                    <tr class="skeleton-row"><td class="py-3.5"><div class="h-3 w-20 rounded bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div></td><td class="py-3.5"><div class="h-3 w-24 rounded bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div></td><td class="py-3.5"><div class="h-3 w-32 rounded bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div></td><td class="py-3.5"><div class="h-5 w-16 rounded-full bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div></td><td class="py-3.5"><div class="h-3 w-12 ml-auto rounded bg-slate-200/80 dark:bg-emerald-900/60 animate-pulse"></div></td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </section>

