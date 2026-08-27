// ==========================================
// 1. CARROSSEL DE FINANÇAS
// ==========================================
let financeChartInstance = null;
let currentFinanceIndex = 0;
let cachedFinanceData = null;

const financeChartConfigs = [
    {
        title: 'Despesas por Categoria',
        render: (ctx, data) => renderCategoryChart(ctx, data.categorias || [])
    },
    {
        title: 'Receitas vs Despesas',
        render: (ctx, data) => renderBalanceBarChart(ctx, data.total_receitas || 0, data.total_despesas || 0)
    },
    {
        title: 'Gastos por Método de Pagamento',
        render: (ctx, data) => renderPaymentMethodsChart(ctx, data.historico || [])
    }
];

function initFinanceCharts(data) {
    cachedFinanceData = data;
    renderCurrentFinanceChart();
}

function renderCurrentFinanceChart() {
    const canvas = document.getElementById('financeDetailChart');
    if (!canvas || !cachedFinanceData) return;

    const ctx = canvas.getContext('2d');
    const current = financeChartConfigs[currentFinanceIndex];

    const titleEl = document.getElementById('chart-title');
    if (titleEl) titleEl.textContent = current.title;

    if (financeChartInstance) {
        financeChartInstance.destroy();
    }

    current.render(ctx, cachedFinanceData);

    if (window.lucide) {
        lucide.createIcons();
    }
}

function nextChart() {
    currentFinanceIndex = (currentFinanceIndex + 1) % financeChartConfigs.length;
    renderCurrentFinanceChart();
}

function prevChart() {
    currentFinanceIndex = (currentFinanceIndex - 1 + financeChartConfigs.length) % financeChartConfigs.length;
    renderCurrentFinanceChart();
}

// 1.1 Categoria (Doughnut)
function renderCategoryChart(ctx, categorias) {
    const hasData = Array.isArray(categorias) && categorias.length > 0;
    const labels = hasData ? categorias.map(c => c.categoria) : ['Sem despesas'];
    const values = hasData ? categorias.map(c => parseFloat(c.total)) : [1];
    const colors = hasData 
        ? ['#6366f1', '#10b981', '#f59e0b', '#ec4899', '#8b5cf6', '#06b6d4', '#f43f5e', '#64748b']
        : ['rgba(255, 255, 255, 0.08)'];

    financeChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderWidth: 0,
                hoverOffset: hasData ? 6 : 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { color: '#94a3b8', font: { family: 'Plus Jakarta Sans', size: 11 }, padding: 12 } },
                tooltip: { enabled: hasData }
            },
            cutout: '68%'
        }
    });
}

// 1.2 Balanço Geral (Bar)
function renderBalanceBarChart(ctx, receitas, despesas) {
    financeChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Receitas', 'Despesas'],
            datasets: [{
                data: [parseFloat(receitas) || 0, parseFloat(despesas) || 0],
                backgroundColor: ['#10b981', '#f43f5e'],
                borderRadius: 8,
                barThickness: 45
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#94a3b8' } },
                y: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#94a3b8' } }
            }
        }
    });
}

// 1.3 Métodos de Pagamento (Doughnut)
function renderPaymentMethodsChart(ctx, historico) {
    const despesas = (historico || []).filter(h => h.tipo === 'despesa');
    const mapMetodos = {};

    despesas.forEach(d => {
        const metodo = d.metodo_pagamento || 'Outros';
        mapMetodos[metodo] = (mapMetodos[metodo] || 0) + parseFloat(d.valor);
    });

    const labels = Object.keys(mapMetodos);
    const values = Object.values(mapMetodos);
    const hasData = labels.length > 0;

    financeChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: hasData ? labels : ['Sem dados'],
            datasets: [{
                data: hasData ? values : [1],
                backgroundColor: hasData 
                    ? ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444', '#06b6d4']
                    : ['rgba(255, 255, 255, 0.08)'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { color: '#94a3b8', font: { size: 11 }, padding: 12 } },
                tooltip: { enabled: hasData }
            },
            cutout: '68%'
        }
    });
}


// ==========================================
// 2. CARROSSEL DE FITNESS / ACADEMIA
// ==========================================
let fitnessChartInstance = null;
let currentFitnessIndex = 0;
let cachedFitnessData = null;

const fitnessChartConfigs = [
    {
        title: 'Divisão por Grupamento',
        render: (ctx, data) => renderFitnessGroupChart(ctx, data.grupos || [])
    },
    {
        title: 'Intensidade dos Treinos',
        render: (ctx, data) => renderFitnessIntensityChart(ctx, data.historico || [])
    }
];

function initFitnessCharts(data) {
    cachedFitnessData = data;
    renderCurrentFitnessChart();
}

function renderCurrentFitnessChart() {
    const canvas = document.getElementById('fitnessDetailChart');
    if (!canvas || !cachedFitnessData) return;

    const ctx = canvas.getContext('2d');
    const current = fitnessChartConfigs[currentFitnessIndex];

    const titleEl = document.getElementById('fitness-chart-title');
    if (titleEl) titleEl.textContent = current.title;

    if (fitnessChartInstance) {
        fitnessChartInstance.destroy();
    }

    current.render(ctx, cachedFitnessData);

    if (window.lucide) {
        lucide.createIcons();
    }
}

function nextFitnessChart() {
    currentFitnessIndex = (currentFitnessIndex + 1) % fitnessChartConfigs.length;
    renderCurrentFitnessChart();
}

function prevFitnessChart() {
    currentFitnessIndex = (currentFitnessIndex - 1 + fitnessChartConfigs.length) % fitnessChartConfigs.length;
    renderCurrentFitnessChart();
}

// 2.1 Grupamento Muscular (Doughnut)
function renderFitnessGroupChart(ctx, grupos) {
    const hasData = Array.isArray(grupos) && grupos.length > 0;
    const labels = hasData ? grupos.map(g => g.grupo) : ['Sem treinos registrados'];
    const values = hasData ? grupos.map(g => parseInt(g.total, 10)) : [1];
    const colors = hasData 
        ? ['#06b6d4', '#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#64748b']
        : ['rgba(255, 255, 255, 0.08)'];

    fitnessChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderWidth: 0,
                hoverOffset: hasData ? 6 : 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { color: '#94a3b8', font: { family: 'Plus Jakarta Sans', size: 11 }, padding: 12 } },
                tooltip: { enabled: hasData }
            },
            cutout: '68%'
        }
    });
}

// 2.2 Intensidade (Bar)
function renderFitnessIntensityChart(ctx, historico) {
    const contagem = { leve: 0, moderada: 0, intensa: 0 };
    (historico || []).forEach(h => {
        const key = (h.intensidade || 'moderada').toLowerCase();
        if (contagem[key] !== undefined) contagem[key]++;
    });

    fitnessChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Leve / Cardio', 'Moderada', 'Intensa 🔥'],
            datasets: [{
                data: [contagem.leve, contagem.moderada, contagem.intensa],
                backgroundColor: ['#64748b', '#f59e0b', '#f43f5e'],
                borderRadius: 8,
                barThickness: 45
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#94a3b8' } },
                y: { 
                    beginAtZero: true, 
                    ticks: { stepSize: 1, color: '#94a3b8' }, 
                    grid: { color: 'rgba(255, 255, 255, 0.05)' } 
                }
            }
        }
    });
    let studyChartInstance = null;
let currentStudyIndex = 0;
let cachedStudyData = null;

const studyChartConfigs = [
    {
        title: 'Horas Dedicadas por Matéria',
        render: (ctx, data) => renderStudySubjectChart(ctx, data.materias || [])
    },
    {
        title: 'Distribuição de Foco (Minutos)',
        render: (ctx, data) => renderStudyMinutesBarChart(ctx, data.historico || [])
    }
];

function initStudyCharts(data) {
    cachedStudyData = data;
    renderCurrentStudyChart();
}

function renderCurrentStudyChart() {
    const canvas = document.getElementById('studyDetailChart');
    if (!canvas || !cachedStudyData) return;

    const ctx = canvas.getContext('2d');
    const current = studyChartConfigs[currentStudyIndex];

    const titleEl = document.getElementById('study-chart-title');
    if (titleEl) titleEl.textContent = current.title;

    if (studyChartInstance) {
        studyChartInstance.destroy();
    }

    current.render(ctx, cachedStudyData);

    if (window.lucide) {
        lucide.createIcons();
    }
}

function nextStudyChart() {
    currentStudyIndex = (currentStudyIndex + 1) % studyChartConfigs.length;
    renderCurrentStudyChart();
}

function prevStudyChart() {
    currentStudyIndex = (currentStudyIndex - 1 + studyChartConfigs.length) % studyChartConfigs.length;
    renderCurrentStudyChart();
}

// 3.1 Doughnut por Matéria
function renderStudySubjectChart(ctx, materias) {
    const hasData = Array.isArray(materias) && materias.length > 0;
    const labels = hasData ? materias.map(m => m.materia) : ['Sem registros'];
    const values = hasData ? materias.map(m => parseFloat(m.horas_totais)) : [1];
    const colors = hasData 
        ? ['#8b5cf6', '#6366f1', '#3b82f6', '#06b6d4', '#10b981', '#f59e0b', '#ec4899']
        : ['rgba(255, 255, 255, 0.08)'];

    studyChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderWidth: 0,
                hoverOffset: hasData ? 6 : 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'bottom', 
                    labels: { color: '#94a3b8', font: { family: 'Plus Jakarta Sans', size: 11 }, padding: 12 } 
                },
                tooltip: {
                    enabled: hasData,
                    callbacks: {
                        label: (context) => ` ${context.label}: ${context.raw}h dedicadas`
                    }
                }
            },
            cutout: '68%'
        }
    });
}

// 3.2 Barras por Sessões Recentes
function renderStudyMinutesBarChart(ctx, historico) {
    const recent = (historico || []).slice(0, 7).reverse();
    const hasData = recent.length > 0;

    const labels = hasData ? recent.map(h => h.materia.substring(0, 10)) : ['Sem sessões'];
    const values = hasData ? recent.map(h => parseInt(h.duracao_minutos, 10)) : [0];

    studyChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: '#8b5cf6',
                borderRadius: 8,
                barThickness: 32
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#94a3b8' } },
                y: { 
                    beginAtZero: true, 
                    ticks: { color: '#94a3b8' }, 
                    grid: { color: 'rgba(255, 255, 255, 0.05)' } 
                }
            }
        }
    });
}
}