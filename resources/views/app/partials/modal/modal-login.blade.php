  <!-- ================= 1. MODAL LOGIN ================= -->
  {{-- Default-nya disembunyikan. Tampilannya dikendalikan JS lewat
       tampilkanLogin(), supaya halaman ini tidak berkedip di frame pertama
       setiap kali refresh. --}}
  <div id="auth-screen" class="hidden fixed inset-0 bg-brand-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4">
    <div id="login-card" class="bg-white dark:bg-darkcard rounded-3xl border border-slate-200/80 dark:border-emerald-800/80 p-8 w-full max-w-md space-y-6 shadow-2xl animate-modal-pop transition-all">
      
      <div class="text-center space-y-2">
        <div class="w-16 h-16 bg-brand-600 text-white rounded-2xl flex items-center justify-center mx-auto shadow-md">
          <i data-lucide="leaf" class="w-8 h-8"></i>
        </div>
        <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">PLUNG!</h1>
        <p class="text-sm font-bold text-brand-600 dark:text-brand-300">Sistem Pemilahan Sampah Mandiri</p>
      </div>

      <div class="flex bg-slate-100 dark:bg-brand-950 p-1.5 rounded-2xl border border-slate-200/60 dark:border-emerald-900/60 text-xs font-bold">
        <button onclick="setLoginTab('warga')" id="tab-login-warga" class="flex-1 py-3 rounded-xl bg-brand-600 text-white transition-all flex items-center justify-center gap-2">
          <i data-lucide="user" class="w-4 h-4"></i> Akses Warga
        </button>
        <button onclick="setLoginTab('pengurus')" id="tab-login-pengurus" class="flex-1 py-3 rounded-xl text-slate-600 dark:text-emerald-300 hover:text-brand-600 transition-all flex items-center justify-center gap-2">
          <i data-lucide="shield-check" class="w-4 h-4"></i> Pengurus RT
        </button>
      </div>

      <form onsubmit="handleLoginSubmit(event)" class="space-y-4 text-xs font-semibold">
        <div>
          <label class="block mb-1.5 text-slate-700 dark:text-emerald-200">Username</label>
          <div class="relative">
            <i data-lucide="user" class="w-4 h-4 absolute left-3.5 top-3.5 text-slate-400 dark:text-emerald-400"></i>
            <input type="text" id="login-username" required autocomplete="username" placeholder="Masukkan username" aria-label="Username" class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 dark:bg-brand-950 border border-slate-200 dark:border-emerald-800/80 text-slate-900 dark:text-white focus:outline-none focus:border-brand-500 transition-all font-medium">
          </div>
        </div>

        <div>
          <label class="block mb-1.5 text-slate-700 dark:text-emerald-200">Kata Sandi</label>
          <div class="relative">
            <i data-lucide="lock" class="w-4 h-4 absolute left-3.5 top-3.5 text-slate-400 dark:text-emerald-400"></i>
            <input type="password" id="login-password" required autocomplete="current-password" placeholder="••••••••" aria-label="Kata Sandi" class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 dark:bg-brand-950 border border-slate-200 dark:border-emerald-800/80 text-slate-900 dark:text-white focus:outline-none focus:border-brand-500 transition-all font-medium">
          </div>
        </div>

        <button type="submit" class="w-full py-3.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl active-press transition-all flex items-center justify-center gap-2 text-xs shadow-md">
          <i data-lucide="log-in" class="w-4 h-4"></i> Masuk
        </button>
      </form>
    </div>
  </div>


