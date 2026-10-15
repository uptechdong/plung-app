          <div class="flex items-center justify-between gap-2 sm:gap-4 py-3 border-b border-slate-200/60 dark:border-emerald-900/60">

            <button type="button" id="sidebar-toggle" onclick="toggleSidebar()"
                    class="lg:hidden shrink-0 p-2 -ml-1 rounded-xl text-slate-600 dark:text-emerald-200 hover:bg-slate-100 dark:hover:bg-brand-950 active-press transition-colors"
                    aria-label="Buka menu" aria-expanded="false" aria-controls="sidebar">
              <i data-lucide="menu" class="w-5 h-5"></i>
            </button>
            
            <!-- INPUT SEARCH GLOBAL -->
            <div id="top-search-bar" class="relative flex-1 min-w-0 max-w-xs sm:max-w-md flex items-center">
              <i data-lucide="search" class="w-4 h-4 text-slate-400 dark:text-emerald-400 absolute left-3"></i>
              <input type="text" id="global-search-input" oninput="handleGlobalSearch(this.value)" placeholder="Cari data ....." class="w-full pl-9 pr-4 py-2 bg-slate-100 dark:bg-brand-950 rounded-xl text-xs text-slate-700 dark:text-emerald-100 placeholder-slate-400 dark:placeholder-emerald-500 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all font-medium border border-transparent dark:border-emerald-800/60">
            </div>

            <div class="flex items-center gap-2 sm:gap-4 shrink-0">
              <span id="current-date-text" class="hidden sm:inline text-xs font-bold text-slate-600 dark:text-emerald-200/90 pr-3 sm:pr-4 border-r border-slate-200 dark:border-emerald-800/80">-</span>

              <button onclick="toggleDarkMode()" title="Ganti Mode Gelap/Terang" aria-label="Ganti mode gelap/terang" class="p-2 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-emerald-200 transition-colors">
                <i data-lucide="moon" class="w-5 h-5 dark:hidden"></i>
                <i data-lucide="sun" class="w-5 h-5 hidden dark:block text-amber-400"></i>
              </button>

              <div class="relative">
                <button onclick="toggleProfileDropdown()" class="flex items-center gap-2 sm:gap-3 text-xs text-slate-700 dark:text-emerald-100 font-bold hover:opacity-80 transition-opacity">
                  <div class="w-9 h-9 rounded-xl border-2 border-slate-200 dark:border-emerald-700 text-brand-600 dark:text-brand-300 flex items-center justify-center shrink-0 overflow-hidden bg-slate-50 dark:bg-brand-950 shadow-sm">
                    <img id="user-avatar-img" src="" alt="Avatar" class="w-full h-full object-cover hidden">
                    <i id="user-avatar-default-icon" data-lucide="user" class="w-5 h-5"></i>
                  </div>
                  <span id="user-display-name" class="hidden sm:inline max-w-[9rem] truncate">-</span>
                  <i data-lucide="chevron-down" class="hidden sm:block w-4 h-4 text-slate-400"></i>
                </button>

                <div id="profile-dropdown-menu" class="absolute right-0 mt-2 w-52 bg-white dark:bg-darkcard border border-slate-200/80 dark:border-emerald-800/80 rounded-2xl shadow-2xl p-2 z-40 hidden animate-modal-pop text-xs font-semibold">
                  <div class="px-3 py-2 border-b border-slate-100 dark:border-emerald-800/60 mb-1">
                    <p id="dropdown-user-name" class="font-extrabold text-slate-900 dark:text-white">-</p>
                    <p id="dropdown-user-role" class="text-[10px] text-slate-400 dark:text-emerald-400 font-bold">Warga RT 36/RW 09</p>
                  </div>

                  <button id="dropdown-btn-edit" onclick="openEditProfilFromMenu(); closeProfileDropdown();" class="w-full flex items-center gap-2 px-3 py-2.5 rounded-xl text-slate-700 dark:text-emerald-200 hover:bg-slate-50 dark:hover:bg-brand-950 transition-colors">
                    <i data-lucide="user-cog" class="w-4 h-4 text-brand-600 dark:text-brand-400"></i> Edit Profil
                  </button>

                  <div class="border-t border-slate-100 dark:border-emerald-800/60 my-1"></div>

                  <button onclick="confirmLogout(); closeProfileDropdown();" class="w-full flex items-center gap-2 px-3 py-2.5 rounded-xl text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/60 font-bold transition-colors">
                    <i data-lucide="log-out" class="w-4 h-4"></i> Logout
                  </button>
                </div>
              </div>
            </div>
          </div>
