  <!-- ================= 4. MODAL KELOLA WARGA ================= -->
  <div id="modal-warga-form" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-darkcard rounded-3xl border border-slate-100 dark:border-emerald-800/80 p-6 w-full max-w-md space-y-4 shadow-2xl animate-modal-pop">
      <div class="flex justify-between items-center border-b border-slate-100 dark:border-emerald-800/60 pb-3">
        <h3 id="mw-title" class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2">
          <i data-lucide="user-plus" class="w-5 h-5 text-brand-600 dark:text-brand-400"></i> Tambah Warga
        </h3>
        <button onclick="closeModalWarga()" class="text-slate-400 hover:text-slate-600 dark:hover:text-emerald-200"><i data-lucide="x" class="w-5 h-5"></i></button>
      </div>

      <form onsubmit="simpanWargaForm(event)" class="space-y-3 text-xs font-semibold">
        <input type="hidden" id="mw-id">
        <div>
          <label class="block mb-1 text-slate-700 dark:text-emerald-200">Nama Warga</label>
          <input type="text" id="mw-nama" required placeholder="Contoh: Pak Rete" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-brand-950 border border-slate-200 dark:border-emerald-800/80 text-slate-900 dark:text-white font-medium">
        </div>
        <div>
          <label class="block mb-1 text-slate-700 dark:text-emerald-200">No. Rumah / Blok</label>
          <input type="text" id="mw-rumah" required placeholder="Contoh: No. 20" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-brand-950 border border-slate-200 dark:border-emerald-800/80 text-slate-900 dark:text-white font-medium">
        </div>
        <div>
          <label class="block mb-1 text-slate-700 dark:text-emerald-200">WhatsApp</label>
          <input type="text" id="mw-wa" required placeholder="081234567890" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-brand-950 border border-slate-200 dark:border-emerald-800/80 text-slate-900 dark:text-white font-medium">
        </div>
        
        <div class="p-3.5 bg-slate-50 dark:bg-brand-950 rounded-xl border border-slate-200/80 dark:border-emerald-800/80 space-y-2">
          <p class="font-bold text-brand-700 dark:text-emerald-300 text-xs">Akses Akun</p>
          <div>
            <label class="block text-slate-600 dark:text-emerald-300/80 mb-0.5">Username</label>
            <input type="text" id="mw-username" required autocomplete="username" placeholder="warga" class="w-full p-2.5 rounded-lg bg-white dark:bg-darkcard border border-slate-200 dark:border-emerald-800/80 text-slate-900 dark:text-white font-medium">
          </div>
          <div>
            <label class="block text-slate-600 dark:text-emerald-300/80 mb-0.5">Kata Sandi</label>
            <input type="password" id="mw-password" autocomplete="new-password" placeholder="sandi" class="w-full p-2.5 rounded-lg bg-white dark:bg-darkcard border border-slate-200 dark:border-emerald-800/80 text-slate-900 dark:text-white font-medium">
          </div>
        </div>

        <div class="pt-2 flex gap-2">
          <button type="button" onclick="closeModalWarga()" class="flex-1 py-3 bg-slate-100 dark:bg-brand-950 text-slate-700 dark:text-emerald-200 font-bold rounded-xl active-press">Batal</button>
          <button type="submit" class="flex-1 py-3 bg-brand-600 text-white font-bold rounded-xl active-press shadow-sm">Simpan</button>
        </div>
      </form>
    </div>
  </div>


