          <!-- 7. HALAMAN EDIT AKUN PROFIL (UNIVERSAL WARGA & PENGURUS) -->
          <section id="page-edit-profil" class="page-content hidden space-y-6">
            <div class="bg-white dark:bg-darkcard p-6 rounded-2xl border border-slate-200/70 dark:border-emerald-800/80 space-y-5 max-w-xl mx-auto shadow-sm">
              <div>
                <h2 id="ep-title-heading" class="font-bold text-slate-900 dark:text-white text-base">Edit Profil Akun</h2>
                <p id="ep-subtitle-heading" class="text-xs text-slate-400 dark:text-emerald-400 font-medium">Perbarui data profil dan kata sandi Anda</p>
              </div>

              <form onsubmit="simpanProfilUser(event)" class="space-y-4 text-xs font-semibold">
                <div class="p-4 bg-slate-50 dark:bg-brand-950 rounded-xl border border-slate-200/80 dark:border-emerald-800/80 flex items-center gap-4">
                  <div class="relative w-16 h-16 rounded-2xl border-2 border-slate-200 dark:border-emerald-700 text-brand-600 dark:text-brand-300 flex items-center justify-center overflow-hidden shrink-0 shadow-sm bg-white dark:bg-darkcard">
                    <img id="ep-preview-foto" src="" alt="Preview Foto" class="w-full h-full object-cover hidden">
                    <i id="ep-preview-icon" data-lucide="user" class="w-8 h-8"></i>
                  </div>
                  <div class="min-w-0 space-y-1.5">
                    <label class="block text-slate-700 dark:text-emerald-200 font-bold">Foto Profil</label>
                    <input type="file" id="ep-upload-foto" accept="image/*" class="hidden" onchange="previewUploadedFoto(event)">
                    <button type="button" onclick="document.getElementById('ep-upload-foto').click()" class="inline-flex items-center gap-1.5 shrink-0 rounded-xl bg-brand-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-brand-700 active:scale-95 transition-transform">
                      <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                      Pilih Foto
                    </button>
                    <p id="ep-foto-info" class="text-[10px] text-slate-400 dark:text-emerald-400">Pilih berkas gambar JPG/PNG</p>
                  </div>
                </div>

                <div>
                  <label class="block mb-1 text-slate-700 dark:text-emerald-200">Nama Lengkap</label>
                  <input type="text" id="ep-nama" required class="w-full p-3 rounded-xl bg-slate-50 dark:bg-brand-950 border border-slate-200 dark:border-emerald-800/80 text-slate-900 dark:text-white font-medium">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div>
                    <label id="ep-label-rumah" class="block mb-1 text-slate-700 dark:text-emerald-200">Nomor Rumah</label>
                    <input type="text" id="ep-rumah" required class="w-full p-3 rounded-xl bg-slate-50 dark:bg-brand-950 border border-slate-200 dark:border-emerald-800/80 text-slate-900 dark:text-white font-medium">
                  </div>
                  <div>
                    <label class="block mb-1 text-slate-700 dark:text-emerald-200">Nomor WhatsApp</label>
                    <input type="text" id="ep-wa" required class="w-full p-3 rounded-xl bg-slate-50 dark:bg-brand-950 border border-slate-200 dark:border-emerald-800/80 text-slate-900 dark:text-white font-medium">
                  </div>
                </div>

                <div class="p-4 bg-slate-50 dark:bg-brand-950 rounded-xl border border-slate-200/80 dark:border-emerald-800/80 space-y-3">
                  <p class="font-bold text-brand-700 dark:text-emerald-300 text-xs">Ubah Akses Login:</p>
                  <div>
                    <label class="block mb-1 text-slate-700 dark:text-emerald-200">Username</label>
                    <input type="text" id="ep-username" name="username" autocomplete="username" required class="w-full p-3 rounded-xl bg-white dark:bg-darkcard border border-slate-200 dark:border-emerald-800/80 text-slate-900 dark:text-white font-medium">
                  </div>

                  <div>
                    <label class="block mb-1 text-slate-700 dark:text-emerald-200">Kata Sandi Baru</label>
                    <input type="password" id="ep-password" autocomplete="new-password" placeholder="Kosongkan jika tidak diubah" class="w-full p-3 rounded-xl bg-white dark:bg-darkcard border border-slate-200 dark:border-emerald-800/80 text-slate-900 dark:text-white font-medium">
                  </div>
                </div>

                <button type="submit" class="w-full py-3.5 bg-brand-600 text-white font-bold rounded-xl active-press transition-all flex items-center justify-center gap-2 shadow-sm">
                  <i data-lucide="save" class="w-4 h-4"></i> Simpan Perubahan
                </button>
              </form>
            </div>
          </section>

