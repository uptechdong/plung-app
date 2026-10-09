          <!-- PANEL WARGA -->
          {{-- Sama seperti sidebar pengurus: whitespace-nowrap + truncate supaya
               nama menu panjang (mis. "Riwayat Aktivitas") tetap satu baris dan
               dipotong dengan elipsis, bukan turun ke baris kedua. --}}
          <nav id="nav-group-warga" class="space-y-1.5 font-bold text-[13px]">
            <p class="px-3 py-2 text-[11px] text-slate-400 dark:text-emerald-400 uppercase tracking-widest font-extrabold">Panel Warga</p>
            
            <button onclick="navigate('dashboard-warga')" id="nav-dashboard-warga" class="nav-btn smooth-zoom w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all active-press whitespace-nowrap text-slate-600 dark:text-emerald-200 hover:bg-slate-100 dark:hover:bg-brand-950">
              <i data-lucide="layout-dashboard" class="w-4 h-4 shrink-0"></i> Dashboard
            </button>
            
            <button onclick="navigate('panduan')" id="nav-panduan" class="nav-btn smooth-zoom w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all active-press whitespace-nowrap text-slate-600 dark:text-emerald-200 hover:bg-slate-100 dark:hover:bg-brand-950">
              <i data-lucide="book-open" class="w-4 h-4 shrink-0"></i> Panduan Pilah
            </button>

            <button onclick="navigate('setor')" id="nav-setor" class="nav-btn smooth-zoom w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all active-press whitespace-nowrap text-slate-600 dark:text-emerald-200 hover:bg-slate-100 dark:hover:bg-brand-950">
              <i data-lucide="plus-circle" class="w-4 h-4 shrink-0"></i> Setor Sampah
            </button>

            <button onclick="navigate('tukar')" id="nav-tukar" class="nav-btn smooth-zoom w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all active-press whitespace-nowrap text-slate-600 dark:text-emerald-200 hover:bg-slate-100 dark:hover:bg-brand-950">
              <i data-lucide="gift" class="w-4 h-4 shrink-0"></i> Tukar Poin
            </button>

            <button onclick="navigate('riwayat')" id="nav-riwayat" class="nav-btn smooth-zoom w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all active-press whitespace-nowrap text-slate-600 dark:text-emerald-200 hover:bg-slate-100 dark:hover:bg-brand-950">
              <i data-lucide="history" class="w-4 h-4 shrink-0"></i> Riwayat Aktivitas
            </button>
          </nav>

