
          <!-- PANEL PENGURUS RT -->
          <nav id="nav-group-pengurus" class="space-y-1.5 font-bold text-[13px] hidden">
            <p class="px-3 py-2 text-[11px] text-slate-400 dark:text-emerald-400 uppercase tracking-widest font-extrabold">Panel Pengurus RT</p>
            
            <button onclick="navigate('dashboard-rt')" id="nav-dashboard-rt" class="nav-btn smooth-zoom w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all active-press text-slate-600 dark:text-emerald-200 hover:bg-slate-100 dark:hover:bg-brand-950">
              <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
            </button>

            <button onclick="navigate('daftar-warga')" id="nav-daftar-warga" class="nav-btn smooth-zoom w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all active-press text-slate-600 dark:text-emerald-200 hover:bg-slate-100 dark:hover:bg-brand-950">
              <i data-lucide="users" class="w-4 h-4"></i> Kelola Warga
            </button>

            <button onclick="navigate('verifikasi')" id="nav-verifikasi" class="nav-btn smooth-zoom w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all active-press text-slate-600 dark:text-emerald-200 hover:bg-slate-100 dark:hover:bg-brand-950">
              <span class="flex items-center gap-3">
                <i data-lucide="check-square" class="w-4 h-4"></i> Verifikasi Setoran
              </span>
              <span id="badge-pending-count" class="bg-amber-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full">0</span>
            </button>

            <button onclick="navigate('verifikasi-kupon')" id="nav-verifikasi-kupon" class="nav-btn smooth-zoom w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all active-press text-slate-600 dark:text-emerald-200 hover:bg-slate-100 dark:hover:bg-brand-950">
              <span class="flex items-center gap-3">
                <i data-lucide="ticket" class="w-4 h-4"></i> Verifikasi Kupon
              </span>
              <span id="badge-kupon-pending-count" class="bg-brand-600 text-white text-[10px] font-black px-2 py-0.5 rounded-full">0</span>
            </button>

            <button onclick="navigate('kelola-reward')" id="nav-kelola-reward" class="nav-btn smooth-zoom w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all active-press text-slate-600 dark:text-emerald-200 hover:bg-slate-100 dark:hover:bg-brand-950">
              <i data-lucide="sliders" class="w-4 h-4"></i> Edit Katalog Hadiah
            </button>

            <button onclick="navigate('laporan')" id="nav-laporan" class="nav-btn smooth-zoom w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all active-press text-slate-600 dark:text-emerald-200 hover:bg-slate-100 dark:hover:bg-brand-950">
              <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> Cetak Laporan
            </button>
          </nav>

