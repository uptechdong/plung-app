          <!-- 8. DAFTAR WARGA RT -->
          <section id="page-daftar-warga" class="page-content hidden space-y-4">
            <div class="bg-white dark:bg-darkcard p-6 rounded-2xl border border-slate-200/70 dark:border-emerald-800/80 space-y-4 shadow-sm">
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between items-start gap-2.5 border-b border-slate-100 dark:border-emerald-800/60 pb-3">
                <h2 class="font-bold text-slate-900 dark:text-white text-base truncate">Kelola Data Warga RT 36</h2>
                <button onclick="openModalTambahWarga()" class="shrink-0 whitespace-nowrap px-4 py-2.5 bg-brand-600 text-white rounded-xl text-xs font-bold active-press flex items-center gap-2 shadow-sm">
                  <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah Warga
                </button>
              </div>

              <div id="cards-daftar-warga" class="md:hidden space-y-3"></div>

              <div class="hidden md:block overflow-x-auto">
                <table class="w-full md:min-w-[40rem] text-left text-xs whitespace-nowrap">
                  <thead>
                    <tr class="text-slate-400 dark:text-emerald-400 border-b border-slate-100 dark:border-emerald-800/60 font-bold">
                      <th class="pb-2.5 text-center">Nama Warga</th>
                      <th class="pb-2.5 text-center">No. Rumah</th>
                      <th class="pb-2.5 text-center">No. WhatsApp</th>
                      <th class="pb-2.5 text-center">Username</th>
                      <th class="pb-2.5 text-center">Poin</th>
                      <th class="pb-2.5 text-center">Aksi</th>
                    </tr>
                  </thead>
                  <tbody id="tbl-daftar-warga-body" class="divide-y divide-slate-100 dark:divide-emerald-800/60 font-semibold text-slate-700 dark:text-emerald-100"></tbody>
                </table>
              </div>
            </div>
          </section>

