<?php require_once __DIR__ . '/layouts/header.php'; ?>
<?php require_once __DIR__ . '/layouts/sidebar.php'; ?>

<main class="main-content">
    <?php require_once __DIR__ . '/layouts/topbar.php'; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Criar Hábito -->
        <div class="glass-panel p-6 rounded-2xl h-fit">
            <h3 class="text-base font-semibold text-white flex items-center gap-2 mb-4">
                <i data-lucide="plus-circle" class="w-5 h-5 text-indigo-400"></i>
                Novo Hábito
            </h3>
            <form id="form-habit" class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Nome do Hábito</label>
                    <input type="text" id="habit-titulo" required placeholder="Ex: Beber 2L de água, Leitura 15m..." class="w-full px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Categoria</label>
                    <select id="habit-categoria" class="w-full px-3.5 py-2.5 rounded-xl bg-[#0d131f] border border-white/10 text-white text-sm focus:outline-none focus:border-indigo-500">
                        <option value="Saúde">Saúde & Bem-estar</option>
                        <option value="Estudos">Estudos & Mente</option>
                        <option value="Rotina">Rotina & Foco</option>
                        <option value="Finanças">Finanças</option>
                    </select>
                </div>
                <button type="submit" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 font-semibold text-sm text-white shadow-lg shadow-indigo-500/25 flex items-center justify-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i> Salvar Hábito (+10 XP)
                </button>
            </form>
        </div>

        <!-- Lista de Hábitos do Dia -->
        <div class="glass-panel p-6 rounded-2xl lg:col-span-2">
            <div class="flex justify-between items-center pb-3 border-b border-white/10 mb-4">
                <h3 class="text-base font-semibold text-white flex items-center gap-2">
                    <i data-lucide="flame" class="w-5 h-5 text-amber-400"></i>
                    Rotina de Hoje
                </h3>
                <span class="text-xs text-indigo-400 font-medium">+10 XP por conclusão</span>
            </div>
            <div id="habit-list" class="space-y-3">
                <p class="text-sm text-slate-500 text-center py-6">Carregando hábitos...</p>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>