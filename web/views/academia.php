<?php require_once __DIR__ . '/layouts/header.php'; ?>
<?php require_once __DIR__ . '/layouts/sidebar.php'; ?>

<main class="main-content">
    <?php require_once __DIR__ . '/layouts/topbar.php'; ?>
    <header class="flex justify-between items-center pb-4 border-b border-white/10">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-white">Performance & Treinos</h2>
            <p class="text-sm text-slate-400">Controle de volume muscular, frequência e ganho de nível físico.</p>
        </div>
        <div class="text-right">
            <span class="text-xs uppercase tracking-wider text-slate-400">Frequência Semanal</span>
            <p class="text-sm font-semibold text-cyan-400" id="fitness-frequency-tag">Meta: 5 Sessões</p>
        </div>
    </header>

    <!-- Cards de Métricas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="stat-card glass-panel border-l-4 border-l-cyan-500">
            <div class="stat-header">
                <span>Treinos (7 Dias)</span>
                <i data-lucide="activity" class="w-5 h-5 text-cyan-400"></i>
            </div>
            <div id="workouts-count" class="stat-value text-cyan-400">0</div>
            <span class="text-xs text-slate-400">Sessões finalizadas</span>
        </div>

        <div class="stat-card glass-panel border-l-4 border-l-amber-500">
            <div class="stat-header">
                <span>Tempo em Atividade</span>
                <i data-lucide="timer" class="w-5 h-5 text-amber-400"></i>
            </div>
            <div id="workouts-duration" class="stat-value text-amber-400">0 min</div>
            <span class="text-xs text-slate-400">Tempo sob tensão</span>
        </div>

        <div class="stat-card glass-panel border-l-4 border-l-rose-500">
            <div class="stat-header">
                <span>Gasto Estimado</span>
                <i data-lucide="flame" class="w-5 h-5 text-rose-400"></i>
            </div>
            <div id="workouts-calories" class="stat-value text-rose-400">0 kcal</div>
            <span class="text-xs text-slate-400">Consumo calórico</span>
        </div>

        <div class="stat-card glass-panel border-l-4 border-l-purple-500">
            <div class="stat-header">
                <span>XP Ganho</span>
                <i data-lucide="zap" class="w-5 h-5 text-purple-400"></i>
            </div>
            <div id="workouts-xp" class="stat-value text-purple-400">+0 XP</div>
            <span class="text-xs text-slate-400">Evolução de perfil</span>
        </div>
    </div>

    <!-- Linha: Formulário Detalhado + Carrossel de Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Formulário -->
        <div class="glass-panel p-6 rounded-2xl lg:col-span-1 flex flex-col gap-4">
            <h3 class="text-lg font-semibold text-slate-200 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-5 h-5 text-cyan-400"></i>
                Registrar Sessão
            </h3>
            <form id="form-workout" class="space-y-3.5">
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Grupamento Muscular</label>
                    <select id="workout-tipo" class="w-full">
                        <option value="Peito & Tríceps">Peito & Tríceps (Push A)</option>
                        <option value="Costas & Bíceps">Costas & Bíceps (Pull A)</option>
                        <option value="Pernas Completo">Pernas Completo (Legs)</option>
                        <option value="Upper">Upper (Superiores Geral)</option>
                        <option value="Lower">Lower (Inferiores Geral)</option>
                        <option value="Ombros & Abdômen">Ombros & Abdômen</option>
                        <option value="Cardio / HIIT">Cardio / Corrida / HIIT</option>
                        <option value="Full Body">Full Body</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Duração (min)</label>
                        <input type="number" id="workout-duracao" min="10" max="240" value="60" required class="w-full">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Intensidade</label>
                        <select id="workout-intensidade" class="w-full">
                            <option value="moderada">Moderada</option>
                            <option value="intensa">Intensa 🔥</option>
                            <option value="leve">Leve / Regenerativo</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Data da Sessão</label>
                    <input type="date" id="workout-data" class="w-full">
                </div>

                <div class="p-3 bg-white/[0.03] rounded-xl border border-white/5 flex justify-between items-center text-xs">
                    <span class="text-slate-400">Recompensa Estimada:</span>
                    <span id="workout-preview-xp" class="font-bold text-purple-400">+75 XP</span>
                </div>

                <button type="submit" id="btn-save-workout" class="btn-primary w-full py-3 mt-1 font-semibold flex items-center justify-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    Salvar Treino
                </button>
            </form>
        </div>

        <!-- Carrossel de Gráficos -->
        <div class="glass-panel p-6 rounded-2xl lg:col-span-2 flex flex-col justify-between">
            <div class="flex justify-between items-center pb-2 border-b border-white/5">
                <div class="flex items-center gap-2">
                    <i data-lucide="dumbbell" class="w-5 h-5 text-cyan-400"></i>
                    <h3 id="fitness-chart-title" class="text-base font-semibold text-slate-200">Divisão por Grupamento</h3>
                </div>
                
                <div class="flex items-center gap-1.5">
                    <button type="button" onclick="prevFitnessChart()" class="p-2 rounded-lg bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white border border-white/5 transition-all active:scale-95" title="Anterior">
                        <i data-lucide="chevron-left" class="w-4 h-4"></i>
                    </button>
                    <button type="button" onclick="nextFitnessChart()" class="p-2 rounded-lg bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white border border-white/5 transition-all active:scale-95" title="Próximo">
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <div class="relative w-full h-72 flex items-center justify-center my-auto pt-2">
                <canvas id="fitnessDetailChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tabela de Histórico -->
    <div class="glass-panel p-6 rounded-2xl flex flex-col gap-4">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-slate-200 flex items-center gap-2">
                <i data-lucide="history" class="w-5 h-5 text-cyan-400"></i>
                Histórico Recente de Treinos
            </h3>
            <span class="text-xs text-slate-400">Últimos registros salvos</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="border-b border-white/10 text-xs text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-4">Data</th>
                        <th class="py-3 px-4">Grupamento</th>
                        <th class="py-3 px-4">Duração</th>
                        <th class="py-3 px-4">Intensidade</th>
                        <th class="py-3 px-4">XP Ganho</th>
                        <th class="py-3 px-4 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody id="workout-history-body" class="divide-y divide-white/5 text-slate-200">
                    <tr>
                        <td colspan="6" class="py-6 text-center text-slate-500">Carregando treinos...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>