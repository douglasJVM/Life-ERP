<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Entrar | Life-ERP</title>

    <!-- Configurações PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#6366f1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Life-ERP">
    <link rel="apple-touch-icon" href="/assets/icons/icon-192.png">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-[#06090e] text-slate-100 min-h-screen flex items-center justify-center p-4 antialiased selection:bg-indigo-500 selection:text-white">

    <div class="w-full max-w-md">
        <!-- Logo & Identidade -->
        <div class="text-center mb-8">
            <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-xl shadow-indigo-500/20">
                <i data-lucide="shield" class="w-7 h-7"></i>
            </div>
            <h1 class="text-2xl font-bold tracking-wider text-white">LIFE <span class="text-indigo-400">ERP</span></h1>
            <p class="text-xs text-slate-400 mt-1 uppercase tracking-widest">Painel Pessoal & Gamificação</p>
        </div>

        <!-- Card Principal (Glassmorphism) -->
        <div class="bg-white/[0.03] border border-white/10 rounded-3xl p-6 sm:p-8 backdrop-blur-xl shadow-2xl">
            
            <!-- Abas Login / Cadastro -->
            <div class="flex p-1 bg-white/5 rounded-2xl mb-6 border border-white/5">
                <button type="button" id="tab-login-btn" onclick="toggleAuthMode('login')" class="flex-1 py-2 text-xs font-semibold rounded-xl bg-indigo-600 text-white transition-all shadow-sm">
                    Entrar
                </button>
                <button type="button" id="tab-register-btn" onclick="toggleAuthMode('register')" class="flex-1 py-2 text-xs font-semibold rounded-xl text-slate-400 hover:text-slate-200 transition-all">
                    Criar Conta
                </button>
            </div>

            <!-- Formulário de Login -->
            <form id="form-login" onsubmit="handleAuthSubmit(event, 'login')" class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1.5">E-mail</label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-4 h-4 absolute left-3.5 top-3 text-slate-500"></i>
                        <input type="email" id="login-email" required placeholder="seu@email.com" class="w-full bg-black/40 border border-white/10 rounded-xl pl-10 pr-4 py-2.5 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-indigo-500 transition-colors">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1.5">Senha</label>
                    <div class="relative">
                        <i data-lucide="lock" class="w-4 h-4 absolute left-3.5 top-3 text-slate-500"></i>
                        <input type="password" id="login-password" required placeholder="••••••••" class="w-full bg-black/40 border border-white/10 rounded-xl pl-10 pr-4 py-2.5 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-indigo-500 transition-colors">
                    </div>
                </div>

                <button type="submit" id="btn-submit-login" class="w-full py-3 mt-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow-lg shadow-indigo-600/30 transition-all active:scale-[0.98]">
                    Acessar Painel
                </button>
            </form>

            <!-- Formulário de Cadastro -->
            <form id="form-register" onsubmit="handleAuthSubmit(event, 'register')" class="space-y-4 hidden">
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1.5">Nome Completo</label>
                    <div class="relative">
                        <i data-lucide="user" class="w-4 h-4 absolute left-3.5 top-3 text-slate-500"></i>
                        <input type="text" id="reg-name" required placeholder="Seu Nome" class="w-full bg-black/40 border border-white/10 rounded-xl pl-10 pr-4 py-2.5 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-indigo-500 transition-colors">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1.5">E-mail</label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-4 h-4 absolute left-3.5 top-3 text-slate-500"></i>
                        <input type="email" id="reg-email" required placeholder="seu@email.com" class="w-full bg-black/40 border border-white/10 rounded-xl pl-10 pr-4 py-2.5 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-indigo-500 transition-colors">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1.5">Senha</label>
                    <div class="relative">
                        <i data-lucide="lock" class="w-4 h-4 absolute left-3.5 top-3 text-slate-500"></i>
                        <input type="password" id="reg-password" required placeholder="Mínimo 6 caracteres" class="w-full bg-black/40 border border-white/10 rounded-xl pl-10 pr-4 py-2.5 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-indigo-500 transition-colors">
                    </div>
                </div>

                <button type="submit" id="btn-submit-register" class="w-full py-3 mt-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-sm font-semibold shadow-lg shadow-purple-600/30 transition-all active:scale-[0.98]">
                    Criar Minha Conta
                </button>
            </form>

            <div id="auth-error-msg" class="mt-4 p-3 bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs rounded-xl hidden text-center"></div>
        </div>

        <p class="text-center text-[11px] text-slate-600 mt-6">
            Life-ERP &copy; <?= date('Y') ?> · Todos os direitos reservados.
        </p>
    </div>

    <!-- Scripts -->
    <script src="/assets/js/script.js"></script>
    <script>
        lucide.createIcons();

        function toggleAuthMode(mode) {
            const loginForm = document.getElementById('form-login');
            const regForm = document.getElementById('form-register');
            const loginBtn = document.getElementById('tab-login-btn');
            const regBtn = document.getElementById('tab-register-btn');
            const errorMsg = document.getElementById('auth-error-msg');
            
            if (errorMsg) errorMsg.classList.add('hidden');

            if (mode === 'login') {
                loginForm.classList.remove('hidden');
                regForm.classList.add('hidden');
                loginBtn.className = "flex-1 py-2 text-xs font-semibold rounded-xl bg-indigo-600 text-white transition-all shadow-sm";
                regBtn.className = "flex-1 py-2 text-xs font-semibold rounded-xl text-slate-400 hover:text-slate-200 transition-all";
            } else {
                loginForm.classList.add('hidden');
                regForm.classList.remove('hidden');
                regBtn.className = "flex-1 py-2 text-xs font-semibold rounded-xl bg-purple-600 text-white transition-all shadow-sm";
                loginBtn.className = "flex-1 py-2 text-xs font-semibold rounded-xl text-slate-400 hover:text-slate-200 transition-all";
            }
            lucide.createIcons();
        }

        async function handleAuthSubmit(event, action) {
            event.preventDefault();
            event.stopPropagation();

            const errorDiv = document.getElementById('auth-error-msg');
            const submitBtn = action === 'login' ? document.getElementById('btn-submit-login') : document.getElementById('btn-submit-register');
            
            errorDiv.classList.add('hidden');
            errorDiv.className = 'mt-4 p-3 bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs rounded-xl hidden text-center';

            const payload = { action };

            if (action === 'login') {
                payload.email = (document.getElementById('login-email')?.value || '').trim();
                payload.senha = document.getElementById('login-password')?.value || '';
            } else {
                const nameInput = document.getElementById('reg-name') || document.getElementById('reg-nome');
                payload.nome = (nameInput?.value || '').trim();
                payload.email = (document.getElementById('reg-email')?.value || '').trim();
                payload.senha = document.getElementById('reg-password')?.value || '';
            }

            if (!payload.email || !payload.senha || (action === 'register' && !payload.nome)) {
                errorDiv.textContent = 'Por favor, preencha todos os campos obrigatórios.';
                errorDiv.classList.remove('hidden');
                return;
            }

            if (action === 'register' && payload.senha.length < 6) {
                errorDiv.textContent = 'A senha deve ter no mínimo 6 caracteres.';
                errorDiv.classList.remove('hidden');
                return;
            }

            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Carregando...';

            const apiBase = window.location.hostname === 'localhost' ? 'http://localhost:8000' : '/api';

            try {
                const res = await fetch(`${apiBase}/auth`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();

                if (res.ok && (data.status === 'success' || data.success)) {
                    const user = data.user || data.data || {};
                    localStorage.setItem('life_user_id', user.id || '');
                    localStorage.setItem('life_user_name', user.nome || '');
                    localStorage.setItem('life_user_email', user.email || '');
                    localStorage.setItem('user_session', JSON.stringify(user));

                    errorDiv.className = 'mt-4 p-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs rounded-xl text-center';
                    errorDiv.textContent = action === 'register' ? 'Conta criada! Redirecionando...' : 'Login realizado! Redirecionando...';
                    errorDiv.classList.remove('hidden');

                    setTimeout(() => {
                        window.location.href = '/views/dashboard.php';
                    }, 600);
                } else {
                    errorDiv.textContent = data.message || 'Falha ao processar requisição.';
                    errorDiv.classList.remove('hidden');
                }
            } catch (err) {
                console.error(err);
                errorDiv.textContent = 'Erro de comunicação com o servidor API.';
                errorDiv.classList.remove('hidden');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        }
    </script>
</body>
</html>