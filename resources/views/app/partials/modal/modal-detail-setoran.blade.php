  <!-- ================= 3. MODAL DETAIL VERIFIKASI SETORAN ================= -->
  <div id="modal-detail-setoran" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-darkcard rounded-3xl border border-slate-100 dark:border-emerald-800/80 p-6 w-full max-w-lg space-y-4 shadow-2xl animate-modal-pop">
      <div class="flex justify-between items-center border-b border-slate-100 dark:border-emerald-800/60 pb-3">
        <h3 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2">
          <i data-lucide="file-search" class="w-5 h-5 text-brand-600 dark:text-brand-400"></i> Detail Setoran Sampah
        </h3>
        <button onclick="closeModalDetailSetoran()" class="text-slate-400 hover:text-slate-600 dark:hover:text-emerald-200"><i data-lucide="x" class="w-5 h-5"></i></button>
      </div>

      <div class="space-y-3 text-xs">
        <div>
          <label class="block font-bold text-slate-700 dark:text-emerald-200 mb-1">Foto Bukti:</label>
          <div class="rounded-2xl overflow-hidden border border-slate-200 dark:border-emerald-800 bg-slate-100 dark:bg-brand-950 h-52 flex items-center justify-center">
            <img id="detail-img-bukti" src="" alt="Bukti Sampah" class="w-full h-full object-cover">
            <div id="detail-img-bukti-empty" class="hidden flex-col items-center justify-center text-slate-400 dark:text-slate-500">
              <i data-lucide="user-x" class="w-12 h-12 mb-2"></i>
              <p class="text-xs font-semibold">Tidak ada foto bukti</p>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-2 p-3 bg-slate-50 dark:bg-brand-950 rounded-xl border border-slate-200/80 dark:border-emerald-800/80">
          <div>
            <p class="text-slate-400 dark:text-emerald-400 font-medium">Warga:</p>
            <p id="detail-nama-warga" class="font-extrabold text-slate-900 dark:text-white text-xs">-</p>
          </div>
          <div>
            <p class="text-slate-400 dark:text-emerald-400 font-medium">Tanggal:</p>
            <p id="detail-tgl-kirim" class="font-extrabold text-slate-900 dark:text-white text-xs">-</p>
          </div>
          <div>
            <p class="text-slate-400 dark:text-emerald-400 font-medium">Kategori:</p>
            <p id="detail-kategori" class="font-extrabold text-brand-600 dark:text-brand-300 text-xs">-</p>
          </div>
          <div>
            <p class="text-slate-400 dark:text-emerald-400 font-medium">Berat/Jumlah:</p>
            <p id="detail-berat" class="font-extrabold text-slate-900 dark:text-white text-xs">-</p>
          </div>
        </div>

        <div>
          <p class="text-slate-400 dark:text-emerald-400 font-medium mb-0.5">Catatan Warga:</p>
          <p id="detail-catatan" class="p-2.5 rounded-xl bg-slate-50 dark:bg-brand-950 border border-slate-200 dark:border-emerald-800/80 text-slate-700 dark:text-emerald-100 font-medium italic">-</p>
        </div>
      </div>

      <div class="pt-2 flex gap-2">
        <button id="btn-modal-terima" class="flex-1 py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl active-press shadow-sm flex items-center justify-center gap-1.5 text-xs">
          <i data-lucide="check" class="w-4 h-4"></i> Terima
        </button>
        <button id="btn-modal-tolak" class="flex-1 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl active-press shadow-sm flex items-center justify-center gap-1.5 text-xs">
          <i data-lucide="x" class="w-4 h-4"></i> Tolak
        </button>
      </div>
    </div>
  </div>


