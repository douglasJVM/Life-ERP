-- 1. Criação do Banco de Dados
CREATE DATABASE IF NOT EXISTS painel_vida 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE painel_vida;

-- 2. Tabela de Usuários (Autenticação e Gamificação)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    nivel INT DEFAULT 1,
    xp_total INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Tabela de Transações Financeiras (Finanças)
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    descricao VARCHAR(150) NOT NULL,
    valor DECIMAL(10, 2) NOT NULL,
    tipo ENUM('receita', 'despesa') NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    metodo_pagamento VARCHAR(50) DEFAULT 'PIX',
    data_transacao DATE DEFAULT (CURRENT_DATE),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_transactions_user (user_id)
) ENGINE=InnoDB;

-- 4. Tabela de Treinos / Fitness (Com Intensidade)
CREATE TABLE IF NOT EXISTS workouts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tipo VARCHAR(100) NOT NULL,
    duracao_minutos INT NOT NULL DEFAULT 45,
    intensidade ENUM('leve', 'moderada', 'intensa') DEFAULT 'moderada',
    xp_ganho INT NOT NULL DEFAULT 50,
    data_treino DATE DEFAULT (CURRENT_DATE),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_workouts_user (user_id)
) ENGINE=InnoDB;

-- 5. Tabela de Sessões de Estudo & Foco
CREATE TABLE IF NOT EXISTS study_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    materia VARCHAR(100) NOT NULL,
    conteudo VARCHAR(255) NULL,
    duracao_minutos INT NOT NULL,
    xp_ganho INT NOT NULL DEFAULT 30,
    data_estudo DATE DEFAULT (CURRENT_DATE),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_study_user (user_id)
) ENGINE=InnoDB;

-- 6. Tabela de Hábitos e Rotinas
CREATE TABLE IF NOT EXISTS habits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    frequencia ENUM('diario', 'semanal') DEFAULT 'diario',
    xp_recompensa INT DEFAULT 10,
    status ENUM('ativo', 'pausado') DEFAULT 'ativo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_habits_user (user_id)
) ENGINE=InnoDB;

-- 7. Histórico de Execução de Hábitos (Com trava de 1 check por dia)
CREATE TABLE IF NOT EXISTS habit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    habit_id INT NOT NULL,
    user_id INT NOT NULL,
    data_conclusao DATE DEFAULT (CURRENT_DATE),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_habit_daily (habit_id, data_conclusao),
    FOREIGN KEY (habit_id) REFERENCES habits(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_habit_logs_date (user_id, data_conclusao)
) ENGINE=InnoDB;

-- 8. Tabela de Tarefas e Projetos (Quadro Kanban)
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
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_tasks_user (user_id)
) ENGINE=InnoDB;

-- 9. Tabela de Metas (Opcional / OKRs)
CREATE TABLE IF NOT EXISTS goals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT NULL,
    status ENUM('pendente', 'em_andamento', 'concluida') DEFAULT 'pendente',
    prazo DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_goals_user (user_id)
) ENGINE=InnoDB;

-- ==========================================================
-- DADOS INICIAIS (SEED)
-- ==========================================================

-- Usuário Principal (Senha padrão do hash: 123456)
INSERT INTO users (id, nome, email, senha, nivel, xp_total) 
VALUES (1, 'Djalma Franco', 'dev@painel.local', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe9Y5d5TjZ3aRkLgVvH9CqN4yZpQzE2Ky', 1, 150)
ON DUPLICATE KEY UPDATE nome=VALUES(nome);

-- Transações Financeiras
INSERT INTO transactions (user_id, descricao, valor, tipo, categoria, metodo_pagamento, data_transacao) VALUES
(1, 'Salário / Bolsa', 1800.00, 'receita', 'Trabalho', 'PIX', CURRENT_DATE),
(1, 'Mensalidade Academia', 110.00, 'despesa', 'Saúde', 'Cartão', CURRENT_DATE),
(1, 'Supermercado & Dieta', 450.00, 'despesa', 'Alimentação', 'PIX', CURRENT_DATE),
(1, 'Assinatura Nuvem / Ferramentas', 45.00, 'despesa', 'Tecnologia', 'Crédito', CURRENT_DATE);

-- Hábitos
INSERT INTO habits (id, user_id, nome, frequencia, xp_recompensa, status) VALUES
(1, 1, 'Treinar Musculação', 'diario', 20, 'ativo'),
(2, 1, 'Estudo Técnico de Programação', 'diario', 20, 'ativo'),
(3, 1, 'Beber 3L de Água', 'diario', 10, 'ativo');

-- Treinos (com intensidade)
INSERT INTO workouts (user_id, tipo, duracao_minutos, intensidade, xp_ganho, data_treino) VALUES
(1, 'Musculação - Superiores', 60, 'intensa', 105, CURRENT_DATE),
(1, 'Musculação - Pernas', 55, 'moderada', 69, DATE_SUB(CURRENT_DATE, INTERVAL 2 DAY));

-- Estudos
INSERT INTO study_sessions (user_id, materia, conteudo, duracao_minutos, xp_ganho, data_estudo) VALUES
(1, 'Backend PHP', 'APIs RESTful e PDO', 90, 104, CURRENT_DATE),
(1, 'Banco de Dados', 'Modelagem Relacional MySQL', 60, 69, DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY));

-- Tarefas Kanban
INSERT INTO tasks (user_id, titulo, descricao, status, prioridade, prazo, xp_recompensa) VALUES
(1, 'Deploy da API REST', 'Configurar conexão PDO com banco em nuvem', 'doing', 'alta', DATE_ADD(CURRENT_DATE, INTERVAL 2 DAY), 15),
(1, 'Testar instalação PWA no celular', 'Validar Service Worker e Manifest', 'todo', 'media', DATE_ADD(CURRENT_DATE, INTERVAL 1 DAY), 15),
(1, 'Refatoração da Sidebar Mobile', 'Criar Bottom Navigation responsiva', 'done', 'alta', CURRENT_DATE, 15);