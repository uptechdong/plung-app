          <!-- 8. DAFTAR WARGA RT -->
          <section id="page-daftar-warga" class="page-content hidden space-y-4">
            <div class="bg-white dark:bg-darkcard p-6 rounded-2xl border border-slate-200/70 dark:border-emerald-800/80 space-y-4 shadow-sm">
              {{-- Di layar 390px lebar area hanya 302px. Tombol "Tambah Warga" memakan
                   134px sehingga tersisa 168px, sedangkan judul pada
                   text-base butuh 190px -- jadi judul selalu terpotong kalau
                   dipaksa satu baris.
                   Solusinya: di HP kontainer jadi flex-col + items-start, jadi
                   judul dapat baris penuh sendiri dan tombol turun ke baris
                   kedua dengan lebar secukup isinya (bukan melebar ke tepi).
                   Mulai 640px ke atas keduanya kembali satu baris, tombol
                   terdorong ke kanan dengan sm:justify-between. --}}
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between items-start gap-2.5 border-b border-slate-100 dark:border-emerald-800/60 pb-3">
                <h2 class="font-bold text-slate-900 dark:text-white text-base truncate">Kelola Data Warga RT 36</h2>
                <button onclick="openModalTambahWarga()" class="shrink-0 whitespace-nowrap px-4 py-2.5 bg-brand-600 text-white rounded-xl text-xs font-bold active-press flex items-center gap-2 shadow-sm">
                  <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah Warga
                </button>
              </div>

              {{-- Di bawah 768px tabel 6 kolom tidak muat dan tombol Aksi di kolom
                   paling kanan jadi terpotong. Jadi di HP data dirender
                   sebagai kartu (lihat JS renderDaftarWarga), tabel baru
                   muncul di tablet/desktop. --}}
              <div id="cards-daftar-warga" class="md:hidden space-y-3"></div>

              {{-- md:min-w-[40rem] (640px) di bawah area konten terkecil saat
                   tabel tampil (704px), jadi tidak pernah memunculkan gulir. --}}
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

