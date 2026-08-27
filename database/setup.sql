-- 1. Criação do Banco de Dados
CREATE DATABASE IF NOT EXISTS painel_vida 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE painel_vida;

-- 2. Tabela de Usuários (base para autenticação e gamificação/XP)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    nivel INT DEFAULT 1,
    xp_total INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Tabela de Transações Financeiras (FinanceController / Transaction.php)
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    descricao VARCHAR(150) NOT NULL,
    valor DECIMAL(10, 2) NOT NULL,
    tipo ENUM('receita', 'despesa') NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    data_transacao DATE DEFAULT (CURRENT_DATE),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_transactions_user (user_id)
) ENGINE=InnoDB;

-- 4. Tabela de Metas (Goal.php)
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

-- 5. Tabela de Hábitos e Rotinas (Habit.php)
CREATE TABLE IF NOT EXISTS habits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    frequencia ENUM('diario', 'semanal') DEFAULT 'diario',
    xp_recompensa INT DEFAULT 20,
    status ENUM('ativo', 'pausado') DEFAULT 'ativo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_habits_user (user_id)
) ENGINE=InnoDB;

-- 6. Histórico de Execução de Hábitos (para contagem semanal/streak no Dashboard)
CREATE TABLE IF NOT EXISTS habit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    habit_id INT NOT NULL,
    user_id INT NOT NULL,
    data_conclusao DATE DEFAULT (CURRENT_DATE),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (habit_id) REFERENCES habits(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_habit_logs_date (user_id, data_conclusao)
) ENGINE=InnoDB;

-- 7. Tabela de Treinos / Fitness (FitnessController.php / workouts)
CREATE TABLE IF NOT EXISTS workouts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tipo VARCHAR(100) NOT NULL,
    duracao_minutos INT DEFAULT 0,
    xp_ganho INT DEFAULT 50,
    data_treino DATE DEFAULT (CURRENT_DATE),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_workouts_user (user_id)
) ENGINE=InnoDB;

-- 8. Tabela de Sessões de Estudo (StudyController.php)
CREATE TABLE IF NOT EXISTS study_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    materia VARCHAR(100) NOT NULL,
    duracao_minutos INT NOT NULL,
    xp_ganho INT DEFAULT 30,
    data_estudo DATE DEFAULT (CURRENT_DATE),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_study_user (user_id)
) ENGINE=InnoDB;

-- ==========================================================
-- DADOS INICIAIS (SEED) PARA TESTE DOS CONTROLLERS E API
-- ==========================================================

-- Usuário Principal
INSERT INTO users (id, nome, email, senha, nivel, xp_total) 
VALUES (1, 'Djalma Franco', 'dev@painel.local', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe9Y5d5TjZ3aRkLgVvH9CqN4yZpQzE2Ky', 1, 150)
ON DUPLICATE KEY UPDATE nome=VALUES(nome);

-- Transações Financeiras
INSERT INTO transactions (user_id, descricao, valor, tipo, categoria, data_transacao) VALUES
(1, 'Salário / Bolsa', 1800.00, 'receita', 'Trabalho', CURRENT_DATE),
(1, 'Mensalidade Academia', 110.00, 'despesa', 'Saúde', CURRENT_DATE),
(1, 'Supermercado & Dieta', 450.00, 'despesa', 'Alimentação', CURRENT_DATE),
(1, 'Assinatura Nuvem / Ferramentas', 45.00, 'despesa', 'Tecnologia', CURRENT_DATE);

-- Metas
INSERT INTO goals (user_id, titulo, descricao, status, prazo) VALUES
(1, 'Estudar 10h de Backend na semana', 'Foco em PHP, APIs REST e Banco de Dados', 'pendente', DATE_ADD(CURRENT_DATE, INTERVAL 7 DAY)),
(1, 'Completar 4 treinos de musculação', 'Manter rotina semanal consistente', 'pendente', DATE_ADD(CURRENT_DATE, INTERVAL 7 DAY)),
(1, 'Finalizar estrutura do SaaS', 'Concluir autenticação e endpoints REST', 'em_andamento', DATE_ADD(CURRENT_DATE, INTERVAL 15 DAY));

-- Hábitos
INSERT INTO habits (id, user_id, nome, frequencia, xp_recompensa, status) VALUES
(1, 1, 'Treinar Musculação', 'diario', 50, 'ativo'),
(2, 1, 'Estudo Técnico de Programação', 'diario', 30, 'ativo'),
(3, 1, 'Beber 3L de Água', 'diario', 10, 'ativo');

-- Registros de Treinos
INSERT INTO workouts (user_id, tipo, duracao_minutos, xp_ganho, data_treino) VALUES
(1, 'Musculação - Superiores', 60, 50, CURRENT_DATE),
(1, 'Musculação - Pernas', 55, 50, DATE_SUB(CURRENT_DATE, INTERVAL 2 DAY));

-- Registros de Estudos
INSERT INTO study_sessions (user_id, materia, duracao_minutos, xp_ganho, data_estudo) VALUES
(1, 'Arquitetura Backend & APIs PHP', 90, 45, CURRENT_DATE),
(1, 'Modelagem de Banco de Dados MySQL', 60, 30, DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY));