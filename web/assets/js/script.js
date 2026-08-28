const API_BASE = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
    ? 'http://localhost:8000'
    : '/api';
// ==========================================
// 1. SESSÃO & CONTROLE DE ACESSO
// ==========================================
function getActiveUser() {
    try {
        const session = localStorage.getItem('user_session');
        return session ? JSON.parse(session) : null;
    } catch (e) {
        return null;
    }
}

function requireAuth() {
    const user = getActiveUser();
    const isLoginPage = window.location.pathname.includes('login.php');

    if (!user && !isLoginPage) {
        window.location.replace('login.php');
        return false;
    }
    if (user && isLoginPage) {
        window.location.replace('dashboard.php');
        return false;
    }
    return true;
}

window.logoutUser = function() {
    if (!confirm('Deseja realmente sair da sua conta?')) return;
    localStorage.removeItem('user_session');
    localStorage.clear();
    sessionStorage.clear();
    window.location.replace('login.php');
};

function renderUserData(user) {
    if (!user) return;

    const nome = user.nome || 'Usuário';
    const email = user.email || '';
    
    // Normalização numérica dos dados de Gamificação
    const xpTotal = Number(user.xp_total) || 0;
    const nivel = Number(user.nivel) || Math.floor(xpTotal / 100) + 1;
    const xpProgresso = user.xp_progresso !== undefined ? Number(user.xp_progresso) : (xpTotal % 100);
    const xpFalta = user.xp_para_proximo_nivel !== undefined ? Number(user.xp_para_proximo_nivel) : (100 - xpProgresso);

    // 1. Nomes e E-mails
    setText('header-user-name', nome);
    setText('header-profile-name', nome);
    setText('user-name', nome);
    setText('sidebar-user-name', nome);
    setText('sidebar-user-email', email);

    // 2. Nível e Textos de XP
    setText('header-user-level', `Nível ${nivel}`);
    setText('user-level', `Nível ${nivel}`);
    setText('user-xp-text', `${xpProgresso} / 100 XP (${xpFalta} XP para subir)`);
    setText('dash-total-xp', `+${xpTotal} XP`);

    // 3. Barra de Progresso (limitada entre 0 e 100%)
    const xpBar = document.getElementById('user-xp-bar');
    if (xpBar) {
        const percentualSeguro = Math.min(100, Math.max(0, xpProgresso));
        xpBar.style.width = `${percentualSeguro}%`;
    }

    // 4. Iniciais do Avatar
    const parts = String(nome).trim().split(/\s+/).filter(Boolean);
    const initials = parts.length > 1 
        ? `${parts[0][0]}${parts[parts.length - 1][0]}`.toUpperCase() 
        : (parts[0] ? parts[0][0].toUpperCase() : 'U');

    ['header-user-avatar', 'sidebar-user-avatar', 'user-avatar-initials'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.textContent = initials;
    });
}
async function syncGlobalUserProfile() {
    const user = getActiveUser();
    if (!user) return;

    renderUserData(user);

    try {
        const res = await fetch(`${API_BASE}/dashboard`, {
            headers: { 'X-User-Id': user.id }
        });
        const result = await parseResponse(res);

        if (result && result.status === 'success' && result.dashboard?.user) {
            const freshUser = result.dashboard.user;
            localStorage.setItem('user_session', JSON.stringify(freshUser));
            renderUserData(freshUser);
        }
    } catch (err) {
        console.warn('Servidor offline ou inacessível no momento.');
    }
}

// ==========================================
// 2. CONTROLE DA TELA DE LOGIN / CADASTRO
// ==========================================
window.switchAuthTab = function(tab) {
    const loginForm = document.getElementById('form-login');
    const regForm = document.getElementById('form-register');
    const loginBtn = document.getElementById('tab-login-btn');
    const regBtn = document.getElementById('tab-register-btn');
    const alertBox = document.getElementById('auth-alert');

    if (alertBox) alertBox.classList.add('hidden');

    if (tab === 'login') {
        if (loginForm) loginForm.style.display = 'block';
        if (regForm) regForm.style.display = 'none';
        if (loginBtn) loginBtn.className = "flex-1 pb-2 text-sm font-semibold text-indigo-400 border-b-2 border-indigo-500 transition-all";
        if (regBtn) regBtn.className = "flex-1 pb-2 text-sm font-semibold text-slate-400 border-b-2 border-transparent hover:text-slate-200 transition-all";
    } else {
        if (loginForm) loginForm.style.display = 'none';
        if (regForm) regForm.style.display = 'block';
        if (regBtn) regBtn.className = "flex-1 pb-2 text-sm font-semibold text-purple-400 border-b-2 border-purple-500 transition-all";
        if (loginBtn) loginBtn.className = "flex-1 pb-2 text-sm font-semibold text-slate-400 border-b-2 border-transparent hover:text-slate-200 transition-all";
    }
    if (window.lucide) lucide.createIcons();
};

function showAuthAlert(message, type = 'error') {
    const alertBox = document.getElementById('auth-alert');
    if (!alertBox) return;

    alertBox.textContent = message;
    alertBox.className = "mb-4 p-3 rounded-xl text-xs font-medium border";

    if (type === 'error') {
        alertBox.classList.add('bg-rose-500/10', 'text-rose-400', 'border-rose-500/20');
    } else {
        alertBox.classList.add('bg-emerald-500/10', 'text-emerald-400', 'border-emerald-500/20');
    }
    alertBox.classList.remove('hidden');
}

function setupAuthPage() {
    const loginForm = document.getElementById('form-login');
    const regForm = document.getElementById('form-register');

    if (loginForm) {
        loginForm.onsubmit = async (e) => {
            e.preventDefault();
            const email = document.getElementById('login-email').value.trim();
            const senha = document.getElementById('login-password').value;

            try {
                const res = await fetch(`${API_BASE}/auth`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'login', email, senha })
                });

                const data = await parseResponse(res);
                if (res.ok && data?.status === 'success' && data.user) {
                    localStorage.setItem('user_session', JSON.stringify(data.user));
                    showAuthAlert('Login realizado com sucesso!', 'success');
                    setTimeout(() => window.location.replace('dashboard.php'), 500);
                } else {
                    showAuthAlert(data?.message || 'E-mail ou senha inválidos.');
                }
            } catch (err) {
                showAuthAlert('Erro ao conectar ao servidor backend.');
            }
        };
    }

    //
    /*
    if (regForm) {
        regForm.onsubmit = async (e) => {
            e.preventDefault();
            const nomeInput = document.getElementById('reg-name') || document.getElementById('reg-nome');
            const emailInput = document.getElementById('reg-email');
            const senhaInput = document.getElementById('reg-password') || document.getElementById('reg-senha');

            const nome = nomeInput ? nomeInput.value.trim() : '';
            const email = emailInput ? emailInput.value.trim() : '';
            const senha = senhaInput ? senhaInput.value : '';

            if (!nome || !email || !senha) {
                 alert('Por favor, preencha todos os campos obrigatórios.');
                 return;
            }

            if (senha.length < 6) {
                showAuthAlert('A senha deve ter no mínimo 6 caracteres.');
                return;
            }

            try {
                const res = await fetch(`${API_BASE}/auth`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'register', nome, email, senha })
                });

                const data = await parseResponse(res);
                if (res.ok && data?.status === 'success' && data.user) {
                    localStorage.setItem('user_session', JSON.stringify(data.user));
                    showAuthAlert('Conta criada com sucesso!', 'success');
                    setTimeout(() => window.location.replace('dashboard.php'), 500);
                } else {
                    showAuthAlert(data?.message || 'Erro ao registrar conta.');
                }
            } catch (err) {
                showAuthAlert('Erro ao conectar ao servidor backend.');
            }
        };
    }*/
}


//
// ==========================================
// 3. INICIALIZAÇÃO GERAL DO SISTEMA
// ==========================================
document.addEventListener('DOMContentLoaded', () => {
    if (!requireAuth()) return;

    if (window.location.pathname.includes('login.php')) {
        setupAuthPage();
        if (window.lucide) lucide.createIcons();
        return;
    }

    syncGlobalUserProfile();

    const today = new Date().toISOString().split('T')[0];
    ['trans-data', 'workout-data', 'study-data'].forEach(id => {
        const input = document.getElementById(id);
        if (input && !input.value) input.value = today;
    });

    const dateDisplay = document.getElementById('current-date-display');
    if (dateDisplay) {
        dateDisplay.textContent = new Date().toLocaleDateString('pt-BR', { dateStyle: 'long' });
    }

    initApp();
});

async function initApp() {
    setupForms();
    setupHabitForm();
    setupTaskForm();

    await syncGlobalUserProfile();

    const path = window.location.pathname;
    if (path.includes('financas.php')) {
        await loadFinances();
    } else if (path.includes('academia.php')) {
        await loadFitness();
    } else if (path.includes('estudos.php')) {
        await loadStudies();
    } else if (path.includes('habitos.php')) {
        await loadHabits();
    } else if (path.includes('projetos.php')) {
        await loadTasks();
    } else {
        await loadDashboard();
    }
}

function setupForms() {
    setupFinanceForm();
    setupFitnessForm();
    setupStudyForm();
}

// ==========================================
// 4. MÓDULO: DASHBOARD
// ==========================================
    async function loadDashboard() {
        const user = getActiveUser();
        try {
            const response = await fetch(`${API_BASE}/dashboard`, {
                headers: user ? { 'X-User-Id': user.id } : {}
            });
            const result = await parseResponse(response);
            if (!result || result.status !== 'success' || !result.dashboard) return;

            const { financas, fitness, estudos, feed } = result.dashboard;

            // Dentro de loadDashboard() no script.js:
if (financas) {
            // Aceita tanto saldo_liquido quanto saldo_atual
            const saldo = parseFloat(financas.saldo_liquido ?? financas.saldo_atual ?? 0);
            const receitas = parseFloat(financas.total_receitas ?? 0);
            const despesas = parseFloat(financas.total_despesas ?? 0);

            const formataMoeda = (val) => {
                if (typeof formatBRL === 'function') return formatBRL(val);
                return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(val);
            };

    // Atualiza os elementos de saldo na tela
            const saldoEl = document.getElementById('finance-balance') || 
                            document.getElementById('dash-finance-balance') || 
                            document.querySelector('[data-finance-balance]');

            if (saldoEl) {
                saldoEl.textContent = formataMoeda(saldo);
                saldoEl.style.color = saldo >= 0 ? '#10b981' : '#f43f5e';
            }

            const fluxoEl = document.getElementById('dash-finance-flow');
            if (fluxoEl) {
                fluxoEl.textContent = `Rec: ${formataMoeda(receitas)} | Desp: ${formataMoeda(despesas)}`;
            }

            const canvas = document.getElementById('expensesChart');
            if (canvas && typeof Chart !== 'undefined' && typeof renderDashboardChart === 'function') {
                    renderDashboardChart(canvas, financas.categorias || financas.despesas_categoria || []);
                }
            }

            if (fitness) {
                setText('weekly-workouts-count', `${fitness.treinos_semana || 0} treinos`);
                setText('dash-fitness-time', `${fitness.minutos_semana || 0} min em atividade`);
            }

            if (estudos) {
                setText('weekly-studies-hours', `${estudos.horas_semana || 0}h`);
                setText('dash-studies-sessions', `${estudos.sessoes_semana || 0} blocos de estudo`);
            }

            const feedContainer = document.getElementById('dash-recent-feed');
            if (feedContainer) {
                if (!feed || feed.length === 0) {
                    feedContainer.innerHTML = `<div class="py-4 text-center text-xs text-slate-500">Nenhuma atividade registrada recentemente.</div>`;
                } else {
                    feedContainer.innerHTML = feed.map(item => `
                        <div class="p-3 rounded-xl bg-white/[0.02] border border-white/5 flex items-center justify-between hover:bg-white/[0.04] transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-2.5 h-2.5 rounded-full ${
                                    item.color === 'emerald' ? 'bg-emerald-400 shadow-sm shadow-emerald-400/50' :
                                    item.color === 'rose' ? 'bg-rose-400 shadow-sm shadow-rose-400/50' :
                                    item.color === 'cyan' ? 'bg-cyan-400 shadow-sm shadow-cyan-400/50' :
                                    'bg-purple-400 shadow-sm shadow-purple-400/50'
                                }"></div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-200">${item.titulo}</p>
                                    <p class="text-xs text-slate-400">${item.subtitulo} • <span class="text-slate-500">${item.data}</span></p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-white/5 border border-white/10 text-slate-300">${item.badge}</span>
                        </div>
                    `).join('');
                }
            }

            if (window.lucide) lucide.createIcons();
        } catch (error) {
            console.error('Erro ao carregar Dashboard:', error);
        }
    }

function renderDashboardChart(canvas, categorias) {
    const ctx = canvas.getContext('2d');
    const hasData = Array.isArray(categorias) && categorias.length > 0;
    const labels = hasData ? categorias.map(c => c.categoria) : ['Sem despesas'];
    const values = hasData ? categorias.map(c => parseFloat(c.total)) : [1];

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: hasData 
                    ? ['#6366f1', '#10b981', '#f59e0b', '#ec4899', '#8b5cf6', '#06b6d4', '#f43f5e']
                    : ['rgba(255, 255, 255, 0.08)'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { color: '#94a3b8', font: { size: 11 } } },
                tooltip: { enabled: hasData }
            },
            cutout: '68%'
        }
    });
}

// ==========================================
// 5. FINANÇAS
// ==========================================
async function loadFinances() {
    const user = getActiveUser();
    try {
        const response = await fetch(`${API_BASE}/financas`, {
            headers: user ? { 'X-User-Id': user.id } : {}
        });
        const result = await parseResponse(response);

        if (result && result.status === 'success') {
            const { financas } = result;
            setText('total-income', formatBRL(financas.total_receitas));
            setText('total-expenses', formatBRL(financas.total_despesas));
            
            const saldoEl = document.getElementById('total-balance');
            if (saldoEl) {
                saldoEl.textContent = formatBRL(financas.saldo_liquido);
                saldoEl.style.color = financas.saldo_liquido >= 0 ? '#10b981' : '#f43f5e';
            }

            if (typeof initFinanceCharts === 'function' && document.getElementById('financeDetailChart')) {
                initFinanceCharts(financas);
            }

            renderHistoryTable(financas.historico || []);
        }
    } catch (error) {
        console.error('Erro ao carregar Finanças:', error);
    }
}

function renderHistoryTable(records) {
    const tbody = document.getElementById('transaction-history-body');
    if (!tbody) return;

    if (records.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="py-6 text-center text-slate-400">Nenhuma movimentação registrada.</td></tr>`;
        return;
    }

    tbody.innerHTML = records.map(item => {
        const isReceita = item.tipo === 'receita';
        const colorClass = isReceita ? 'text-emerald-400' : 'text-rose-400';
        const prefix = isReceita ? '+ ' : '- ';

        return `
            <tr class="hover:bg-white/[0.02] transition-colors">
                <td class="py-3 px-4 text-xs text-slate-400">${item.data_formatada}</td>
                <td class="py-3 px-4 font-medium text-slate-100">${item.descricao}</td>
                <td class="py-3 px-4"><span class="px-2.5 py-1 rounded-full text-xs bg-slate-800 text-slate-300 border border-white/5">${item.categoria}</span></td>
                <td class="py-3 px-4 text-xs text-slate-400">${item.metodo_pagamento}</td>
                <td class="py-3 px-4 font-semibold ${colorClass}">${prefix}${formatBRL(item.valor)}</td>
                <td class="py-3 px-4 text-center">
                    <button onclick="deleteTransaction(${item.id})" class="p-1.5 text-slate-400 hover:text-rose-400 rounded-lg hover:bg-rose-500/10 transition-colors" title="Excluir Lançamento">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');

    if (window.lucide) lucide.createIcons();
}

function setupFinanceForm() {
    const form = document.getElementById('form-transaction');
    if (!form) return;

    form.onsubmit = async (e) => {
        e.preventDefault();
        const user = getActiveUser();

        const payload = {
            descricao: document.getElementById('trans-desc').value.trim(),
            valor: parseFloat(document.getElementById('trans-val').value),
            tipo: document.getElementById('trans-tipo').value,
            categoria: document.getElementById('trans-cat').value,
            metodo_pagamento: document.getElementById('trans-metodo').value,
            data_transacao: document.getElementById('trans-data').value
        };

        try {
            const response = await fetch(`${API_BASE}/financas`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    ...(user ? { 'X-User-Id': user.id } : {})
                },
                body: JSON.stringify(payload)
            });

            const data = await parseResponse(response);
            if (response.ok && data?.status === 'success') {
                form.reset();
                document.getElementById('trans-data').value = new Date().toISOString().split('T')[0];
                await loadFinances();
                await syncGlobalUserProfile();
            } else {
                alert(data?.message || 'Erro ao registrar transação.');
            }
        } catch (error) {
            console.error('Erro na requisição:', error);
        }
    };
}

async function deleteTransaction(id) {
    if (!confirm('Deseja realmente remover esta movimentação?')) return;
    const user = getActiveUser();

    try {
        const res = await fetch(`${API_BASE}/financas`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                ...(user ? { 'X-User-Id': user.id } : {})
            },
            body: JSON.stringify({ action: 'delete', id })
        });
        const data = await parseResponse(res);
        if (res.ok && data?.status === 'success') {
            await loadFinances();
            await syncGlobalUserProfile();
        } else {
            alert(data?.message || 'Falha ao remover item.');
        }
    } catch (err) {
        console.error('Erro ao deletar:', err);
    }
}

// ==========================================
// 6. FITNESS
// ==========================================
async function loadFitness() {
    const user = getActiveUser();
    try {
        const response = await fetch(`${API_BASE}/fitness`, {
            headers: user ? { 'X-User-Id': user.id } : {}
        });
        const result = await parseResponse(response);

        if (result && result.status === 'success') {
            const { fitness } = result;
            const totalTreinos = fitness.treinos_ultimos_7_dias || 0;
            const minutos = fitness.minutos_semana || 0;
            const xp = fitness.xp_semana || 0;
            const calorias = Math.round(minutos * 7.5);

            setText('workouts-count', `${totalTreinos} treinos`);
            setText('workouts-duration', `${minutos} min`);
            setText('workouts-calories', `${calorias} kcal`);
            setText('workouts-xp', `+${xp} XP`);

            if (typeof initFitnessCharts === 'function' && document.getElementById('fitnessDetailChart')) {
                initFitnessCharts(fitness);
            }

            renderWorkoutTable(fitness.historico || []);
            updateWorkoutXpPreview();
        }
    } catch (error) {
        console.error('Erro ao carregar Treinos:', error);
    }
}

function updateWorkoutXpPreview() {
    const duracaoEl = document.getElementById('workout-duracao');
    const intensidadeEl = document.getElementById('workout-intensidade');
    const previewEl = document.getElementById('workout-preview-xp');
    if (!duracaoEl || !intensidadeEl || !previewEl) return;

    const duracao = parseInt(duracaoEl.value, 10) || 45;
    const intensidade = intensidadeEl.value;
    const mult = intensidade === 'intensa' ? 1.4 : (intensidade === 'leve' ? 0.8 : 1.0);
    const xp = Math.ceil((duracao * 1.25) * mult);

    previewEl.textContent = `+${xp} XP`;
}

function renderWorkoutTable(records) {
    const tbody = document.getElementById('workout-history-body');
    if (!tbody) return;

    if (records.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="py-6 text-center text-slate-400">Nenhum treino registrado ainda.</td></tr>`;
        return;
    }

    tbody.innerHTML = records.map(item => `
        <tr class="hover:bg-white/[0.02] transition-colors">
            <td class="py-3 px-4 text-xs text-slate-400">${item.data_formatada}</td>
            <td class="py-3 px-4 font-semibold text-slate-100">${item.tipo}</td>
            <td class="py-3 px-4 text-xs text-slate-400">${item.duracao_minutos} min</td>
            <td class="py-3 px-4">
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold ${
                    item.intensidade === 'intensa' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' :
                    item.intensidade === 'leve' ? 'bg-slate-800 text-slate-400 border border-white/5' :
                    'bg-amber-500/10 text-amber-400 border border-amber-500/20'
                }">
                    ${item.intensidade.toUpperCase()}
                </span>
            </td>
            <td class="py-3 px-4 font-semibold text-purple-400">+${item.xp_ganho} XP</td>
            <td class="py-3 px-4 text-center">
                <button onclick="deleteWorkout(${item.id})" class="p-1.5 text-slate-400 hover:text-rose-400 rounded-lg hover:bg-rose-500/10 transition-colors" title="Excluir Treino">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </td>
        </tr>
    `).join('');

    if (window.lucide) lucide.createIcons();
}

function setupFitnessForm() {
    const form = document.getElementById('form-workout');
    if (!form) return;

    const duracaoEl = document.getElementById('workout-duracao');
    const intensidadeEl = document.getElementById('workout-intensidade');
    if (duracaoEl) duracaoEl.addEventListener('input', updateWorkoutXpPreview);
    if (intensidadeEl) intensidadeEl.addEventListener('change', updateWorkoutXpPreview);

    form.onsubmit = async (e) => {
        e.preventDefault();
        const user = getActiveUser();

        const payload = {
            tipo: document.getElementById('workout-tipo').value.trim(),
            duracao_minutos: parseInt(document.getElementById('workout-duracao').value, 10),
            intensidade: document.getElementById('workout-intensidade').value,
            data_treino: document.getElementById('workout-data').value || new Date().toISOString().split('T')[0]
        };

        try {
            const response = await fetch(`${API_BASE}/fitness`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    ...(user ? { 'X-User-Id': user.id } : {})
                },
                body: JSON.stringify(payload)
            });

            const data = await parseResponse(response);
            if (response.ok && data?.status === 'success') {
                form.reset();
                document.getElementById('workout-data').value = new Date().toISOString().split('T')[0];
                document.getElementById('workout-duracao').value = '60';
                updateWorkoutXpPreview();
                await loadFitness();
                await syncGlobalUserProfile();
            } else {
                alert(data?.message || 'Erro ao registrar treino.');
            }
        } catch (error) {
            console.error('Erro na requisição de fitness:', error);
        }
    };
}

async function deleteWorkout(id) {
    if (!confirm('Deseja realmente remover este registro de treino?')) return;
    const user = getActiveUser();

    try {
        const res = await fetch(`${API_BASE}/fitness`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                ...(user ? { 'X-User-Id': user.id } : {})
            },
            body: JSON.stringify({ action: 'delete', id })
        });
        const data = await parseResponse(res);
        if (res.ok && data?.status === 'success') {
            await loadFitness();
            await syncGlobalUserProfile();
        } else {
            alert(data?.message || 'Falha ao remover item.');
        }
    } catch (err) {
        console.error('Erro ao deletar treino:', err);
    }
}

// ==========================================
// 7. ESTUDOS
// ==========================================
async function loadStudies() {
    const user = getActiveUser();
    try {
        const response = await fetch(`${API_BASE}/estudos`, {
            headers: user ? { 'X-User-Id': user.id } : {}
        });
        const result = await parseResponse(response);

        if (result && result.status === 'success') {
            const { estudos } = result;
            const sessoes = estudos.sessoes_semana || 0;
            const horas = estudos.horas_totais || 0;
            const xp = estudos.xp_semana || 0;
            const mediaDiaria = sessoes > 0 ? Math.round((horas * 60) / 7) : 0;

            setText('studies-sessions-count', `${sessoes} blocos`);
            setText('studies-total-hours', `${horas}h`);
            setText('studies-avg-daily', `${mediaDiaria} min/dia`);
            setText('studies-xp', `+${xp} XP`);

            if (typeof initStudyCharts === 'function' && document.getElementById('studyDetailChart')) {
                initStudyCharts(estudos);
            }

            renderStudyTable(estudos.historico || []);
            updateStudyXpPreview();
        }
    } catch (error) {
        console.error('Erro ao carregar Estudos:', error);
    }
}

function updateStudyXpPreview() {
    const tempoEl = document.getElementById('study-tempo');
    const previewEl = document.getElementById('study-preview-xp');
    if (!tempoEl || !previewEl) return;

    const minutos = parseInt(tempoEl.value, 10) || 45;
    const xp = Math.ceil(minutos * 1.15);
    previewEl.textContent = `+${xp} XP`;
}

function renderStudyTable(records) {
    const tbody = document.getElementById('study-history-body');
    if (!tbody) return;

    if (records.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="py-6 text-center text-slate-400">Nenhuma sessão registrada ainda.</td></tr>`;
        return;
    }

    tbody.innerHTML = records.map(item => `
        <tr class="hover:bg-white/[0.02] transition-colors">
            <td class="py-3 px-4 text-xs text-slate-400">${item.data_formatada}</td>
            <td class="py-3 px-4 font-semibold text-slate-100">${item.materia}</td>
            <td class="py-3 px-4 text-xs text-slate-300">
                <span class="px-2 py-0.5 rounded bg-white/5 border border-white/5">
                    ${item.conteudo || 'Geral'}
                </span>
            </td>
            <td class="py-3 px-4 text-xs text-slate-400">${item.duracao_minutos} min</td>
            <td class="py-3 px-4 font-semibold text-emerald-400">+${item.xp_ganho} XP</td>
            <td class="py-3 px-4 text-center">
                <button onclick="deleteStudy(${item.id})" class="p-1.5 text-slate-400 hover:text-rose-400 rounded-lg hover:bg-rose-500/10 transition-colors" title="Excluir Sessão">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </td>
        </tr>
    `).join('');

    if (window.lucide) lucide.createIcons();
}

function setupStudyForm() {
    const form = document.getElementById('form-study');
    if (!form) return;

    const tempoEl = document.getElementById('study-tempo');
    if (tempoEl) tempoEl.addEventListener('input', updateStudyXpPreview);

    form.onsubmit = async (e) => {
        e.preventDefault();
        const user = getActiveUser();

        const payload = {
            materia: document.getElementById('study-materia').value.trim(),
            conteudo: document.getElementById('study-conteudo').value.trim(),
            duracao_minutos: parseInt(document.getElementById('study-tempo').value, 10),
            data_estudo: document.getElementById('study-data').value || new Date().toISOString().split('T')[0]
        };

        try {
            const response = await fetch(`${API_BASE}/estudos`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    ...(user ? { 'X-User-Id': user.id } : {})
                },
                body: JSON.stringify(payload)
            });

            const data = await parseResponse(response);
            if (response.ok && data?.status === 'success') {
                form.reset();
                document.getElementById('study-data').value = new Date().toISOString().split('T')[0];
                document.getElementById('study-tempo').value = '45';
                updateStudyXpPreview();
                await loadStudies();
                await syncGlobalUserProfile();
            } else {
                alert(data?.message || 'Erro ao registrar sessão.');
            }
        } catch (error) {
            console.error('Erro na requisição de estudos:', error);
        }
    };
}

async function deleteStudy(id) {
    if (!confirm('Deseja realmente remover esta sessão de estudos?')) return;
    const user = getActiveUser();

    try {
        const res = await fetch(`${API_BASE}/estudos`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                ...(user ? { 'X-User-Id': user.id } : {})
            },
            body: JSON.stringify({ action: 'delete', id })
        });
        const data = await parseResponse(res);
        if (res.ok && data?.status === 'success') {
            await loadStudies();
            await syncGlobalUserProfile();
        } else {
            alert(data?.message || 'Falha ao remover item.');
        }
    } catch (err) {
        console.error('Erro ao deletar sessão:', err);
    }
}

// ==========================================
// 8. HÁBITOS
// ==========================================
async function loadHabits() {
    const user = getActiveUser();
    const container = document.getElementById('habit-list');
    if (!container) return;

    try {
        const res = await fetch(`${API_BASE}/habitos`, {
            headers: user ? { 'X-User-Id': user.id } : {}
        });
        const data = await parseResponse(res);

        if (!data || data.status !== 'success' || !Array.isArray(data.habitos)) {
            container.innerHTML = `<p class="text-sm text-slate-500 text-center py-6">Erro ao carregar hábitos.</p>`;
            return;
        }

        if (data.habitos.length === 0) {
            container.innerHTML = `<p class="text-sm text-slate-500 text-center py-6">Nenhum hábito cadastrado ainda.</p>`;
            return;
        }

        container.innerHTML = data.habitos.map(h => {
            const isDone = Number(h.concluido_hoje) === 1;
            return `
                <div class="p-4 rounded-xl border flex items-center justify-between transition-all ${
                    isDone 
                    ? 'bg-emerald-500/10 border-emerald-500/30' 
                    : 'bg-white/[0.02] border-white/5 hover:border-white/10'
                }">
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="toggleHabit(${h.id})" class="w-6 h-6 rounded-lg flex items-center justify-center transition-all ${
                            isDone 
                            ? 'bg-emerald-500 text-white' 
                            : 'border border-white/20 hover:border-indigo-400 bg-white/5'
                        }">
                            ${isDone ? '<i data-lucide="check" class="w-4 h-4"></i>' : ''}
                        </button>
                        <div>
                            <p class="text-sm font-semibold ${isDone ? 'line-through text-slate-400' : 'text-slate-100'}">${h.titulo}</p>
                            <p class="text-xs text-slate-500">${h.categoria} • <span class="text-indigo-400">${h.total_conclusoes}x</span> cumprido</p>
                        </div>
                    </div>
                    <button type="button" onclick="deleteHabit(${h.id})" class="text-slate-500 hover:text-rose-400 p-1.5 transition-colors" title="Excluir Hábito">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
            `;
        }).join('');

        if (window.lucide) lucide.createIcons();
    } catch (e) {
        console.error('Erro ao carregar hábitos:', e);
        container.innerHTML = `<p class="text-sm text-rose-400 text-center py-6">Erro de conexão ao buscar hábitos.</p>`;
    }
}

async function toggleHabit(habitId) {
    const user = getActiveUser();
    try {
        await fetch(`${API_BASE}/habitos`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', ...(user ? { 'X-User-Id': user.id } : {}) },
            body: JSON.stringify({ action: 'toggle', habit_id: habitId })
        });
        await loadHabits();
        await syncGlobalUserProfile();
    } catch (e) {
        console.error('Erro ao alternar hábito:', e);
    }
}

async function deleteHabit(habitId) {
    if (!confirm('Deseja realmente remover este hábito?')) return;
    const user = getActiveUser();
    try {
        await fetch(`${API_BASE}/habitos`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', ...(user ? { 'X-User-Id': user.id } : {}) },
            body: JSON.stringify({ action: 'delete', habit_id: habitId })
        });
        await loadHabits();
    } catch (e) {
        console.error('Erro ao remover hábito:', e);
    }
}

function setupHabitForm() {
    const form = document.getElementById('form-habit');
    if (!form) return;

    form.onsubmit = async (e) => {
        e.preventDefault();
        const user = getActiveUser();
        const titulo = document.getElementById('habit-titulo')?.value.trim();
        const categoria = document.getElementById('habit-categoria')?.value || 'Rotina';

        if (!titulo) return;

        try {
            const res = await fetch(`${API_BASE}/habitos`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', ...(user ? { 'X-User-Id': user.id } : {}) },
                body: JSON.stringify({ action: 'create', titulo, categoria })
            });

            const data = await parseResponse(res);
            if (res.ok && data?.status === 'success') {
                form.reset();
                await loadHabits();
                await syncGlobalUserProfile();
            } else {
                alert(data?.message || 'Erro ao criar hábito.');
            }
        } catch (e) {
            console.error('Erro ao cadastrar hábito:', e);
        }
    };
}

// ==========================================
// 9. PROJETOS & TAREFAS (KANBAN)
// ==========================================
async function loadTasks() {
    const user = getActiveUser();
    try {
        const res = await fetch(`${API_BASE}/tarefas`, {
            headers: user ? { 'X-User-Id': user.id } : {}
        });
        const data = await parseResponse(res);
        if (!data || data.status !== 'success') return;

        const tasks = data.tasks || [];
        const todoCol = document.getElementById('col-todo');
        const doingCol = document.getElementById('col-doing');
        const doneCol = document.getElementById('col-done');

        if (!todoCol || !doingCol || !doneCol) return;

        const renderCard = (t) => `
            <div class="p-3.5 rounded-xl bg-white/[0.03] border border-white/5 hover:border-white/15 space-y-2.5 shadow-sm">
                <div class="flex justify-between items-start">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase ${
                        t.prioridade === 'alta' ? 'bg-rose-500/10 text-rose-400' :
                        t.prioridade === 'baixa' ? 'bg-slate-800 text-slate-400' : 'bg-amber-500/10 text-amber-400'
                    }">${t.prioridade}</span>
                    <button type="button" onclick="deleteTask(${t.id})" class="text-slate-500 hover:text-rose-400"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i></button>
                </div>
                <p class="text-xs font-semibold text-slate-200">${t.titulo}</p>
                <div class="flex justify-between items-center pt-2 border-t border-white/5 text-[11px] text-slate-400">
                    <span>${t.prazo || 'Sem prazo'}</span>
                    <div class="flex gap-1">
                        ${t.status !== 'todo' ? `<button type="button" onclick="updateTaskStatus(${t.id}, 'todo')" title="Mover para A Fazer" class="p-1 hover:text-white"><i data-lucide="arrow-left" class="w-3.5 h-3.5"></i></button>` : ''}
                        ${t.status !== 'doing' ? `<button type="button" onclick="updateTaskStatus(${t.id}, 'doing')" title="Mover para Progresso" class="p-1 hover:text-amber-400"><i data-lucide="play" class="w-3.5 h-3.5"></i></button>` : ''}
                        ${t.status !== 'done' ? `<button type="button" onclick="updateTaskStatus(${t.id}, 'done')" title="Concluir (+15 XP)" class="p-1 hover:text-emerald-400"><i data-lucide="check" class="w-3.5 h-3.5"></i></button>` : ''}
                    </div>
                </div>
            </div>
        `;

        const todoList = tasks.filter(t => t.status === 'todo');
        const doingList = tasks.filter(t => t.status === 'doing');
        const doneList = tasks.filter(t => t.status === 'done');

        setText('count-todo', todoList.length);
        setText('count-doing', doingList.length);
        setText('count-done', doneList.length);

        todoCol.innerHTML = todoList.map(renderCard).join('');
        doingCol.innerHTML = doingList.map(renderCard).join('');
        doneCol.innerHTML = doneList.map(renderCard).join('');

        if (window.lucide) lucide.createIcons();
    } catch (e) {
        console.error('Erro ao carregar tarefas:', e);
    }
}

async function updateTaskStatus(taskId, status) {
    const user = getActiveUser();
    try {
        await fetch(`${API_BASE}/tarefas`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', ...(user ? { 'X-User-Id': user.id } : {}) },
            body: JSON.stringify({ action: 'update_status', task_id: taskId, status })
        });
        await loadTasks();
        if (status === 'done') await syncGlobalUserProfile();
    } catch (e) {
        console.error('Erro ao atualizar status da tarefa:', e);
    }
}

async function deleteTask(taskId) {
    if (!confirm('Deseja realmente remover esta tarefa?')) return;
    const user = getActiveUser();
    try {
        await fetch(`${API_BASE}/tarefas`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', ...(user ? { 'X-User-Id': user.id } : {}) },
            body: JSON.stringify({ action: 'delete', task_id: taskId })
        });
        await loadTasks();
    } catch (e) {
        console.error('Erro ao deletar tarefa:', e);
    }
}

function setupTaskForm() {
    const form = document.getElementById('form-task');
    if (!form) return;

    form.onsubmit = async (e) => {
        e.preventDefault();
        const user = getActiveUser();
        const titulo = document.getElementById('task-titulo')?.value.trim();
        const prioridade = document.getElementById('task-prioridade')?.value || 'media';
        const prazo = document.getElementById('task-prazo')?.value || null;

        if (!titulo) return;

        try {
            const res = await fetch(`${API_BASE}/tarefas`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', ...(user ? { 'X-User-Id': user.id } : {}) },
                body: JSON.stringify({ action: 'create', titulo, prioridade, prazo })
            });

            const data = await parseResponse(res);
            if (res.ok && data?.status === 'success') {
                form.reset();
                const modal = document.getElementById('modal-task');
                if (modal) modal.classList.add('hidden');
                await loadTasks();
            } else {
                alert(data?.message || 'Erro ao criar tarefa.');
            }
        } catch (e) {
            console.error('Erro ao cadastrar tarefa:', e);
        }
    };
}

// ==========================================
// 10. HELPERS
// ==========================================
async function parseResponse(response) {
    const text = await response.text();
    try {
        return JSON.parse(text);
    } catch (e) {
        return null;
    }
}

function setText(id, text) {
    const el = document.getElementById(id);
    if (el) el.textContent = text;
}

function formatBRL(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value || 0);
}

// ==========================================
// REGISTRO DE PWA (SERVICE WORKER)
// ==========================================
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(reg => console.log('Service Worker registrado com sucesso!', reg.scope))
            .catch(err => console.warn('Falha ao registrar Service Worker:', err));
    });
}