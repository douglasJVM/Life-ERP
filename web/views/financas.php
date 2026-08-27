<?php require_once __DIR__ . '/layouts/header.php'; ?>
<?php require_once __DIR__ . '/layouts/sidebar.php'; ?>

<main class="main-content">
    <?php require_once __DIR__ . '/layouts/topbar.php'; ?>
    <header class="flex justify-between items-center pb-4 border-b border-white/10">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-white">Gestão Financeira</h2>
            <p class="text-sm text-slate-400">Controle minucioso de receitas, custos e fluxo de capital.</p>
        </div>
        <div class="text-right">
            <span class="text-xs uppercase tracking-wider text-slate-400">Data Base</span>
            <p class="text-sm font-semibold text-slate-200" id="current-date-display"></p>
        </div>
    </header>

    <!-- Cards Superiores de Métricas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="stat-card glass-panel border-l-4 border-l-emerald-500">
            <div class="stat-header">
                <span>Receitas Totais</span>
                <i data-lucide="arrow-up-circle" class="w-5 h-5 text-emerald-400"></i>
            </div>
            <div id="total-income" class="stat-value text-emerald-400">R$ 0,00</div>
            <span class="text-xs text-slate-400">Entradas acumuladas</span>
        </div>

        <div class="stat-card glass-panel border-l-4 border-l-rose-500">
            <div class="stat-header">
                <span>Despesas Totais</span>
                <i data-lucide="arrow-down-circle" class="w-5 h-5 text-rose-400"></i>
            </div>
            <div id="total-expenses" class="stat-value text-rose-400">R$ 0,00</div>
            <span class="text-xs text-slate-400">Saídas registradas</span>
        </div>

        <div class="stat-card glass-panel border-l-4 border-l-indigo-500">
            <div class="stat-header">
                <span>Saldo em Caixa</span>
                <i data-lucide="wallet" class="w-5 h-5 text-indigo-400"></i>
            </div>
            <div id="total-balance" class="stat-value">R$ 0,00</div>
            <span class="text-xs text-slate-400">Balanço líquido atual</span>
        </div>
    </div>

    <!-- Linha de Ação: Formulário + Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Formulário de Transações -->
        <div class="glass-panel p-6 rounded-2xl lg:col-span-1 flex flex-col gap-4">
            <h3 class="text-lg font-semibold text-slate-200 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-5 h-5 text-indigo-400"></i>
                Novo Lançamento
            </h3>
            <form id="form-transaction" class="space-y-3.5">
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Descrição</label>
                    <input type="text" id="trans-desc" required placeholder="Ex: Assinatura Servidor, Salário..." class="w-full">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Valor (R$)</label>
                        <input type="number" step="0.01" min="0.01" id="trans-val" required placeholder="0.00" class="w-full">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Tipo</label>
                        <select id="trans-tipo" class="w-full">
                            <option value="despesa">Despesa (-)</option>
                            <option value="receita">Receita (+)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Categoria</label>
                        <select id="trans-cat" class="w-full">
                            <option value="Alimentação">Alimentação</option>
                            <option value="Moradia">Moradia</option>
                            <option value="Transporte">Transporte</option>
                            <option value="Educação">Educação</option>
                            <option value="Lazer">Lazer</option>
                            <option value="Saúde">Saúde</option>
                            <option value="Investimento">Investimento</option>
                            <option value="Salário">Salário</option>
                            <option value="Freelance">Freelance</option>
                            <option value="Outros">Outros</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Método</label>
                        <select id="trans-metodo" class="w-full">
                            <option value="Pix">Pix</option>
                            <option value="Cartão de Crédito">Cartão de Crédito</option>
                            <option value="Cartão de Débito">Cartão de Débito</option>
                            <option value="Boleto">Boleto</option>
                            <option value="Dinheiro">Dinheiro</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Data</label>
                    <input type="date" id="trans-data" class="w-full">
                </div>

                <button type="submit" class="btn-primary w-full py-3 mt-1 font-semibold">Efetivar Registro</button>
            </form>
        </div>

        <!-- Card de Gráficos (Apenas com setas de navegação) -->
        <div class="glass-panel p-6 rounded-2xl lg:col-span-2 flex flex-col justify-between">
            <div class="flex justify-between items-center pb-2 border-b border-white/5">
                <div class="flex items-center gap-2">
                    <i data-lucide="pie-chart" class="w-5 h-5 text-indigo-400"></i>
                    <h3 id="chart-title" class="text-lg font-semibold text-slate-200">Composição de Despesas</h3>
                </div>
                
                <!-- Setas de navegação direta -->
                <div class="flex items-center gap-1.5">
                    <button type="button" onclick="prevChart()" class="p-2 rounded-lg bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white border border-white/5 transition-all active:scale-95" title="Anterior">
                        <i data-lucide="chevron-left" class="w-4 h-4"></i>
                    </button>
                    <button type="button" onclick="nextChart()" class="p-2 rounded-lg bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white border border-white/5 transition-all active:scale-95" title="Próximo">
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <div class="relative w-full h-72 flex items-center justify-center my-auto pt-2">
                <canvas id="financeDetailChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tabela de Histórico Recente -->
    <div class="glass-panel p-6 rounded-2xl flex flex-col gap-4">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-slate-200 flex items-center gap-2">
                <i data-lucide="receipt" class="w-5 h-5 text-indigo-400"></i>
                Extrato de Lançamentos
            </h3>
            <span class="text-xs text-slate-400">Últimos 20 registros</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="border-b border-white/10 text-xs text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-4">Data</th>
                        <th class="py-3 px-4">Descrição</th>
                        <th class="py-3 px-4">Categoria</th>
                        <th class="py-3 px-4">Método</th>
                        <th class="py-3 px-4">Valor</th>
                        <th class="py-3 px-4 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody id="transaction-history-body" class="divide-y divide-white/5 text-slate-200">
                    <tr>
                        <td colspan="6" class="py-6 text-center text-slate-500">Carregando movimentações...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>