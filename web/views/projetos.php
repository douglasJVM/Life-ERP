<?php require_once __DIR__ . '/layouts/header.php'; ?>
<?php require_once __DIR__ . '/layouts/sidebar.php'; ?>

<main class="main-content">
    <?php require_once __DIR__ . '/layouts/topbar.php'; ?>

    <!-- Header do Kanban + Botão de Nova Tarefa -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-bold text-white">Quadro de Projetos & Tarefas</h2>
            <p class="text-xs text-slate-400">Gerencie entregas acadêmicas, profissionais e pessoais.</p>
        </div>
        <button onclick="document.getElementById('modal-task').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold flex items-center gap-2 shadow-lg shadow-indigo-500/25">
            <i data-lucide="plus" class="w-4 h-4"></i> Nova Tarefa
        </button>
    </div>

    <!-- 3 Colunas do Kanban -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- A Fazer (To Do) -->
        <div class="glass-panel p-4 rounded-2xl flex flex-col gap-3 min-h-[500px] border-t-2 border-t-slate-500">
            <div class="flex justify-between items-center pb-2 border-b border-white/5">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">A Fazer</span>
                <span id="count-todo" class="px-2 py-0.5 rounded-full text-[10px] bg-slate-800 text-slate-400">0</span>
            </div>
            <div id="col-todo" class="space-y-3 flex-1"></div>
        </div>

        <!-- Em Andamento (Doing) -->
        <div class="glass-panel p-4 rounded-2xl flex flex-col gap-3 min-h-[500px] border-t-2 border-t-amber-500">
            <div class="flex justify-between items-center pb-2 border-b border-white/5">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-400">Em Progresso</span>
                <span id="count-doing" class="px-2 py-0.5 rounded-full text-[10px] bg-amber-500/10 text-amber-400">0</span>
            </div>
            <div id="col-doing" class="space-y-3 flex-1"></div>
        </div>

        <!-- Concluído (Done) -->
        <div class="glass-panel p-4 rounded-2xl flex flex-col gap-3 min-h-[500px] border-t-2 border-t-emerald-500">
            <div class="flex justify-between items-center pb-2 border-b border-white/5">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">Concluído (+15 XP)</span>
                <span id="count-done" class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-500/10 text-emerald-400">0</span>
            </div>
            <div id="col-done" class="space-y-3 flex-1"></div>
        </div>
    </div>

    <!-- Modal Nova Tarefa -->
    <div id="modal-task" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center hidden z-50 p-4">
        <div class="glass-panel bg-[#0d131f] border border-white/10 p-6 rounded-2xl w-full max-w-md space-y-4">
            <div class="flex justify-between items-center">
                <h3 class="text-base font-bold text-white">Criar Nova Tarefa</h3>
                <button onclick="document.getElementById('modal-task').classList.add('hidden')" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form id="form-task" class="space-y-3">
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Título</label>
                    <input type="text" id="task-titulo" required placeholder="Ex: Entregar relatório de segurança..." class="w-full px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Prioridade</label>
                    <select id="task-prioridade" class="w-full px-3 py-2 rounded-xl bg-[#0d131f] border border-white/10 text-white text-sm focus:outline-none focus:border-indigo-500">
                        <option value="baixa">Baixa</option>
                        <option value="media" selected>Média</option>
                        <option value="alta">Alta</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Prazo (Opcional)</label>
                    <input type="date" id="task-prazo" class="w-full px-3 py-2 rounded-xl bg-[#0d131f] border border-white/10 text-white text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <button type="submit" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm mt-2">Adicionar Tarefa</button>
            </form>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>