<?php
require_once __DIR__ . '/../../API/auth/routeAuthorization.php';
require_once __DIR__ . '/../../API/config/conecta.php';

// Verifica se o usuário está logado e tem permissão para acessar
// Apenas administradores podem acessar
$allowedRoles = ['administrador'];
requirePermission($allowedRoles, '../../pages/public/login.php');

// Obtém informações do usuário logado da sessão (armazenadas no login)
$user_id = $_SESSION['user_id'];
$user_data = [
    'nome_completo' => $_SESSION['nome_completo'] ?? 'Administrador',
    'email' => $_SESSION['email'] ?? '',
    'cargo' => $_SESSION['cargo'] ?? ''
];

// Validação adicional - garantir que os dados da sessão estão presentes
if (empty($user_data['nome_completo']) || empty($user_data['email'])) {
    // Se dados da sessão estão incompletos, redirecionar para login
    header('Location: ../../pages/public/login.php?error=' . urlencode('Sessão inválida. Faça login novamente.'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sthenos Gym - Painel do Administrador</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-900">

    <header class="bg-gradient-to-r from-red-800 to-red-900 text-white p-4 md:p-6 shadow-lg sticky top-0 z-50">
        <div class="container mx-auto flex items-center justify-between">
            <h1 class="text-2xl md:text-3xl font-bold flex items-center gap-2">
                <i class="fas fa-crown text-yellow-500"></i>Sthenos Gym - Admin
            </h1>
            <div class="flex items-center gap-4">
                <button id="mobile-nav-toggle" class="md:hidden p-2 rounded-md border border-white/20 hover:bg-white/10 transition" aria-label="Abrir menu">
                    <i class="fas fa-bars"></i>
                </button>
                <nav id="main-nav" class="hidden md:flex items-center space-x-6 text-sm">
                    <a href="#dashboard" class="hover:text-yellow-400 transition flex items-center gap-1">
                        <i class="fas fa-tachometer-alt"></i>Dashboard
                    </a>
                    <a href="#usuarios" class="hover:text-yellow-400 transition flex items-center gap-1">
                        <i class="fas fa-users"></i>Usuários
                    </a>
                    <a href="#treinos" class="hover:text-yellow-400 transition flex items-center gap-1">
                        <i class="fas fa-dumbbell"></i>Treinos
                    </a>
                    <div class="flex items-center gap-3 pl-4 border-l border-gray-700">
                        <span class="text-sm">
                            <i class="fas fa-user-shield text-yellow-500"></i>
                            <span id="username"><?php echo htmlspecialchars($user_data['nome_completo']); ?></span>
                        </span>
                        <a href="../../API/auth/logout.php" class="hover:text-red-400 transition">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </nav>
            </div>
        </div>
        <div id="mobile-nav" class="md:hidden hidden bg-red-900/95 border-t border-gray-700 mt-2">
            <div class="px-4 py-3 border-b border-gray-700 text-yellow-400 text-sm">
                <p class="font-semibold"><?php echo htmlspecialchars($user_data['nome_completo']); ?></p>
                <p class="text-xs text-gray-400"><?php echo htmlspecialchars($user_data['email']); ?></p>
            </div>
            <a href="#dashboard" class="block px-4 py-3 text-white hover:bg-red-800 border-b border-gray-700">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="#usuarios" class="block px-4 py-3 text-white hover:bg-red-800 border-b border-gray-700">
                <i class="fas fa-users"></i> Usuários
            </a>
            <a href="#treinos" class="block px-4 py-3 text-white hover:bg-red-800 border-b border-gray-700">
                <i class="fas fa-dumbbell"></i> Treinos
            </a>
            <a href="../../API/auth/logout.php" class="block px-4 py-3 text-red-400 hover:bg-red-800">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>
    </header>

    <!-- Notification -->
    <div id="notification" class="fixed top-4 right-4 p-4 rounded-lg text-white z-50 hidden">
    </div>

    <main class="container mx-auto px-4 md:px-6 py-8">

        <!-- Dashboard Section -->
        <section id="dashboard" class="mb-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8" data-aos="fade-up">
                <!-- Card: Total de Usuários -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-2xl p-6 shadow-lg transform hover:scale-105 transition duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold">Total Usuários</h3>
                        <i class="fas fa-users text-3xl opacity-50"></i>
                    </div>
                    <p class="text-4xl font-extrabold mb-2" id="total-usuarios">0</p>
                    <p class="text-sm text-blue-100">cadastrados no sistema</p>
                </div>

                <!-- Card: Alunos Ativos -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-2xl p-6 shadow-lg transform hover:scale-105 transition duration-300" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold">Alunos Ativos</h3>
                        <i class="fas fa-user-check text-3xl opacity-50"></i>
                    </div>
                    <p class="text-4xl font-extrabold mb-2" id="alunos-ativos">0</p>
                    <p class="text-sm text-green-100">com planos ativos</p>
                </div>

                <!-- Card: Check-ins Hoje -->
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-2xl p-6 shadow-lg transform hover:scale-105 transition duration-300" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold">Check-ins Hoje</h3>
                        <i class="fas fa-calendar-check text-3xl opacity-50"></i>
                    </div>
                    <p class="text-4xl font-extrabold mb-2" id="checkins-hoje">0</p>
                    <p class="text-sm text-purple-100">presenças registradas</p>
                </div>

                <!-- Card: Treinos Criados -->
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-2xl p-6 shadow-lg transform hover:scale-105 transition duration-300" data-aos="fade-up" data-aos-delay="300">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold">Treinos Criados</h3>
                        <i class="fas fa-plus-circle text-3xl opacity-50"></i>
                    </div>
                    <p class="text-4xl font-extrabold mb-2" id="treinos-criados">0</p>
                    <p class="text-sm text-orange-100">programas ativos</p>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-8" data-aos="fade-right">
                <div class="flex items-center gap-3 mb-6">
                    <i class="fas fa-bolt text-yellow-500 text-2xl"></i>
                    <h2 class="text-2xl font-bold text-gray-800">Ações Rápidas</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <button id="add-user-btn" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                        <i class="fas fa-user-plus"></i> Adicionar Usuário
                    </button>
                    <button id="create-workout-btn" class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                        <i class="fas fa-dumbbell"></i> Criar Treino
                    </button>
                </div>
            </div>
        </section>

        <!-- Usuários Section -->
        <section id="usuarios" class="mb-12">
            <div class="flex items-center gap-3 mb-6" data-aos="fade-left">
                <i class="fas fa-users text-yellow-500 text-2xl"></i>
                <h2 class="text-2xl font-bold text-gray-800">Gerenciamento de Usuários</h2>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-gray-600 mb-4">Aqui você pode gerenciar alunos, professores e colaboradores.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <button id="manage-students-btn" class="bg-blue-100 hover:bg-blue-200 text-blue-800 font-semibold py-4 px-6 rounded-lg transition flex items-center gap-3">
                        <i class="fas fa-user-graduate text-2xl"></i>
                        <div>
                            <h3 class="text-lg">Gerenciar Alunos</h3>
                            <p class="text-sm">Visualizar e editar perfis de alunos</p>
                        </div>
                    </button>
                    <button id="manage-professors-btn" class="bg-green-100 hover:bg-green-200 text-green-800 font-semibold py-4 px-6 rounded-lg transition flex items-center gap-3">
                        <i class="fas fa-chalkboard-teacher text-2xl"></i>
                        <div>
                            <h3 class="text-lg">Gerenciar Professores</h3>
                            <p class="text-sm">Administrar equipe de instrutores</p>
                        </div>
                    </button>
                </div>
            </div>
        </section>

        <!-- Treinos Section -->
        <section id="treinos" class="mb-12">
            <div class="flex items-center gap-3 mb-6" data-aos="fade-right">
                <i class="fas fa-dumbbell text-yellow-500 text-2xl"></i>
                <h2 class="text-2xl font-bold text-gray-800">Gerenciamento de Treinos</h2>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-gray-600 mb-4">Controle programas de treino e exercícios.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <button id="manage-workouts-btn" class="bg-purple-100 hover:bg-purple-200 text-purple-800 font-semibold py-4 px-6 rounded-lg transition flex items-center gap-3">
                        <i class="fas fa-list text-2xl"></i>
                        <div>
                            <h3 class="text-lg">Ver Treinos</h3>
                            <p class="text-sm">Listar e editar programas existentes</p>
                        </div>
                    </button>
                    <button id="create-new-workout-btn" class="bg-orange-100 hover:bg-orange-200 text-orange-800 font-semibold py-4 px-6 rounded-lg transition flex items-center gap-3">
                        <i class="fas fa-plus text-2xl"></i>
                        <div>
                            <h3 class="text-lg">Criar Novo Treino</h3>
                            <p class="text-sm">Desenvolver programas personalizados</p>
                        </div>
                    </button>
                </div>
            </div>
        </section>

        <!-- Relatórios Section -->
        <section id="relatorios" class="mb-12">
            <div class="flex items-center gap-3 mb-6" data-aos="fade-left">
                <i class="fas fa-chart-line text-yellow-500 text-2xl"></i>
                <h2 class="text-2xl font-bold text-gray-800">Relatórios</h2>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-gray-600 mb-4">Acompanhe métricas e performance do sistema.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <button id="frequency-report-btn" class="bg-blue-100 hover:bg-blue-200 text-blue-800 font-semibold py-4 px-6 rounded-lg transition flex items-center gap-3">
                        <i class="fas fa-calendar-check text-2xl"></i>
                        <div>
                            <h3 class="text-lg">Relatórios de Frequência</h3>
                            <p class="text-sm">Check-ins e assiduidade dos alunos</p>
                        </div>
                    </button>
                    <button id="performance-report-btn" class="bg-green-100 hover:bg-green-200 text-green-800 font-semibold py-4 px-6 rounded-lg transition flex items-center gap-3">
                        <i class="fas fa-chart-bar text-2xl"></i>
                        <div>
                            <h3 class="text-lg">Relatórios de Performance</h3>
                            <p class="text-sm">Evolução e estatísticas de treinos</p>
                        </div>
                    </button>
                </div>
            </div>
        </section>

        </main>

        <!-- Modal Adicionar Usuário -->
    <div id="add-user-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center overflow-y-auto">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Adicionar Usuário</h3>
                <button id="close-modal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="add-user-form">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="nome_completo">
                        Nome Completo
                    </label>
                    <input type="text" id="nome_completo" name="nome_completo" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                        Email
                    </label>
                    <input type="email" id="email" name="email" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="peso">
                            Peso (kg)
                        </label>
                        <input type="number" step="0.1" min="0" id="peso" name="peso"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="altura">
                            Altura (m)
                        </label>
                        <input type="number" step="0.01" min="0" id="altura" name="altura"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="cargo">
                        Cargo
                    </label>
                    <select id="cargo" name="cargo" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Selecione um cargo</option>
                        <option value="usuario_cadastrado">Usuário Cadastrado</option>
                        <option value="aluno_pagante">Aluno Pagante</option>
                        <option value="professor">Professor</option>
                        <option value="colaborador_baixo">Colaborador Baixo</option>
                        <option value="administrador">Administrador</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="senha">
                        Senha
                    </label>
                    <input type="password" id="senha" name="senha" required minlength="6"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="confirmar_senha">
                        Confirmar Senha
                    </label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="flex items-center justify-between">
                    <button type="button" id="cancel-add-user" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Adicionar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Alerta Peso/Altura -->
    <div id="warning-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-2xl p-6 max-w-sm w-full mx-4">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Campos opcionais não preenchidos</h3>
            <p class="text-gray-700 mb-6">Peso e/ou altura não foram informados. Deseja continuar mesmo assim?</p>
            <div class="flex justify-end gap-3">
                <button id="warning-cancel" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md">Cancelar</button>
                <button id="warning-confirm" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">Confirmar</button>
            </div>
        </div>
    </div>

    <!-- Modal Criar Treino -->
    <div id="create-workout-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center overflow-y-auto">
        <div class="bg-white rounded-2xl p-8 max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Criar Treino</h3>
                <button id="close-create-workout" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="create-workout-form">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="workout-aluno">Aluno</label>
                    <select id="workout-aluno" name="aluno" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Carregando alunos...</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="workout-exercicio">Exercício</label>
                    <select id="workout-exercicio" name="exercicio" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Carregando exercícios...</option>
                    </select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1" for="workout-series">Séries</label>
                        <input type="number" id="workout-series" name="series" min="1" value="3" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1" for="workout-reps">Repetições</label>
                        <input type="number" id="workout-reps" name="repeticoes" min="1" value="10" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1" for="workout-carga">Carga</label>
                        <input type="number" step="0.5" id="workout-carga" name="carga" min="0" value="0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="workout-date">Data</label>
                    <input type="date" id="workout-date" name="date" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="workout-time">Horário</label>
                    <input type="time" id="workout-time" name="time" value="08:00" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline">
                </div>
                <div class="flex items-center gap-2 mb-4">
                    <input type="checkbox" id="workout-recurring" name="recurring" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="workout-recurring" class="text-gray-700 text-sm">Repetir treino</label>
                </div>
                <div id="recurring-options" class="hidden mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Dias de semana</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="recurring_days" value="1" class="h-4 w-4 text-blue-600"><span>Seg</span></label>
                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="recurring_days" value="2" class="h-4 w-4 text-blue-600"><span>Ter</span></label>
                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="recurring_days" value="3" class="h-4 w-4 text-blue-600"><span>Qua</span></label>
                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="recurring_days" value="4" class="h-4 w-4 text-blue-600"><span>Qui</span></label>
                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="recurring_days" value="5" class="h-4 w-4 text-blue-600"><span>Sex</span></label>
                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="recurring_days" value="6" class="h-4 w-4 text-blue-600"><span>Sáb</span></label>
                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="recurring_days" value="0" class="h-4 w-4 text-blue-600"><span>Dom</span></label>
                    </div>
                    <div class="mt-4">
                        <label class="block text-gray-700 text-sm font-bold mb-1" for="workout-end-date">Data final</label>
                        <input type="date" id="workout-end-date" name="end_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3">
                    <button id="cancel-create-workout" type="button" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md">Salvar Treino</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Relatórios -->
    <!-- Modal Gerenciar Alunos -->
    <div id="manage-students-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center overflow-y-auto">
        <div class="bg-white rounded-2xl p-8 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Gerenciar Alunos</h3>
                <button id="close-manage-students" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="students-list" class="space-y-4">
                <!-- Students will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Modal Gerenciar Professores -->
    <div id="manage-professors-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center overflow-y-auto">
        <div class="bg-white rounded-2xl p-8 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Gerenciar Professores</h3>
                <button id="close-manage-professors" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="professors-list" class="space-y-4">
                <!-- Professors will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Modal Editar Usuário -->
    <div id="edit-user-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center overflow-y-auto">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Editar Usuário</h3>
                <button id="close-edit-user" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="edit-user-form">
                <input type="hidden" id="edit-user-id" name="user_id">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit-nome_completo">
                        Nome Completo
                    </label>
                    <input type="text" id="edit-nome_completo" name="nome_completo" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit-email">
                        Email
                    </label>
                    <input type="email" id="edit-email" name="email" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit-peso">
                            Peso (kg)
                        </label>
                        <input type="number" step="0.1" min="0" id="edit-peso" name="peso"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit-altura">
                            Altura (m)
                        </label>
                        <input type="number" step="0.01" min="0" id="edit-altura" name="altura"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit-cargo">
                        Cargo
                    </label>
                    <select id="edit-cargo" name="cargo" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="usuario_cadastrado">Usuário Cadastrado</option>
                        <option value="aluno_pagante">Aluno Pagante</option>
                        <option value="professor">Professor</option>
                        <option value="colaborador_baixo">Colaborador Baixo</option>
                        <option value="administrador">Administrador</option>
                    </select>
                </div>
                <div class="flex items-center justify-between">
                    <button type="button" id="cancel-edit-user" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Gerenciar Treinos -->
    <div id="manage-workouts-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center overflow-y-auto">
        <div class="bg-white rounded-2xl p-8 max-w-6xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Gerenciar Treinos</h3>
                <button id="close-manage-workouts" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="workouts-list" class="space-y-4">
                <!-- Workouts will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Modal Detalhes do Treino -->
    <div id="workout-details-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center overflow-y-auto">
        <div class="bg-white rounded-2xl p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Detalhes do Treino</h3>
                <button id="close-workout-details" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="workout-details-content">
                <!-- Details will be loaded here -->
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button id="delete-workout-btn" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md">
                    <i class="fas fa-trash"></i> Deletar Treino
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Relatório de Frequência -->
    <div id="frequency-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center overflow-y-auto">
        <div class="bg-white rounded-2xl p-8 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Relatório de Frequência</h3>
                <button id="close-frequency-modal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm mb-2">Período (dias)</label>
                <select id="frequency-period" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline">
                    <option value="7">Últimos 7 dias</option>
                    <option value="30" selected>Últimos 30 dias</option>
                    <option value="90">Últimos 90 dias</option>
                </select>
            </div>
            <div id="frequency-content">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-red-50 border rounded-lg p-4">
                        <h4 class="font-semibold text-red-700">Total Check-ins</h4>
                        <p id="freq-total-checkins" class="text-3xl font-bold text-red-600">...</p>
                    </div>
                    <div class="bg-red-50 border rounded-lg p-4">
                        <h4 class="font-semibold text-red-700">Média Diária</h4>
                        <p id="freq-media-diaria" class="text-3xl font-bold text-red-600">...</p>
                    </div>
                    <div class="bg-red-50 border rounded-lg p-4">
                        <h4 class="font-semibold text-red-700">Período</h4>
                        <p id="freq-periodo" class="text-3xl font-bold text-red-600">...</p>
                    </div>
                </div>
                <div class="bg-white border rounded-lg p-4">
                    <canvas id="frequency-chart" class="w-full"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Relatório de Performance -->
    <div id="performance-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center overflow-y-auto">
        <div class="bg-white rounded-2xl p-8 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Relatório de Performance</h3>
                <button id="close-performance-modal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm mb-2">Período (dias)</label>
                <select id="performance-period" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline">
                    <option value="7">Últimos 7 dias</option>
                    <option value="30" selected>Últimos 30 dias</option>
                    <option value="90">Últimos 90 dias</option>
                </select>
            </div>
            <div id="performance-content">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-indigo-50 border rounded-lg p-4">
                        <h4 class="font-semibold text-indigo-700">Total Treinos</h4>
                        <p id="perf-total-treinos" class="text-3xl font-bold text-indigo-600">...</p>
                    </div>
                    <div class="bg-indigo-50 border rounded-lg p-4">
                        <h4 class="font-semibold text-indigo-700">Média Diária</h4>
                        <p id="perf-media-diaria" class="text-3xl font-bold text-indigo-600">...</p>
                    </div>
                    <div class="bg-indigo-50 border rounded-lg p-4">
                        <h4 class="font-semibold text-indigo-700">Usuários Ativos</h4>
                        <p id="perf-usuarios-ativos" class="text-3xl font-bold text-indigo-600">...</p>
                    </div>
                    <div class="bg-indigo-50 border rounded-lg p-4">
                        <h4 class="font-semibold text-indigo-700">Média/Usuário</h4>
                        <p id="perf-media-usuario" class="text-3xl font-bold text-indigo-600">...</p>
                    </div>
                </div>
                <div class="bg-white border rounded-lg p-4">
                    <canvas id="performance-chart" class="w-full"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();

        // Mobile navigation toggle
        document.getElementById('mobile-nav-toggle').addEventListener('click', function() {
            document.getElementById('mobile-nav').classList.toggle('hidden');
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Function to show notifications
        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `fixed top-4 right-4 p-4 rounded-lg text-white z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
            notification.classList.remove('hidden');
            setTimeout(() => {
                notification.classList.add('hidden');
            }, 5000);
        }

        // Load dashboard stats from API
        document.addEventListener('DOMContentLoaded', function() {
            fetch('../../API/admin/get_stats.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('total-usuarios').textContent = data.stats.total_users;
                        document.getElementById('alunos-ativos').textContent = data.stats.active_students;
                        document.getElementById('checkins-hoje').textContent = data.stats.checkins_today;
                        document.getElementById('treinos-criados').textContent = data.stats.created_workouts;
                    } else {
                        console.error('Erro ao carregar estatísticas:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro na requisição:', error);
                });

            // Modal funcionalidade adicionar usuário
            const modal = document.getElementById('add-user-modal');
            const warningModal = document.getElementById('warning-modal');
            const warningConfirm = document.getElementById('warning-confirm');
            const warningCancel = document.getElementById('warning-cancel');
            const addUserBtn = document.getElementById('add-user-btn');
            const closeModal = document.getElementById('close-modal');
            const cancelBtn = document.getElementById('cancel-add-user');
            const form = document.getElementById('add-user-form');

            // Modal criar treino
            const createWorkoutBtn = document.getElementById('create-workout-btn');
            const createNewWorkoutBtn = document.getElementById('create-new-workout-btn');
            const createWorkoutModal = document.getElementById('create-workout-modal');
            const closeCreateWorkoutBtn = document.getElementById('close-create-workout');
            const cancelCreateWorkoutBtn = document.getElementById('cancel-create-workout');
            const createWorkoutForm = document.getElementById('create-workout-form');
            const recurringCheckbox = document.getElementById('workout-recurring');
            const recurringOptions = document.getElementById('recurring-options');

            function toggleRecurringOptions() {
                recurringOptions.classList.toggle('hidden', !recurringCheckbox.checked);
            }

            recurringCheckbox.addEventListener('change', toggleRecurringOptions);

            function loadSelectData() {
                fetch('../../API/aluno/get_alunos.php')
                    .then(res => res.json())
                    .then(data => {
                        const alunoSelect = document.getElementById('workout-aluno');
                        if (!data.success) {
                            alunoSelect.innerHTML = '<option value="">Erro ao carregar alunos</option>';
                            return;
                        }
                        alunoSelect.innerHTML = '<option value="">Selecione um aluno</option>' + data.data.map(aluno => `<option value="${aluno.id}">${aluno.nome_completo}</option>`).join('');
                    })
                    .catch(() => {
                        document.getElementById('workout-aluno').innerHTML = '<option value="">Erro ao carregar alunos</option>';
                    });

                fetch('../../API/treino/get_exercicios.php')
                    .then(res => res.json())
                    .then(data => {
                        const exercicioSelect = document.getElementById('workout-exercicio');
                        if (!data.success) {
                            exercicioSelect.innerHTML = '<option value="">Erro ao carregar exercícios</option>';
                            return;
                        }
                        exercicioSelect.innerHTML = '<option value="">Selecione um exercício</option>' + data.data.map(exercicio => `<option value="${exercicio.id}">${exercicio.nome}</option>`).join('');
                    })
                    .catch(() => {
                        document.getElementById('workout-exercicio').innerHTML = '<option value="">Erro ao carregar exercícios</option>';
                    });
            }

            // Funções de bloqueio de rolagem de fundo
            function openModal(modalElement) {
                modalElement.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeModalElement(modalElement) {
                modalElement.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            // Show modal adicionar usuário
            addUserBtn.addEventListener('click', () => {
                openModal(modal);
            });

            // Show criar treino modal
            createWorkoutBtn.addEventListener('click', () => {
                openModal(createWorkoutModal);
                loadSelectData();
            });

            createNewWorkoutBtn.addEventListener('click', () => {
                openModal(createWorkoutModal);
                loadSelectData();
            });


            // Hide modal
            closeModal.addEventListener('click', () => {
                closeModalElement(modal);
                form.reset();
            });

            cancelBtn.addEventListener('click', () => {
                closeModalElement(modal);
                form.reset();
            });

            // Close modal when clicking outside
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeModalElement(modal);
                    form.reset();
                }
            });

            // Criar treino modal hide/close
            closeCreateWorkoutBtn.addEventListener('click', () => {
                closeModalElement(createWorkoutModal);
                createWorkoutForm.reset();
                recurringOptions.classList.add('hidden');
                recurringCheckbox.checked = false;
            });

            cancelCreateWorkoutBtn.addEventListener('click', () => {
                closeModalElement(createWorkoutModal);
                createWorkoutForm.reset();
                recurringOptions.classList.add('hidden');
                recurringCheckbox.checked = false;
            });

            createWorkoutModal.addEventListener('click', (e) => {
                if (e.target === createWorkoutModal) {
                    closeModalElement(createWorkoutModal);
                    createWorkoutForm.reset();
                    recurringOptions.classList.add('hidden');
                    recurringCheckbox.checked = false;
                }
            });

            warningCancel.addEventListener('click', () => {
                warningModal.classList.add('hidden');
            });

            warningModal.addEventListener('click', (e) => {
                if (e.target === warningModal) {
                    warningModal.classList.add('hidden');
                }
            });

            function submitAddUser() {
                const formData = new FormData(form);

                fetch('../../API/admin/add_user.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Usuário adicionado com sucesso!');
                        warningModal.classList.add('hidden');
                        modal.classList.add('hidden');
                        form.reset();
                        location.reload();
                    } else {
                        if (data.errors) {
                            showNotification('Erros: ' + data.errors.join(' | '), 'error');
                        } else {
                            showNotification('Erro: ' + data.message, 'error');
                        }
                    }
                })
                .catch(error => {
                    console.error('Erro na requisição:', error);
                    showNotification('Erro ao adicionar usuário. Tente novamente.', 'error');
                });
            }

            warningConfirm.addEventListener('click', () => {
                warningModal.classList.add('hidden');
                submitAddUser();
            });

            // Form submission adicionar usuário
            form.addEventListener('submit', (e) => {
                e.preventDefault();

                const peso = document.getElementById('peso').value.trim();
                const altura = document.getElementById('altura').value.trim();

                if (peso === '' || altura === '') {
                    warningModal.classList.remove('hidden');
                    return;
                }

                submitAddUser();
            });

            // Criar treino
            function submitCreateWorkout() {
                const alunoId = document.getElementById('workout-aluno').value;
                const exercicioId = document.getElementById('workout-exercicio').value;
                const series = parseInt(document.getElementById('workout-series').value, 10);
                const repeticoes = parseInt(document.getElementById('workout-reps').value, 10);
                const carga = parseFloat(document.getElementById('workout-carga').value) || 0;
                const date = document.getElementById('workout-date').value;
                const time = document.getElementById('workout-time').value;
                const isRecurring = recurringCheckbox.checked;
                const endDate = document.getElementById('workout-end-date').value;

                const selectedDays = Array.from(document.querySelectorAll('input[name="recurring_days"]:checked')).map(el => parseInt(el.value, 10));

                if (!alunoId || !exercicioId || !series || !repeticoes || !date || !time) {
                    showNotification('Preencha todos os campos obrigatórios de criação de treino.', 'error');
                    return;
                }

                const payload = {
                    alunos: [parseInt(alunoId, 10)],
                    exercicios: [{
                        exercicio_id: parseInt(exercicioId, 10),
                        series,
                        repeticoes,
                        carga
                    }],
                    horario: time,
                    is_recurring: isRecurring,
                    recurring_days: isRecurring ? selectedDays : [],
                    end_date: isRecurring ? endDate : date
                };

                if (isRecurring && selectedDays.length === 0) {
                    showNotification('Selecione ao menos um dia para treinos recorrentes.', 'error');
                    return;
                }

                if (isRecurring && !endDate) {
                    showNotification('Informe a data final para recorrência.', 'error');
                    return;
                }

                fetch('../../API/treino/post_create_treino.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Treino criado com sucesso!');
                        createWorkoutModal.classList.add('hidden');
                        createWorkoutForm.reset();
                        recurringOptions.classList.add('hidden');
                        recurringCheckbox.checked = false;
                        location.reload();
                    } else {
                        showNotification('Erro ao criar treino: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Erro ao criar treino:', error);
                    showNotification('Erro ao criar treino. Tente novamente.', 'error');
                });
            }

            createWorkoutForm.addEventListener('submit', (e) => {
                e.preventDefault();
                submitCreateWorkout();
            });

            // Gerenciar alunos e professores
            const manageStudentsBtn = document.getElementById('manage-students-btn');
            const manageProfessorsBtn = document.getElementById('manage-professors-btn');
            const manageStudentsModal = document.getElementById('manage-students-modal');
            const manageProfessorsModal = document.getElementById('manage-professors-modal');
            const closeManageStudents = document.getElementById('close-manage-students');
            const closeManageProfessors = document.getElementById('close-manage-professors');
            const editUserModal = document.getElementById('edit-user-modal');
            const closeEditUser = document.getElementById('close-edit-user');
            const cancelEditUser = document.getElementById('cancel-edit-user');
            const editUserForm = document.getElementById('edit-user-form');

            function loadUsers(role, containerId) {
                fetch(`../../API/admin/get_users.php?role=${role}`)
                    .then(res => res.json())
                    .then(data => {
                        const container = document.getElementById(containerId);
                        if (!data.success) {
                            container.innerHTML = '<p class="text-red-500">Erro ao carregar usuários.</p>';
                            return;
                        }
                        if (data.data.length === 0) {
                            container.innerHTML = '<p class="text-gray-500">Nenhum usuário encontrado.</p>';
                            return;
                        }
                        const html = data.data.map(user => `
                            <div class="bg-gray-50 border rounded-lg p-4 flex items-center justify-between">
                                <div>
                                    <h4 class="font-semibold text-gray-800">${user.nome_completo}</h4>
                                    <p class="text-sm text-gray-600">${user.email}</p>
                                    <p class="text-sm text-gray-500">Peso: ${user.peso || 'N/A'} kg | Altura: ${user.altura || 'N/A'} m</p>
                                </div>
                                <div class="flex gap-2">
                                    <button onclick="editUser(${user.id}, '${user.nome_completo}', '${user.email}', '${user.cargo}', '${user.peso || ''}', '${user.altura || ''}')" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button onclick="deactivateUser(${user.id}, '${user.nome_completo}')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                        <i class="fas fa-trash"></i> Desativar
                                    </button>
                                </div>
                            </div>
                        `).join('');
                        container.innerHTML = html;
                    })
                    .catch(() => {
                        document.getElementById(containerId).innerHTML = '<p class="text-red-500">Erro ao carregar usuários.</p>';
                    });
            }

            window.editUser = function(id, nome, email, cargo, peso, altura) {
                document.getElementById('edit-user-id').value = id;
                document.getElementById('edit-nome_completo').value = nome;
                document.getElementById('edit-email').value = email;
                document.getElementById('edit-cargo').value = cargo;
                document.getElementById('edit-peso').value = peso;
                document.getElementById('edit-altura').value = altura;
                openModal(editUserModal);
            };

            window.deactivateUser = function(id, nome) {
                if (confirm(`Tem certeza que deseja desativar o usuário ${nome}?`)) {
                    const formData = new FormData();
                    formData.append('user_id', id);
                    fetch('../../API/admin/deactivate_user.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showNotification('Usuário desativado com sucesso!');
                            // Reload the lists
                            if (manageStudentsModal.classList.contains('hidden') === false) {
                                loadUsers('aluno_pagante', 'students-list');
                            }
                            if (manageProfessorsModal.classList.contains('hidden') === false) {
                                loadUsers('professor', 'professors-list');
                            }
                        } else {
                            showNotification('Erro: ' + data.message, 'error');
                        }
                    })
                    .catch(() => {
                        showNotification('Erro ao desativar usuário.', 'error');
                    });
                }
            };

            manageStudentsBtn.addEventListener('click', () => {
                openModal(manageStudentsModal);
                loadUsers('aluno_pagante', 'students-list');
            });

            manageProfessorsBtn.addEventListener('click', () => {
                openModal(manageProfessorsModal);
                loadUsers('professor', 'professors-list');
            });

            closeManageStudents.addEventListener('click', () => {
                closeModalElement(manageStudentsModal);
            });

            manageStudentsModal.addEventListener('click', (e) => {
                if (e.target === manageStudentsModal) {
                    closeModalElement(manageStudentsModal);
                }
            });

            closeManageProfessors.addEventListener('click', () => {
                closeModalElement(manageProfessorsModal);
            });

            manageProfessorsModal.addEventListener('click', (e) => {
                if (e.target === manageProfessorsModal) {
                    closeModalElement(manageProfessorsModal);
                }
            });

            closeEditUser.addEventListener('click', () => {
                closeModalElement(editUserModal);
                editUserForm.reset();
            });

            cancelEditUser.addEventListener('click', () => {
                closeModalElement(editUserModal);
                editUserForm.reset();
            });

            editUserModal.addEventListener('click', (e) => {
                if (e.target === editUserModal) {
                    closeModalElement(editUserModal);
                    editUserForm.reset();
                }
            });

            editUserForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(editUserForm);
                fetch('../../API/admin/update_user.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Usuário atualizado com sucesso!');
                        closeModalElement(editUserModal);
                        editUserForm.reset();
                        // Reload the lists
                        if (manageStudentsModal.classList.contains('hidden') === false) {
                            loadUsers('aluno_pagante', 'students-list');
                        }
                        if (manageProfessorsModal.classList.contains('hidden') === false) {
                            loadUsers('professor', 'professors-list');
                        }
                    } else {
                        if (data.errors) {
                            showNotification('Erros: ' + data.errors.join(' | '), 'error');
                        } else {
                            showNotification('Erro: ' + data.message, 'error');
                        }
                    }
                })
                .catch(() => {
                    showNotification('Erro ao atualizar usuário.', 'error');
                });
            });

            // Gerenciar treinos
            const manageWorkoutsBtn = document.getElementById('manage-workouts-btn');
            const manageWorkoutsModal = document.getElementById('manage-workouts-modal');
            const closeManageWorkouts = document.getElementById('close-manage-workouts');
            const workoutDetailsModal = document.getElementById('workout-details-modal');
            const closeWorkoutDetails = document.getElementById('close-workout-details');
            const deleteWorkoutBtn = document.getElementById('delete-workout-btn');

            function loadWorkouts() {
                fetch('../../API/admin/get_treinos.php')
                    .then(res => res.json())
                    .then(data => {
                        const container = document.getElementById('workouts-list');
                        if (!data.success) {
                            container.innerHTML = '<p class="text-red-500">Erro ao carregar treinos.</p>';
                            return;
                        }
                        if (data.data.length === 0) {
                            container.innerHTML = '<p class="text-gray-500">Nenhum treino encontrado.</p>';
                            return;
                        }
                        const html = data.data.map(treino => `
                            <div class="bg-gray-50 border rounded-lg p-4 flex items-center justify-between">
                                <div>
                                    <h4 class="font-semibold text-gray-800">${treino.aluno_nome}</h4>
                                    <p class="text-sm text-gray-600">Personal: ${treino.personal_nome}</p>
                                    <p class="text-sm text-gray-500">Data: ${new Date(treino.data_treino).toLocaleDateString('pt-BR')} | Status: ${treino.status}</p>
                                </div>
                                <div class="flex gap-2">
                                    <button onclick="viewWorkoutDetails(${treino.id})" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                        <i class="fas fa-eye"></i> Detalhes
                                    </button>
                                    <button onclick="deleteWorkout(${treino.id})" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                        <i class="fas fa-trash"></i> Deletar
                                    </button>
                                </div>
                            </div>
                        `).join('');
                        container.innerHTML = html;
                    })
                    .catch(() => {
                        document.getElementById('workouts-list').innerHTML = '<p class="text-red-500">Erro ao carregar treinos.</p>';
                    });
            }

            window.viewWorkoutDetails = function(id) {
                fetch(`../../API/admin/get_treino_detalhes.php?treino_id=${id}`)
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) {
                            showNotification('Erro ao carregar detalhes do treino.', 'error');
                            return;
                        }
                        const treino = data.data.treino;
                        const exercicios = data.data.exercicios;
                        const content = document.getElementById('workout-details-content');
                        content.innerHTML = `
                            <div class="mb-4">
                                <h4 class="font-semibold">Informações do Treino</h4>
                                <p><strong>Aluno:</strong> ${treino.aluno_nome}</p>
                                <p><strong>Personal:</strong> ${treino.personal_nome}</p>
                                <p><strong>Data:</strong> ${new Date(treino.data_treino).toLocaleDateString('pt-BR')}</p>
                                <p><strong>Status:</strong> ${treino.status}</p>
                            </div>
                            <div>
                                <h4 class="font-semibold mb-2">Exercícios</h4>
                                ${exercicios.length > 0 ? exercicios.map(ex => `
                                    <div class="bg-gray-100 p-2 rounded mb-2">
                                        <p><strong>${ex.exercicio_nome}</strong></p>
                                        <p>Séries: ${ex.series} | Repetições: ${ex.repeticoes} | Carga: ${ex.carga}kg</p>
                                    </div>
                                `).join('') : '<p>Nenhum exercício encontrado.</p>'}
                            </div>
                        `;
                        deleteWorkoutBtn.setAttribute('data-treino-id', id);
                        openModal(workoutDetailsModal);
                    })
                    .catch(() => {
                        showNotification('Erro ao carregar detalhes do treino.', 'error');
                    });
            };

            window.deleteWorkout = function(id) {
                if (confirm('Tem certeza que deseja deletar este treino?')) {
                    const formData = new FormData();
                    formData.append('treino_id', id);
                    fetch('../../API/admin/delete_treino.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showNotification('Treino deletado com sucesso!');
                            loadWorkouts();
                        } else {
                            showNotification('Erro: ' + data.message, 'error');
                        }
                    })
                    .catch(() => {
                        showNotification('Erro ao deletar treino.', 'error');
                    });
                }
            };

            manageWorkoutsBtn.addEventListener('click', () => {
                openModal(manageWorkoutsModal);
                loadWorkouts();
            });

            closeManageWorkouts.addEventListener('click', () => {
                closeModalElement(manageWorkoutsModal);
            });

            manageWorkoutsModal.addEventListener('click', (e) => {
                if (e.target === manageWorkoutsModal) {
                    closeModalElement(manageWorkoutsModal);
                }
            });

            closeWorkoutDetails.addEventListener('click', () => {
                closeModalElement(workoutDetailsModal);
            });

            workoutDetailsModal.addEventListener('click', (e) => {
                if (e.target === workoutDetailsModal) {
                    closeModalElement(workoutDetailsModal);
                }
            });

            deleteWorkoutBtn.addEventListener('click', () => {
                const id = deleteWorkoutBtn.getAttribute('data-treino-id');
                if (id) {
                    deleteWorkout(id);
                    closeModalElement(workoutDetailsModal);
                }
            });

            // Relatórios de frequência e performance
            const frequencyReportBtn = document.getElementById('frequency-report-btn');
            const performanceReportBtn = document.getElementById('performance-report-btn');
            const frequencyModal = document.getElementById('frequency-modal');
            const performanceModal = document.getElementById('performance-modal');
            const closeFrequencyModal = document.getElementById('close-frequency-modal');
            const closePerformanceModal = document.getElementById('close-performance-modal');
            const frequencyPeriod = document.getElementById('frequency-period');
            const performancePeriod = document.getElementById('performance-period');

            let frequencyChart = null;
            let performanceChart = null;

            function loadFrequencyReport(periodo = 30) {
                fetch(`../../API/admin/get_frequencia.php?periodo=${periodo}`)
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) {
                            showNotification('Erro ao carregar relatório de frequência.', 'error');
                            return;
                        }
                        const stats = data.data;
                        document.getElementById('freq-total-checkins').textContent = stats.total_checkins;
                        document.getElementById('freq-media-diaria').textContent = stats.media_diaria;
                        document.getElementById('freq-periodo').textContent = stats.periodo_dias + ' dias';

                        // Gráfico
                        const ctx = document.getElementById('frequency-chart').getContext('2d');
                        if (frequencyChart) {
                            frequencyChart.destroy();
                        }
                        frequencyChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: stats.checkins_por_dia.map(d => new Date(d.data_dia).toLocaleDateString('pt-BR')),
                                datasets: [{
                                    label: 'Check-ins por dia',
                                    data: stats.checkins_por_dia.map(d => d.total_checkins),
                                    borderColor: '#DC2626',
                                    backgroundColor: 'rgba(220, 38, 38, 0.1)',
                                    tension: 0.1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    })
                    .catch(() => {
                        showNotification('Erro ao carregar relatório de frequência.', 'error');
                    });
            }

            function loadPerformanceReport(periodo = 30) {
                fetch(`../../API/admin/get_performance.php?periodo=${periodo}`)
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) {
                            showNotification('Erro ao carregar relatório de performance.', 'error');
                            return;
                        }
                        const stats = data.data;
                        document.getElementById('perf-total-treinos').textContent = stats.total_treinos;
                        document.getElementById('perf-media-diaria').textContent = stats.media_treinos_diaria;
                        document.getElementById('perf-usuarios-ativos').textContent = stats.usuarios_ativos;
                        document.getElementById('perf-media-usuario').textContent = stats.media_por_usuario;

                        // Gráfico
                        const ctx = document.getElementById('performance-chart').getContext('2d');
                        if (performanceChart) {
                            performanceChart.destroy();
                        }
                        performanceChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: stats.treinos_por_dia.map(d => new Date(d.data_dia).toLocaleDateString('pt-BR')),
                                datasets: [{
                                    label: 'Treinos realizados por dia',
                                    data: stats.treinos_por_dia.map(d => d.total_treinos),
                                    backgroundColor: '#4F46E5',
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    })
                    .catch(() => {
                        showNotification('Erro ao carregar relatório de performance.', 'error');
                    });
            }

            frequencyReportBtn.addEventListener('click', () => {
                openModal(frequencyModal);
                loadFrequencyReport();
            });

            performanceReportBtn.addEventListener('click', () => {
                openModal(performanceModal);
                loadPerformanceReport();
            });

            closeFrequencyModal.addEventListener('click', () => {
                closeModalElement(frequencyModal);
            });

            frequencyModal.addEventListener('click', (e) => {
                if (e.target === frequencyModal) {
                    closeModalElement(frequencyModal);
                }
            });

            closePerformanceModal.addEventListener('click', () => {
                closeModalElement(performanceModal);
            });

            performanceModal.addEventListener('click', (e) => {
                if (e.target === performanceModal) {
                    closeModalElement(performanceModal);
                }
            });

            frequencyPeriod.addEventListener('change', () => {
                loadFrequencyReport(frequencyPeriod.value);
            });

            performancePeriod.addEventListener('change', () => {
                loadPerformanceReport(performancePeriod.value);
            });
        });
    </script>

</body>
</html>