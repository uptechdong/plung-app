          <!-- 12. CETAK REKAP -->
          <section id="page-laporan" class="page-content hidden space-y-4">
            <div class="bg-white dark:bg-darkcard p-6 rounded-2xl border border-slate-200/70 dark:border-emerald-800/80 space-y-5 max-w-md mx-auto shadow-sm">
              <div class="text-center space-y-1">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-brand-950 text-brand-600 dark:text-brand-300 rounded-2xl flex items-center justify-center mx-auto border border-transparent dark:border-emerald-800">
                  <i data-lucide="file-spreadsheet" class="w-6 h-6"></i>
                </div>
                <h2 class="font-bold text-slate-900 dark:text-white text-base">Cetak Laporan</h2>
                <p class="text-xs text-slate-400 dark:text-emerald-400 font-medium">Unduh laporan rekapitulasi data pengelolaan sampah</p>
              </div>

              <form onsubmit="handleCetakLaporan(event)" class="space-y-4 text-xs font-semibold">
                <div>
                  <label class="block mb-1 text-slate-700 dark:text-emerald-200">Periode Laporan</label>
                  <select id="laporan-bulan" required class="w-full p-3 rounded-xl bg-slate-50 dark:bg-brand-950 border border-slate-200 dark:border-emerald-800/80 text-slate-900 dark:text-white font-medium">
                    <option value="">Pilih Bulan</option>
                    <option value="all">Semua Periode</option>
                  </select>
                </div>

                <button type="submit" class="w-full py-3.5 bg-brand-600 text-white font-bold rounded-xl active-press shadow-sm flex items-center justify-center gap-2">
                  <i data-lucide="download" class="w-4 h-4"></i> Unduh Laporan
                </button>
              </form>
            </div>
          </section>

