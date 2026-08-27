<?php require_once __DIR__ . '/layouts/header.php'; ?>
<?php require_once __DIR__ . '/layouts/sidebar.php'; ?>

<main class="main-content">
    <?php require_once __DIR__ . '/layouts/topbar.php'; ?>
    <header class="flex justify-between items-center pb-4 border-b border-white/10">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-white">Foco & Aprendizado</h2>
            <p class="text-sm text-slate-400">Acompanhamento de horas líquidas, retenção e evolução técnica.</p>
        </div>
        <div class="text-right">
            <span class="text-xs uppercase tracking-wider text-slate-400">Meta Semanal</span>
            <p class="text-sm font-semibold text-purple-400" id="study-target-tag">10h / Semana</p>
        </div>
    </header>

    <!-- Cards de Métricas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="stat-card glass-panel border-l-4 border-l-purple-500">
            <div class="stat-header">
                <span>Sessões (7 Dias)</span>
                <i data-lucide="book-open" class="w-5 h-5 text-purple-400"></i>
            </div>
            <div id="studies-sessions-count" class="stat-value text-purple-400">0</div>
            <span class="text-xs text-slate-400">Blocos concluídos</span>
        </div>

        <div class="stat-card glass-panel border-l-4 border-l-indigo-500">
            <div class="stat-header">
                <span>Horas Líquidas</span>
                <i data-lucide="clock" class="w-5 h-5 text-indigo-400"></i>
            </div>
            <div id="studies-total-hours" class="stat-value text-indigo-400">0h</div>
            <span class="text-xs text-slate-400">Tempo de imersão</span>
        </div>

        <div class="stat-card glass-panel border-l-4 border-l-cyan-500">
            <div class="stat-header">
                <span>Média Diária</span>
                <i data-lucide="target" class="w-5 h-5 text-cyan-400"></i>
            </div>
            <div id="studies-avg-daily" class="stat-value text-cyan-400">0 min</div>
            <span class="text-xs text-slate-400">Consistência diária</span>
        </div>

        <div class="stat-card glass-panel border-l-4 border-l-emerald-500">
            <div class="stat-header">
                <span>XP Intelectual</span>
                <i data-lucide="sparkles" class="w-5 h-5 text-emerald-400"></i>
            </div>
            <div id="studies-xp" class="stat-value text-emerald-400">+0 XP</div>
            <span class="text-xs text-slate-400">Pontos adquiridos</span>
        </div>
    </div>

    <!-- Linha: Formulário + Carrossel de Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Formulário de Registro -->
        <div class="glass-panel p-6 rounded-2xl lg:col-span-1 flex flex-col gap-4">
            <h3 class="text-lg font-semibold text-slate-200 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-5 h-5 text-purple-400"></i>
                Registrar Bloco de Estudo
            </h3>
            <form id="form-study" class="space-y-3.5">
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Matéria / Área</label>
                    <input type="text" id="study-materia" required placeholder="Ex: C#, Spring Boot, Inglês, SQL..." class="w-full">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Tópico Específico (Opcional)</label>
                    <input type="text" id="study-conteudo" placeholder="Ex: Collections, Shadowing, Joins..." class="w-full">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Duração (min)</label>
                        <input type="number" id="study-tempo" min="5" max="480" value="45" required class="w-full">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Data</label>
                        <input type="date" id="study-data" class="w-full">
                    </div>
                </div>

                <div class="p-3 bg-white/[0.03] rounded-xl border border-white/5 flex justify-between items-center text-xs">
                    <span class="text-slate-400">Recompensa Intelectual:</span>
                    <span id="study-preview-xp" class="font-bold text-emerald-400">+52 XP</span>
                </div>

                <button type="submit" id="btn-save-study" class="btn-primary w-full py-3 mt-1 font-semibold flex items-center justify-center gap-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    Salvar Sessão
                </button>
            </form>
        </div>

        <!-- Carrossel de Gráficos de Estudo -->
        <div class="glass-panel p-6 rounded-2xl lg:col-span-2 flex flex-col justify-between">
            <div class="flex justify-between items-center pb-2 border-b border-white/5">
                <div class="flex items-center gap-2">
                    <i data-lucide="pie-chart" class="w-5 h-5 text-purple-400"></i>
                    <h3 id="study-chart-title" class="text-base font-semibold text-slate-200">Horas Dedicadas por Matéria</h3>
                </div>
                
                <div class="flex items-center gap-1.5">
                    <button type="button" onclick="prevStudyChart()" class="p-2 rounded-lg bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white border border-white/5 transition-all active:scale-95" title="Anterior">
                        <i data-lucide="chevron-left" class="w-4 h-4"></i>
                    </button>
                    <button type="button" onclick="nextStudyChart()" class="p-2 rounded-lg bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white border border-white/5 transition-all active:scale-95" title="Próximo">
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <div class="relative w-full h-72 flex items-center justify-center my-auto pt-2">
                <canvas id="studyDetailChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tabela de Histórico -->
    <div class="glass-panel p-6 rounded-2xl flex flex-col gap-4">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-slate-200 flex items-center gap-2">
                <i data-lucide="history" class="w-5 h-5 text-purple-400"></i>
                Histórico Recente de Estudos
            </h3>
            <span class="text-xs text-slate-400">Últimas imersões concluídas</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="border-b border-white/10 text-xs text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-4">Data</th>
                        <th class="py-3 px-4">Matéria</th>
                        <th class="py-3 px-4">Tópico</th>
                        <th class="py-3 px-4">Duração</th>
                        <th class="py-3 px-4">XP Ganho</th>
                        <th class="py-3 px-4 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody id="study-history-body" class="divide-y divide-white/5 text-slate-200">
                    <tr>
                        <td colspan="6" class="py-6 text-center text-slate-500">Carregando histórico...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>