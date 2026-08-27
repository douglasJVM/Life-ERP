<header class="w-full flex justify-between items-center pb-5 mb-6 border-b border-white/10">
    <div>
        <h2 class="text-xl font-bold tracking-tight text-white flex items-center gap-2">
            Olá, <span id="header-user-name" class="text-indigo-400">...</span> 👋
        </h2>
        <p class="text-xs text-slate-400 mt-0.5">Acompanhe seus dados em tempo real.</p>
    </div>

    <!-- Perfil Dinâmico + Botão Exit -->
    <div class="flex items-center gap-3">
        <div class="text-right hidden sm:block">
            <p class="text-sm font-semibold text-slate-200" id="header-profile-name">Carregando...</p>
            <span class="text-xs text-indigo-400 font-medium" id="header-user-level">Nível 1</span>
        </div>
        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 via-purple-500 to-cyan-400 p-0.5 shadow-md shadow-indigo-500/20">
            <div class="w-full h-full bg-slate-900 rounded-[10px] flex items-center justify-center font-bold text-xs text-white" id="header-user-avatar">
                --
            </div>
        </div>

        <!-- Botão Exit no Topbar -->
        <button type="button" onclick="window.logoutUser()" class="p-2.5 rounded-xl bg-white/5 hover:bg-rose-500/15 border border-white/10 hover:border-rose-500/30 text-slate-400 hover:text-rose-400 transition-all active:scale-95 ml-1" title="Sair da Conta">
            <i data-lucide="log-out" class="w-4 h-4"></i>
        </button>
    </div>
</header>