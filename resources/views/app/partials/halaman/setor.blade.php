          <!-- ================= 3. HALAMAN SETOR SAMPAH ================= -->
          <section id="page-setor" class="page-content hidden space-y-6">
            <div class="bg-white dark:bg-darkcard p-6 rounded-2xl border border-slate-200/70 dark:border-emerald-800/80 space-y-4 max-w-2xl mx-auto shadow-sm">
              <div>
                <h2 class="font-bold text-slate-900 dark:text-white text-base">Formulir Setor Sampah Mandiri</h2>
              </div>

              <form onsubmit="simulasiSetor(event)" class="space-y-4 text-xs font-semibold">
                <div>
                  <label class="block mb-1.5 text-slate-700 dark:text-emerald-200">Kategori Sampah</label>
                  <select id="setor-kategori" required onchange="updateSatuanDanEstimasiSetor()" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-brand-950 border border-slate-200 dark:border-emerald-800/80 text-slate-900 dark:text-white font-medium">
                    <option value="">Pilih Jenis Sampah</option>
                  </select>
                </div>

                <div>
                  <label id="setor-label-jumlah" class="block mb-1.5 text-slate-700 dark:text-emerald-200">Jumlah Berat</label>
                  <div class="flex gap-2">
                    <input type="number" id="setor-berat" step="any" min="0" required oninput="updateSatuanDanEstimasiSetor()" placeholder="Masukkan berat sampah" class="flex-1 min-w-0 p-3 rounded-xl bg-slate-50 dark:bg-brand-950 border border-slate-200 dark:border-emerald-800/80 text-slate-900 dark:text-white font-medium focus:outline-none focus:border-brand-500">
                    <select id="setor-satuan-berat" onchange="updateSatuanDanEstimasiSetor()" class="w-24 sm:w-28 shrink-0 p-3 rounded-xl bg-slate-50 dark:bg-brand-950 border border-slate-200 dark:border-emerald-800/80 text-slate-900 dark:text-white font-bold cursor-pointer">
                      <option value="Kg" selected>Kg</option>
                      <option value="Gram">Gram</option>
                    </select>
                  </div>
                </div>

                <div>
                  <label class="block mb-1.5 text-slate-700 dark:text-emerald-200">Perkiraan Poin</label>
                  {{-- Kolom ini hanya menampilkan, tidak bisa diketik warga. --}}
                  <input type="text" id="setor-poin" readonly value="0" class="w-full p-3 rounded-xl bg-slate-100 dark:bg-brand-950/60 border border-slate-200 dark:border-emerald-800/80 text-slate-500 dark:text-emerald-200 font-extrabold focus:outline-none cursor-not-allowed">
                  
                </div>

                <div>
                  <label class="block mb-1.5 text-slate-700 dark:text-emerald-200">Keterangan</label>
                  <textarea id="setor-catatan" rows="3" placeholder="Catatan kondisi sampah atau lokasi penjemputan" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-brand-950 border border-slate-200 dark:border-emerald-800/80 text-slate-900 dark:text-white focus:outline-none focus:border-brand-500 font-medium"></textarea>
                </div>

                <div>
                  <label class="block mb-1.5 text-slate-700 dark:text-emerald-200">Unggah Foto Bukti Sampah</label>
                  <div onclick="document.getElementById('setor-foto').click()" class="border border-dashed border-slate-300 dark:border-emerald-700/80 p-6 text-center rounded-xl bg-slate-50/50 dark:bg-brand-950/60 hover:bg-slate-100 dark:hover:bg-brand-900/40 transition-colors cursor-pointer">
                    <input type="file" id="setor-foto" accept="image/*" class="hidden" onchange="previewSetorFoto(event)">
                    <div id="setor-foto-placeholder">
                      <i data-lucide="camera" class="w-6 h-6 mx-auto text-brand-600 dark:text-brand-400 mb-1"></i>
                      <p class="text-slate-400 dark:text-emerald-300 text-xs font-medium">Klik untuk mengunggah foto bukti</p>
                    </div>
                    <div id="setor-foto-preview-box" class="hidden space-y-3">
                      <div class="relative w-full h-48 rounded-xl overflow-hidden border border-slate-200 dark:border-emerald-800 bg-black/5 dark:bg-black/30">
                        <img id="setor-foto-preview-img" src="" alt="Preview" class="w-full h-full object-cover">
                        <button type="button" onclick="hapusSetorFoto(event)" class="absolute top-2 right-2 p-1.5 bg-white/90 dark:bg-slate-800/90 rounded-lg shadow-sm hover:bg-white dark:hover:bg-slate-700 transition-colors">
                          <i data-lucide="x" class="w-4 h-4 text-rose-500"></i>
                        </button>
                      </div>
                      <p id="setor-foto-filename" class="text-xs text-slate-500 dark:text-emerald-300 font-medium truncate"></p>
                    </div>
                  </div>
                </div>

                <button type="submit" class="w-full py-3.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl active-press transition-all flex items-center justify-center gap-2 shadow-sm">
                  <i data-lucide="send" class="w-4 h-4"></i> Kirim Laporan
                </button>
              </form>
            </div>
          </section>

