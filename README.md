# ⚡ Life-ERP | Painel Vida

> Um ecossistema completo de gestão pessoal, alta produtividade e evolução gamificada construído com arquitetura desacoplada (PHP REST API + Vanilla JS/Tailwind UI).

---

## 📌 Visão Geral

O **Life-ERP (Painel Vida)** centraliza os pilares essenciais da rotina diária em um único painel inteligente, combinando métricas de performance com elementos de **gamificação por XP e Níveis** para incentivar a consistência do usuário.

---

## 🚀 Funcionalidades Principais

* 🎮 **Sistema de Gamificação Dinâmico:**
  * Bonificação em tempo real de XP para cada treino, bloco de estudo, hábito concluído ou tarefa entregue.
  * Cálculo progressivo de níveis (100 XP por nível) com barra de progresso visual.
* 📊 **Dashboard Central:**
  * Resumo financeiro consolidado, totalizador de treinos semanais, horas de estudo e feed cronológico unificado de atividades.
  * Gráfico de rosca (*Doughnut*) interativo de despesas por categoria usando Chart.js.
* 💰 **Gestão Financeira:**
  * Controle de fluxo de caixa (receitas e despesas), saldo líquido e histórico de transações com métodos de pagamento.
* 🏋️ **Fitness & Performance:**
  * Registro de sessões de treino com multiplicador de XP baseado na intensidade (*Leve*, *Moderada*, *Intensa*).
  * Histórico dos últimos 7 dias e cálculo aproximado de gasto calórico.
* 📚 **Foco & Estudos:**
  * Lançamento de blocos de tempo com categorização por matéria e conteúdo estudado.
* 🎯 **Rastreador de Hábitos (Habit Tracker):**
  * Checklist diário de hábitos com contador acumulativo de consistência.
* 📋 **Quadro de Projetos & Tarefas (Kanban):**
  * Organização de demandas em colunas (*A Fazer*, *Em Progresso*, *Concluído*) com prazos e níveis de prioridade.
* 🔐 **Autenticação & Segurança:**
  * Sistema de login/cadastro com senhas criptografadas (`password_hash`) e isolamento multiusuário via sessões do cliente e headers HTTP (`X-User-Id`).

---

## 🛠️ Tecnologias Utilizadas

### **Frontend:**
* **HTML5 & CSS3** (Design Minimalista / Glassmorphism)
* **Tailwind CSS** (Utility-first framework via CDN)
* **JavaScript (ES6+)** (Arquitetura reativa nativa e Fetch API)
* **Lucide Icons** (Ícones modernos e leves)
* **Chart.js** (Renderização visual de gráficos e métricas)

### **Backend:**
* **PHP 8.x** (REST API estruturada com Composer Autoloader e PDO)
* **MySQL** (Modelagem relacional com integridade referencial `CASCADE`)

---

## 🗄️ Estrutura do Banco de Dados

O banco de dados `painel_vida` é composto pelas seguintes tabelas relacionais:

* `users` — Cadastro de contas, hash de senhas, nível e XP acumulado.
* `transactions` — Lançamentos de entradas e saídas financeiras.
* `workouts` — Registros de treinos, duração, intensidade e XP.
* `study_sessions` — Blocos de estudo e métricas de foco.
* `habits` — Definição dos hábitos cadastrados por usuário.
* `habit_logs` — Histórico diário de conclusão dos hábitos.
* `tasks` — Demandas do quadro Kanban com status e prioridades.

---

## ⚙️ Como Executar o Projeto Localmente

### **Pré-requisitos:**
* PHP 8.0+ instalado
* MySQL / MariaDB Server ativo
* Composer instalado

---

### **1. Clonar o Repositório**
```bash
git clone [https://github.com/douglasJVM/Life-ERP.git](https://github.com/douglasJVM/Life-ERP.git)
cd Life-ERP
