<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Vida - Autenticação</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- CSS Customizado -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-[#06090e] flex items-center justify-center min-h-screen p-4 antialiased text-slate-100">

    <div class="w-full max-w-md">
        <!-- Logo e Cabeçalho -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-500 via-purple-500 to-cyan-400 p-0.5 shadow-xl shadow-indigo-500/25 mb-3">
                <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center text-white">
                    <i data-lucide="sparkles" class="w-7 h-7"></i>
                </div>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Painel Vida</h1>
            <p class="text-xs text-slate-400 mt-1">Gerenciamento pessoal, finanças e evolução gamificada.</p>
        </div>

        <!-- Card de Autenticação -->
        <div class="glass-panel p-8 rounded-2xl relative overflow-hidden bg-slate-900/60 backdrop-blur-xl border border-white/10 shadow-2xl">
            <!-- Tabs Alternadoras -->
            <div class="flex border-b border-white/10 mb-6 pb-2 gap-4">
                <button type="button" id="tab-login-btn" onclick="window.switchAuthTab('login')" class="flex-1 pb-2 text-sm font-semibold text-indigo-400 border-b-2 border-indigo-500 transition-all">
                    Entrar
                </button>
                <button type="button" id="tab-register-btn" onclick="window.switchAuthTab('register')" class="flex-1 pb-2 text-sm font-semibold text-slate-400 border-b-2 border-transparent hover:text-slate-200 transition-all">
                    Criar Conta
                </button>
            </div>

            <!-- Feedback de Alerta -->
            <div id="auth-alert" class="hidden mb-4 p-3 rounded-xl text-xs font-medium border"></div>

            <!-- Formulário de LOGIN -->
            <form id="form-login" class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">E-mail</label>
                    <div class="relative">
                        <input type="email" id="login-email" required placeholder="seu@email.com" class="w-full pl-10 pr-3 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:outline-none focus:border-indigo-500 transition-colors">
                        <i data-lucide="mail" class="w-4 h-4 text-slate-500 absolute left-3.5 top-3"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Senha</label>
                    <div class="relative">
                        <input type="password" id="login-password" required placeholder="••••••••" class="w-full pl-10 pr-3 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:outline-none focus:border-indigo-500 transition-colors">
                        <i data-lucide="lock" class="w-4 h-4 text-slate-500 absolute left-3.5 top-3"></i>
                    </div>
                </div>

                <button type="submit" id="btn-submit-login" class="w-full py-3 mt-2 font-semibold text-sm rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white transition-all shadow-lg shadow-indigo-500/25 flex items-center justify-center gap-2">
                    <span>Acessar Painel</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>

            <!-- Formulário de CADASTRO (Oculto por padrão) -->
            <form id="form-register" class="space-y-4 hidden" style="display: none;">
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Nome Completo</label>
                    <div class="relative">
                        <input type="text" id="reg-nome" placeholder="Ex: Lucas Silva" class="w-full pl-10 pr-3 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:outline-none focus:border-purple-500 transition-colors">
                        <i data-lucide="user" class="w-4 h-4 text-slate-500 absolute left-3.5 top-3"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">E-mail</label>
                    <div class="relative">
                        <input type="email" id="reg-email" placeholder="seu@email.com" class="w-full pl-10 pr-3 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:outline-none focus:border-purple-500 transition-colors">
                        <i data-lucide="mail" class="w-4 h-4 text-slate-500 absolute left-3.5 top-3"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Senha</label>
                    <div class="relative">
                        <input type="password" id="reg-password" placeholder="Mínimo 6 caracteres" class="w-full pl-10 pr-3 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:outline-none focus:border-purple-500 transition-colors">
                        <i data-lucide="lock" class="w-4 h-4 text-slate-500 absolute left-3.5 top-3"></i>
                    </div>
                </div>

                <button type="submit" id="btn-submit-register" class="w-full py-3 mt-2 font-semibold text-sm rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white transition-all shadow-lg shadow-purple-500/25 flex items-center justify-center gap-2">
                    <span>Criar Minha Conta</span>
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-slate-500 mt-6">
            &copy; 2026 Painel Vida. Todos os direitos reservados.
        </p>
    </div>

    <script src="../assets/js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof setupAuthPage === 'function') setupAuthPage();
            if (window.lucide) lucide.createIcons();
        });
    </script>
</body>
</html>