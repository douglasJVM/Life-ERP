Aqui está o **README.md** do **Life-ERP**, formatado com a mesma estrutura visual, tabelas, seções de segurança, fluxo de instalação e lista de melhorias futuras:

```markdown
# ⚡ Life-ERP — Painel Vida

> Ecossistema web de gestão pessoal, produtividade e finanças com arquitetura desacoplada e mecânica de gamificação por XP e Níveis.

---

## 📋 Sumário

* [Visão Geral](#-visão-geral)
* [Funcionalidades](#-funcionalidades)
* [Tecnologias](#-tecnologias)
* [Estrutura de Arquivos](#-estrutura-de-arquivos)
* [Instalação e Configuração](#-instalação-e-configuração)
* [Módulos do Sistema](#-módulos-do-sistema)
* [Sistema de Gamificação (XP)](#-sistema-de-gamificação-xp)
* [Segurança e Arquitetura](#-segurança-e-arquitetura)
* [Futuras Implementações](#-futuras-implementações)
* [Licença](#-licença)

---

## 📌 Visão Geral

O **Life-ERP** centraliza o gerenciamento da rotina diária em um único painel inteligente. A aplicação combina finanças pessoais, rotina de treinos, blocos de foco para estudos, rastreador de hábitos e um quadro Kanban para projetos, integrando todas as ações do usuário a um sistema de progressão de níveis para incentivar a consistência diária.

---

## 🚀 Funcionalidades

* 🔐 **Autenticação e Sessão:** Cadastro e login com hash criptográfico, controle de sessão local e autorização via headers HTTP.
* 🎮 **Gamificação Centralizada:** Pontuação de XP em tempo real e cálculo progressivo de níveis (100 XP por nível) com barra de status dinâmica.
* 📊 **Dashboard Consolidado:** Visão geral de métricas, gráfico de rosca de despesas por categoria e feed cronológico unificado.
* 💰 **Gestão Financeira:** Controle de receitas e despesas, saldo em caixa, categorias de gastos e histórico de movimentações.
* 🏋️ **Fitness & Performance:** Registro de treinos com duração, grupamento muscular e multiplicador de XP por intensidade (*Leve, Moderada, Intensa*).
* 📚 **Estudos & Foco:** Lançamento de blocos de estudo com registro de matéria, tópico abordado e cálculo de média diária.
* 🎯 **Habit Tracker:** Checklist diário de hábitos com contador acumulativo de consistência e pontuação imediata.
* 📋 **Quadro Kanban de Tarefas:** Gerenciamento visual de demandas em três colunas (*A Fazer*, *Em Progresso*, *Concluído*) com prazos e prioridades.
* 🎨 **Interface Dark Glassmorphism:** Layout responsivo, moderno e minimalista utilizando Tailwind CSS e ícones SVG leves.

---

## 🛠️ Tecnologias

| Camada | Tecnologia |
| :--- | :--- |
| **Backend** | PHP 8.0+ (REST API com PDO e Composer Autoloader) |
| **Banco de Dados** | MySQL 5.7+ / MariaDB 10.3+ |
| **Frontend** | HTML5, Tailwind CSS, Vanilla JavaScript (ES6+ / Fetch API) |
| **Gráficos & Visualização** | Chart.js |
| **Ícones** | Lucide Icons |
| **Servidor de Execução** | PHP Built-in Server / Apache |

---

## 📂 Estrutura de Arquivos

```text
Life-ERP/
├── api/
│   ├── public/
│   │   └── index.php              # Roteador central da API e headers CORS
│   ├── src/
│   │   ├── Config/
│   │   │   └── Database.php       # Conexão segura via PDO (Singleton)
│   │   ├── Controllers/           # Controladores REST (Auth, Dashboard, Finanças, etc.)
│   │   │   ├── AuthController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── FinanceController.php
│   │   │   ├── FitnessController.php
│   │   │   ├── HabitTrackerController.php
│   │   │   ├── StudyController.php
│   │   │   └── TaskController.php
│   │   └── Models/                # Modelagem, persistência e regras de XP
│   │       ├── User.php
│   │       ├── Transaction.php
│   │       ├── Fitness.php
│   │       └── Study.php
│   └── composer.json              # Mapeamento PSR-4
├── web/
│   ├── assets/
│   │   ├── css/
│   │   │   └── styles.css         # Estilização Glassmorphism customizada
│   │   └── js/
│   │       └── script.js          # Lógica frontend, consumo da API e reatividade DOM
│   └── views/
│       ├── layouts/
│       │   ├── header.php         # Head, Tailwind CDN e Lucide Icons
│       │   ├── sidebar.php        # Navegação lateral com links ativos
│       │   ├── topbar.php         # Perfil, nível dinâmico e logout
│       │   └── footer.php         # Scripts e fechamento de estrutura
│       ├── login.php              # Tela de autenticação e registro
│       ├── dashboard.php          # Visão consolidada do sistema
│       ├── financas.php           # Gestão financeira detalhada
│       ├── academia.php           # Módulo fitness e histórico de treinos
│       ├── estudos.php            # Sessões de foco e estudos
│       ├── habitos.php            # Rastreador de hábitos diários
│       └── projetos.php           # Kanban de tarefas
├── .gitignore                     # Arquivos ignorados pelo versionamento
└── README.md                      # Documentação do projeto

```

---

## ⚙️ Instalação e Configuração

### **Pré-requisitos:**

* PHP 8.0 ou superior
* MySQL Server ou MariaDB ativo
* Composer instalado
* Git

---

### **Passo a Passo:**

**1. Clonar o Repositório**

```bash
git clone [https://github.com/douglasJVM/Life-ERP.git](https://github.com/douglasJVM/Life-ERP.git)
cd Life-ERP

```

**2. Configuração do Banco de Dados**

Crie a base de dados no MySQL:

```sql
CREATE DATABASE IF NOT EXISTS painel_vida CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

```

Execute a criação da estrutura de tabelas:

```sql
USE painel_vida;

-- Usuários
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    nivel INT DEFAULT 1,
    xp_total INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Transações Financeiras
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    descricao VARCHAR(150) NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    tipo ENUM('receita', 'despesa') NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    metodo_pagamento VARCHAR(50) DEFAULT 'PIX',
    data_transacao DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Treinos / Fitness
CREATE TABLE IF NOT EXISTS workouts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tipo VARCHAR(100) NOT NULL,
    duracao_minutos INT NOT NULL,
    intensidade ENUM('leve', 'moderada', 'intensa') DEFAULT 'moderada',
    xp_ganho INT NOT NULL DEFAULT 0,
    data_treino DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Sessões de Estudo
CREATE TABLE IF NOT EXISTS study_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    materia VARCHAR(100) NOT NULL,
    conteudo VARCHAR(255) NULL,
    duracao_minutos INT NOT NULL,
    xp_ganho INT NOT NULL DEFAULT 0,
    data_estudo DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Hábitos
CREATE TABLE IF NOT EXISTS habits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    titulo VARCHAR(100) NOT NULL,
    categoria VARCHAR(50) DEFAULT 'Rotina',
    xp_recompensa INT DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Logs de Hábitos
CREATE TABLE IF NOT EXISTS habit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    habit_id INT NOT NULL,
    user_id INT NOT NULL,
    data_conclusao DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_habit_day (habit_id, data_conclusao),
    FOREIGN KEY (habit_id) REFERENCES habits(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Kanban / Tarefas
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT NULL,
    status ENUM('todo', 'doing', 'done') DEFAULT 'todo',
    prioridade ENUM('baixa', 'media', 'alta') DEFAULT 'media',
    prazo DATE NULL,
    xp_recompensa INT DEFAULT 15,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

```

**3. Configurar Credenciais de Banco**

Edite o arquivo `api/src/Config/Database.php` com seus dados locais:

```php
private string $host = 'localhost';
private string $db_name = 'painel_vida';
private string $username = 'seu_usuario';
private string $password = 'sua_senha';

```

**4. Gerar o Autoload PSR-4**

```bash
cd api
composer dump-autoload -o
cd ..

```

**5. Executar os Servidores Locais**

Em um terminal (Backend API):

```bash
cd api/public
php -S localhost:8000

```

Em outro terminal (Frontend Web):

```bash
cd web
php -S localhost:3000

```

Acesse o sistema no navegador: `http://localhost:3000/views/login.php`

---

## 📦 Módulos do Sistema

| Módulo | Finalidade | Principais Ações |
| --- | --- | --- |
| 📊 **Dashboard** | Visão panorâmica dos indicadores vitais | Balanço financeiro, resumo de treinos, horas de estudo e feed recente |
| 💰 **Finanças** | Controle de receitas e despesas | Lançamentos, categorização, saldo líquido e gráfico de despesas |
| 🏋️ **Fitness** | Acompanhamento de treinos físicos | Grupamento muscular, intensidade, calorias estimadas e histórico |
| 📚 **Estudos** | Monitoramento de tempo de foco | Registro de matéria/assunto, duração em minutos e média diária |
| 🎯 **Hábitos** | Construção de disciplina diária | Check/uncheck diário e contador acumulativo de execuções |
| 📋 **Projetos** | Gestão de entregas pessoais | Quadro Kanban (*To Do*, *Doing*, *Done*) com prioridades |

---

## 🎮 Sistema de Gamificação (XP)

| Atividade Realizada | Regra de Cálculo de XP | Recompensa Média |
| --- | --- | --- |
| 🎯 **Cumprir Hábito Diário** | Fixo por conclusão | `+10 XP` por hábito |
| 📋 **Concluir Tarefa Kanban** | Fixo ao mover para *Done* | `+15 XP` por tarefa |
| 📚 **Sessão de Estudos** | `minutos * 1.15` | `~52 XP` (45 min de foco) |
| 🏋️ **Treino Leve** | `(minutos * 1.25) * 0.8` | `~60 XP` (60 min) |
| 🏋️ **Treino Moderado** | `(minutos * 1.25) * 1.0` | `~75 XP` (60 min) |
| 🏋️ **Treino Intenso** | `(minutos * 1.25) * 1.4` | `~105 XP` (60 min) |

> 🏆 **Evolução de Nível:** A cada **100 XP** acumulados, o usuário sobe de nível automaticamente (`Nível = floor(XP / 100) + 1`).

---

## 🔒 Segurança e Arquitetura

| Mecanismo | Aplicação no Projeto |
| --- | --- |
| **`password_hash()` BCRYPT** | Armazenamento de senhas com hash criptográfico irreversível. |
| **PDO Prepared Statements** | Parâmetros vinculados em todas as queries SQL, eliminando SQL Injection. |
| **Isolamento Multiusuário** | Todas as operações são vinculadas ao `user_id` da sessão com integridade referencial `CASCADE`. |
| **Headers HTTP CORS** | Liberação controlada de origens e métodos (`GET`, `POST`, `OPTIONS`). |
| **Autenticação Desacoplada** | Identificação via header `X-User-Id` nas requisições assíncronas do frontend. |

---

## 🔮 Futuras Implementações

* [ ] **Cronômetro Pomodoro Integrado** — Timer de foco diretamente na aba de estudos com salvamento automático de sessão.
* [ ] **Sistema de Metas & OKRs** — Barras de progresso para metas de médio/longo prazo (financeiras e corporais).
* [ ] **Exportação de Relatórios em PDF** — Geração de balanço mensal de produtividade e finanças.
* [ ] **Autenticação via JWT** — Tokens assinados com expiração para elevar a segurança da API.
* [ ] **Notificações Push / PWA** — Suporte a instalação mobile e lembretes de rotina diária.
* [ ] **Conquistas & Badges** — Medalhas desbloqueáveis ao atingir marcas (ex: "7 dias seguidos de treino", "100 horas de estudo").

---

## 📄 Licença

Uso pessoal e interno. Consulte `LICENSE` para mais informações.
