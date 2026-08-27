<?php require_once __DIR__ . '/layouts/header.php'; ?>
<?php require_once __DIR__ . '/layouts/sidebar.php'; ?>

<main class="main-content">
    <!-- Barra Superior Padronizada (Nome, Nível, Avatar e Botão Exit) -->
    <?php require_once __DIR__ . '/layouts/topbar.php'; ?>

    <!-- Banner de Gamificação & Progresso de Nível -->
    <div class="glass-panel p-6 rounded-2xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border-l-4 border-l-indigo-500 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
                <i data-lucide="zap" class="w-6 h-6"></i>
            </div>
            <div>
                <div class="flex items-center gap-2.5">
                    <h3 class="text-base font-bold text-white tracking-tight">Evolução do Jogador</h3>
                    <span id="user-level" class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">Nível 1</span>
                </div>
                <p class="text-xs text-slate-400 mt-1" id="current-date-display">Carregando data...</p>
            </div>
        </div>
        
        <!-- Barra de Progresso de Nível -->
        <div class="w-full md:w-80 flex flex-col gap-1.5">
            <div class="flex justify-between text-xs">
                <span class="text-slate-400 font-medium">Progresso para Próximo Nível</span>
                <span id="user-xp-text" class="text-indigo-300 font-bold">0 / 100 XP</span>
            </div>
            <div class="progress-track">
                <div id="user-xp-bar" class="progress-fill" style="width: 0%;"></div>
            </div>
        </div>
    </div>

    <!-- 4 Cards de Indicadores Reais -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Saldo Líquido -->
        <div class="stat-card glass-panel border-l-4 border-l-emerald-500">
            <div class="stat-header">
                <span>Saldo em Caixa</span>
                <i data-lucide="wallet" class="w-4 h-4 text-emerald-400"></i>
            </div>
            <div id="finance-balance" class="stat-value text-emerald-400">R$ 0,00</div>
            <span class="text-xs text-slate-400" id="dash-finance-flow">Balanço líquido</span>
        </div>

        <!-- Treinos Realizados -->
        <div class="stat-card glass-panel border-l-4 border-l-cyan-500">
            <div class="stat-header">
                <span>Treinos (7 Dias)</span>
                <i data-lucide="dumbbell" class="w-4 h-4 text-cyan-400"></i>
            </div>
            <div id="weekly-workouts-count" class="stat-value text-cyan-400">0 treinos</div>
            <span class="text-xs text-slate-400" id="dash-fitness-time">0 min em atividade</span>
        </div>

        <!-- Horas de Estudo -->
        <div class="stat-card glass-panel border-l-4 border-l-purple-500">
            <div class="stat-header">
                <span>Horas de Foco</span>
                <i data-lucide="book-open" class="w-4 h-4 text-purple-400"></i>
            </div>
            <div id="weekly-studies-hours" class="stat-value text-purple-400">0h</div>
            <span class="text-xs text-slate-400" id="dash-studies-sessions">0 blocos de estudo</span>
        </div>

        <!-- Total de XP Semanal -->
        <div class="stat-card glass-panel border-l-4 border-l-amber-500">
            <div class="stat-header">
                <span>XP Acumulado</span>
                <i data-lucide="zap" class="w-4 h-4 text-amber-400"></i>
            </div>
            <div id="dash-total-xp" class="stat-value text-amber-400">+0 XP</div>
            <span class="text-xs text-slate-400">Produtividade total</span>
        </div>
    </div>

    <!-- Linha Principal: Gráfico de Despesas + Resumo dos Módulos -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        <!-- Gráfico de Despesas -->
        <div class="glass-panel p-6 rounded-2xl lg:col-span-2 flex flex-col justify-between">
            <div class="flex justify-between items-center pb-3 border-b border-white/5">
                <div class="flex items-center gap-2">
                    <i data-lucide="pie-chart" class="w-5 h-5 text-indigo-400"></i>
                    <h3 class="text-base font-semibold text-slate-200">Despesas por Categoria</h3>
                </div>
                <a href="financas.php" class="text-xs text-indigo-400 hover:text-indigo-300 flex items-center gap-1 font-medium">
                    Ver relatório completo <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </a>
            </div>
            <div class="relative w-full h-64 flex items-center justify-center my-auto pt-2">
                <canvas id="expensesChart"></canvas>
            </div>
        </div>

        <!-- Ações Rápidas de Lançamento -->
        <div class="glass-panel p-6 rounded-2xl lg:col-span-1 flex flex-col justify-between gap-4">
            <div>
                <h3 class="text-base font-semibold text-slate-200 flex items-center gap-2 pb-2 border-b border-white/5">
                    <i data-lucide="zap" class="w-5 h-5 text-amber-400"></i>
                    Lançamentos Rápidos
                </h3>
                <p class="text-xs text-slate-400 mt-2">Registre novos dados sem sair do painel principal.</p>
            </div>

            <div class="flex flex-col gap-2.5">
                <a href="financas.php" class="p-3.5 rounded-xl bg-white/[0.02] hover:bg-white/[0.05] border border-white/5 hover:border-emerald-500/30 transition-all flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-emerald-500/10 text-emerald-400 group-hover:scale-110 transition-transform">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-slate-200 block">Nova Transação</span>
                            <span class="text-xs text-slate-500">Receita ou despesa</span>
                        </div>
                    </div>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-slate-500 group-hover:text-emerald-400 transition-colors"></i>
                </a>

                <a href="academia.php" class="p-3.5 rounded-xl bg-white/[0.02] hover:bg-white/[0.05] border border-white/5 hover:border-cyan-500/30 transition-all flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-cyan-500/10 text-cyan-400 group-hover:scale-110 transition-transform">
                            <i data-lucide="dumbbell" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-slate-200 block">Registrar Treino</span>
                            <span class="text-xs text-slate-500">Grupamento & volume</span>
                        </div>
                    </div>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-slate-500 group-hover:text-cyan-400 transition-colors"></i>
                </a>

                <a href="estudos.php" class="p-3.5 rounded-xl bg-white/[0.02] hover:bg-white/[0.05] border border-white/5 hover:border-purple-500/30 transition-all flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-purple-500/10 text-purple-400 group-hover:scale-110 transition-transform">
                            <i data-lucide="clock" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-slate-200 block">Registrar Estudo</span>
                            <span class="text-xs text-slate-500">Blocos de foco</span>
                        </div>
                    </div>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-slate-500 group-hover:text-purple-400 transition-colors"></i>
                </a>
            </div>

            <div class="p-3 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-xs text-indigo-300 flex items-center gap-2">
                <i data-lucide="sparkles" class="w-4 h-4 flex-shrink-0"></i>
                <span>Mantenha consistência diária para acelerar a subida de nível.</span>
            </div>
        </div>
    </div>

    <!-- Feed de Atividades Recentes Unificado -->
    <div class="glass-panel p-6 rounded-2xl flex flex-col gap-4 mt-6">
        <div class="flex justify-between items-center pb-2 border-b border-white/5">
            <h3 class="text-base font-semibold text-slate-200 flex items-center gap-2">
                <i data-lucide="history" class="w-5 h-5 text-indigo-400"></i>
                Últimas Atividades Registradas
            </h3>
            <span class="text-xs text-slate-400">Sincronizado em tempo real</span>
        </div>

        <div id="dash-recent-feed" class="space-y-2.5">
            <div class="p-4 text-center text-sm text-slate-500">Carregando feed de atividades...</div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>