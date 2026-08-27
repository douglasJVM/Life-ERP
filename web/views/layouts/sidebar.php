<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- SIDEBAR DESKTOP (Oculta no mobile, visível a partir de telas médias) -->
<aside class="hidden md:flex w-64 flex-col justify-between p-6 glass-panel border-r border-white/5 min-h-screen fixed left-0 top-0 z-30">
    <div class="space-y-8">
        <!-- Logo -->
        <div class="flex items-center gap-3 px-2">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/20">
                <i data-lucide="shield" class="w-5 h-5"></i>
            </div>
            <div>
                <h1 class="font-bold text-base tracking-wider text-white">LIFE <span class="text-indigo-400">ERP</span></h1>
                <p class="text-[10px] text-slate-400 uppercase tracking-widest">Painel Pessoal</p>
            </div>
        </div>

        <!-- Links de Navegação Desktop -->
        <nav class="space-y-1.5">
            <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all <?= $currentPage === 'dashboard.php' ? 'bg-indigo-600/20 text-indigo-300 border border-indigo-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-white/5' ?>">
                <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
            </a>
            <a href="habitos.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all <?= $currentPage === 'habitos.php' ? 'bg-indigo-600/20 text-indigo-300 border border-indigo-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-white/5' ?>">
                <i data-lucide="check-circle" class="w-4 h-4"></i> Hábitos
            </a>
            <a href="projetos.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all <?= $currentPage === 'projetos.php' ? 'bg-indigo-600/20 text-indigo-300 border border-indigo-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-white/5' ?>">
                <i data-lucide="kanban" class="w-4 h-4"></i> Projetos & Tarefas
            </a>
            <a href="financas.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all <?= $currentPage === 'financas.php' ? 'bg-indigo-600/20 text-indigo-300 border border-indigo-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-white/5' ?>">
                <i data-lucide="wallet" class="w-4 h-4"></i> Finanças
            </a>
            <a href="academia.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all <?= $currentPage === 'academia.php' ? 'bg-indigo-600/20 text-indigo-300 border border-indigo-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-white/5' ?>">
                <i data-lucide="dumbbell" class="w-4 h-4"></i> Fitness
            </a>
            <a href="estudos.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all <?= $currentPage === 'estudos.php' ? 'bg-indigo-600/20 text-indigo-300 border border-indigo-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-white/5' ?>">
                <i data-lucide="book-open" class="w-4 h-4"></i> Estudos
            </a>
        </nav>
    </div>

    <!-- Perfil Resumido na Base -->
    <div class="pt-4 border-t border-white/5 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-xs" id="sidebar-user-avatar">
                --
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-semibold text-slate-200 truncate w-28" id="sidebar-user-name">Usuário</p>
                <p class="text-[10px] text-slate-500 truncate w-28" id="sidebar-user-email">email@life.com</p>
            </div>
        </div>
        <button type="button" onclick="window.logoutUser()" class="text-slate-500 hover:text-rose-400 p-1.5 transition-colors" title="Sair">
            <i data-lucide="log-out" class="w-4 h-4"></i>
        </button>
    </div>
</aside>

<!-- BOTTOM NAVIGATION MOBILE (Barra inferior fixa para celulares / PWA) -->
<nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 glass-panel border-t border-white/10 bg-[#06090e]/95 backdrop-blur-xl px-2 py-2 flex items-center justify-around">
    <a href="dashboard.php" class="flex flex-col items-center gap-1 p-2 rounded-xl text-[10px] font-medium transition-colors <?= $currentPage === 'dashboard.php' ? 'text-indigo-400' : 'text-slate-400 hover:text-slate-200' ?>">
        <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
        <span>Dash</span>
    </a>
    <a href="habitos.php" class="flex flex-col items-center gap-1 p-2 rounded-xl text-[10px] font-medium transition-colors <?= $currentPage === 'habitos.php' ? 'text-indigo-400' : 'text-slate-400 hover:text-slate-200' ?>">
        <i data-lucide="check-circle" class="w-5 h-5"></i>
        <span>Hábitos</span>
    </a>
    <a href="financas.php" class="flex flex-col items-center gap-1 p-2 rounded-xl text-[10px] font-medium transition-colors <?= $currentPage === 'financas.php' ? 'text-indigo-400' : 'text-slate-400 hover:text-slate-200' ?>">
        <i data-lucide="wallet" class="w-5 h-5"></i>
        <span>Finanças</span>
    </a>
    <a href="academia.php" class="flex flex-col items-center gap-1 p-2 rounded-xl text-[10px] font-medium transition-colors <?= $currentPage === 'academia.php' ? 'text-indigo-400' : 'text-slate-400 hover:text-slate-200' ?>">
        <i data-lucide="dumbbell" class="w-5 h-5"></i>
        <span>Fitness</span>
    </a>
    <a href="estudos.php" class="flex flex-col items-center gap-1 p-2 rounded-xl text-[10px] font-medium transition-colors <?= $currentPage === 'estudos.php' ? 'text-indigo-400' : 'text-slate-400 hover:text-slate-200' ?>">
        <i data-lucide="book-open" class="w-5 h-5"></i>
        <span>Estudos</span>
    </a>
    <a href="projetos.php" class="flex flex-col items-center gap-1 p-2 rounded-xl text-[10px] font-medium transition-colors <?= $currentPage === 'projetos.php' ? 'text-indigo-400' : 'text-slate-400 hover:text-slate-200' ?>">
        <i data-lucide="kanban" class="w-5 h-5"></i>
        <span>Kanban</span>
    </a>
</nav>