<?php
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<aside class="sidebar glass-panel w-64 p-6 flex flex-col justify-between border-r border-white/10 m-4">
    <div class="space-y-6">
        <div class="flex items-center gap-3 px-2">
            <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                <i data-lucide="shield" class="w-5 h-5 text-white"></i>
            </div>
            <h1 class="font-bold text-lg tracking-wider text-white">LIFE <span class="text-indigo-400">ERP</span></h1>
        </div>

        <nav class="space-y-1.5">
    <a href="dashboard.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
        <span>Dashboard</span>
    </a>
    <a href="habitos.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'habitos.php' ? 'active' : '' ?>">
        <i data-lucide="check-circle" class="w-4 h-4"></i>
        <span>Hábitos</span>
    </a>
    <a href="projetos.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'projetos.php' ? 'active' : '' ?>">
        <i data-lucide="kanban" class="w-4 h-4"></i>
        <span>Projetos & Tarefas</span>
    </a>
    <a href="financas.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'financas.php' ? 'active' : '' ?>">
        <i data-lucide="wallet" class="w-4 h-4"></i>
        <span>Finanças</span>
    </a>
    <a href="academia.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'academia.php' ? 'active' : '' ?>">
        <i data-lucide="dumbbell" class="w-4 h-4"></i>
        <span>Fitness</span>
    </a>
    <a href="estudos.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'estudos.php' ? 'active' : '' ?>">
        <i data-lucide="book-open" class="w-4 h-4"></i>
        <span>Estudos</span>
    </a>
</nav>
    </div>

    <!-- Gamificação / Resumo de Nível na Sidebar -->
    <div class="glass-panel p-4 rounded-xl border border-white/5 space-y-2">
        <div class="flex justify-between text-xs text-slate-400">
            <span id="user-name">Usuário</span>
            <span id="user-level" class="text-indigo-400 font-semibold">Nível 1</span>
        </div>
        <div class="progress-track">
            <div id="user-xp-bar" class="progress-fill" style="width: 0%;"></div>
        </div>
        <p id="user-xp-text" class="text-[11px] text-slate-400 truncate text-center">0 / 100 XP</p>
    </div>
</aside>