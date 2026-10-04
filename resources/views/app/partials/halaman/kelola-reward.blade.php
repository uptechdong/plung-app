          <!-- 11. EDIT & KELOLA KATALOG HADIAH (PENGURUS) -->
          <section id="page-kelola-reward" class="page-content hidden space-y-6">
            <!-- Form Tambah / Edit Hadiah -->
            <div class="bg-white dark:bg-darkcard p-6 rounded-2xl border border-slate-200/70 dark:border-emerald-800/80 space-y-4 shadow-sm">
              <div>
                <h2 id="reward-form-title" class="font-bold text-slate-900 dark:text-white text-base">Kelola Item Hadiah</h2>
                
              </div>

              <form onsubmit="handleTambahReward(event)" class="text-xs font-semibold">
                <input type="hidden" id="reward-id">
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
                  <div>
                    <label class="block mb-1 text-slate-700 dark:text-emerald-200">Nama Hadiah</label>
                    <input type="text" id="reward-nama" placeholder="Contoh: Teh Celup" required class="w-full p-3 rounded-xl bg-slate-50 dark:bg-brand-950 border border-slate-200 dark:border-emerald-800/80 text-slate-900 dark:text-white font-medium">
                  </div>
                  <div>
                    <label class="block mb-1 text-slate-700 dark:text-emerald-200">Poin Dibutuhkan</label>
                    <input type="number" id="reward-poin" placeholder="Contoh: 10" required min="0" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-brand-950 border border-slate-200 dark:border-emerald-800/80 text-slate-900 dark:text-white font-medium">
                  </div>
                  <div>
                    <label class="block mb-1 text-slate-700 dark:text-emerald-200">Jumlah Stok Sisa</label>
                    <input type="number" id="reward-stok" placeholder="Contoh: 1" required min="0" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-brand-950 border border-slate-200 dark:border-emerald-800/80 text-slate-900 dark:text-white font-medium">
                  </div>
                  <div>
                    <label class="block mb-1 text-slate-700 dark:text-emerald-200">Keterangan Ringkas</label>
                    <input type="text" id="reward-ket" placeholder="Contoh: Minuman santai warga" required class="w-full p-3 rounded-xl bg-slate-50 dark:bg-brand-950 border border-slate-200 dark:border-emerald-800/80 text-slate-900 dark:text-white font-medium">
                  </div>
                </div>

                <div class="pt-4 flex gap-2 sm:justify-end">
                  <button type="button" id="btn-cancel-reward-edit" onclick="resetFormReward()" class="hidden flex-1 sm:flex-none px-6 py-3 bg-slate-100 dark:bg-brand-950 text-slate-700 dark:text-emerald-200 font-bold rounded-xl active-press">Batal</button>
                  <button type="submit" id="btn-submit-reward" class="flex-1 sm:flex-none px-6 py-3.5 bg-brand-600 text-white font-bold rounded-xl active-press shadow-sm">Simpan Item</button>
                </div>
              </form>
            </div>

            <!-- Daftar Katalog Hadiah. Grid dan kartunya sengaja dibuat sama
                 persis dengan katalog pada menu Penukaran Poin milik warga,
                 sehingga tampilannya selalu sinkron. -->
            <div class="bg-white dark:bg-darkcard p-6 rounded-2xl border border-slate-200/70 dark:border-emerald-800/80 space-y-4 shadow-sm">
              <div class="border-b border-slate-100 dark:border-emerald-800/60 pb-3">
                <h2 class="font-bold text-slate-900 dark:text-white text-base">Daftar Katalog Hadiah</h2>
              </div>

              <div id="container-reward-pengurus-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-xs"></div>
            </div>
          </section>

