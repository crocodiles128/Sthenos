<?php
session_start();
require_once __DIR__ . '/../../API/auth/routeAuthorization.php';

// Verifica se o usuário está logado e tem permissão para acessar
// Apenas usuários com estes cargos podem acessar:
$allowedRoles = ['usuario_cadastrado', 'aluno_pagante', 'professor', 'colaborador_baixo', 'administrador'];
requirePermission($allowedRoles, '../../pages/public/login.php');

// Obtém informações do usuário logado da sessão (armazenadas no login)
$user_id = $_SESSION['user_id'];
$user_data = [
    'nome_completo' => $_SESSION['nome_completo'] ?? 'Usuário',
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
    <title>Sthenos Gym - Painel do Usuário</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-900">

    <header class="bg-gradient-to-r from-gray-800 to-gray-900 text-white p-4 md:p-6 shadow-lg sticky top-0 z-50">
        <div class="container mx-auto flex items-center justify-between">
            <h1 class="text-2xl md:text-3xl font-bold flex items-center gap-2">
                <i class="fas fa-dumbbell text-yellow-500"></i>Sthenos Gym
            </h1>
            <div class="flex items-center gap-4">
                <button id="mobile-nav-toggle" class="md:hidden p-2 rounded-md border border-white/20 hover:bg-white/10 transition" aria-label="Abrir menu">
                    <i class="fas fa-bars"></i>
                </button>
                <nav id="main-nav" class="hidden md:flex items-center space-x-6 text-sm">
                    <a href="#dashboard" class="hover:text-yellow-400 transition flex items-center gap-1">
                        <i class="fas fa-home"></i>Dashboard
                    </a>
                    <a href="#workouts" class="hover:text-yellow-400 transition flex items-center gap-1">
                        <i class="fas fa-list"></i>Meus Treinos
                    </a>
                    <a href="#stats" class="hover:text-yellow-400 transition flex items-center gap-1">
                        <i class="fas fa-chart-bar"></i>Estatísticas
                    </a>
                    <div class="flex items-center gap-3 pl-4 border-l border-gray-700">
                        <span class="text-sm">
                            <i class="fas fa-user-circle text-yellow-500"></i>
                            <span id="username"><?php echo htmlspecialchars($user_data['nome_completo']); ?></span>
                        </span>
                        <a href="../../API/auth/logout.php" class="hover:text-red-400 transition">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </nav>
            </div>
        </div>
        <div id="mobile-nav" class="md:hidden hidden bg-gray-900/95 border-t border-gray-700 mt-2">
            <div class="px-4 py-3 border-b border-gray-700 text-yellow-400 text-sm">
                <p class="font-semibold"><?php echo htmlspecialchars($user_data['nome_completo']); ?></p>
                <p class="text-xs text-gray-400"><?php echo htmlspecialchars($user_data['email']); ?></p>
            </div>
            <a href="#dashboard" class="block px-4 py-3 text-white hover:bg-gray-800 border-b border-gray-700">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="#workouts" class="block px-4 py-3 text-white hover:bg-gray-800 border-b border-gray-700">
                <i class="fas fa-list"></i> Meus Treinos
            </a>
            <a href="#stats" class="block px-4 py-3 text-white hover:bg-gray-800 border-b border-gray-700">
                <i class="fas fa-chart-bar"></i> Estatísticas
            </a>
            <a href="../../API/auth/logout.php" class="block px-4 py-3 text-red-400 hover:bg-gray-800">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>
    </header>

    <main class="container mx-auto px-4 md:px-6 py-8">

        <!-- Dashboard Section -->
        <section id="dashboard" class="mb-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8" data-aos="fade-up">
                <!-- Card: Check-in do Dia -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-2xl p-6 shadow-lg transform hover:scale-105 transition duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold">Treino de Hoje</h3>
                        <i class="fas fa-calendar-check text-3xl opacity-50"></i>
                    </div>
                    <p class="text-sm text-blue-100 mb-4">Faça check-in para registrar sua presença</p>
                    <button id="checkin-btn" class="w-full bg-white text-blue-600 font-bold py-2 px-4 rounded-full hover:bg-blue-50 transition flex items-center justify-center gap-2">
                        <i class="fas fa-check-circle"></i> Check-in Agora
                    </button>
                    <p id="checkin-status" class="text-xs text-blue-100 mt-3 text-center hidden">✓ Check-in realizado hoje</p>
                </div>

                <!-- Card: Treinos Completados -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-2xl p-6 shadow-lg transform hover:scale-105 transition duration-300" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold">Completados</h3>
                        <i class="fas fa-trophy text-3xl opacity-50"></i>
                    </div>
                    <p class="text-4xl font-extrabold mb-2" id="completados-mes">0</p>
                    <p class="text-sm text-green-100">treinos este mês</p>
                </div>

                <!-- Card: Sequência -->
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-2xl p-6 shadow-lg transform hover:scale-105 transition duration-300" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold">Sequência</h3>
                        <i class="fas fa-fire text-3xl opacity-50"></i>
                    </div>
                    <p class="text-4xl font-extrabold mb-2" id="sequencia-dashboard">0</p>
                    <p class="text-sm text-purple-100">dias consecutivos</p>
                </div>
            </div>

            <!-- Próximo Treino -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-8" data-aos="fade-right">
                <div class="flex items-center gap-3 mb-6">
                    <i class="fas fa-dumbbell text-yellow-500 text-2xl"></i>
                    <h2 class="text-2xl font-bold text-gray-800">Seu Próximo Treino</h2>
                </div>
                <div id="next-workout-container" class="border-l-4 border-blue-500 pl-6 py-4"></div>
                <div id="next-workout-empty" class="text-center text-gray-500 py-8 hidden">
                    <i class="fas fa-checkmark-circle text-gray-300 text-4xl mb-2"></i>
                    <p>Todos os seus treinos estão completos! 🎉</p>
                </div>
                <div id="next-workout-loading" class="text-center text-gray-500 py-8">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Carregando próximo treino...
                </div>
            </div>
        </section>

        <!-- Workouts Section -->
        <section id="workouts" class="mb-12">
            <div class="flex items-center gap-3 mb-6" data-aos="fade-left">
                <i class="fas fa-list text-yellow-500 text-2xl"></i>
                <h2 class="text-2xl font-bold text-gray-800">Meus Treinos</h2>
            </div>

            <div id="workouts-list" class="grid grid-cols-1 md:grid-cols-2 gap-6"></div>
            <div id="workouts-empty" class="hidden bg-white rounded-2xl shadow-lg p-8 text-center" data-aos="fade-up">
                <i class="fas fa-dumbbell text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Nenhum treino encontrado</h3>
                <p class="text-gray-600">Você ainda não possui treinos cadastrados. Entre em contato com seu professor para receber seu plano de treino.</p>
            </div>
            <div id="workouts-loading" class="text-center py-8 text-gray-500">
                <i class="fas fa-spinner fa-spin mr-2"></i> Carregando treinos...
            </div>

            <div id="workout-modal" class="fixed inset-0 bg-black/50 items-center justify-center hidden z-50">
                <div class="bg-white w-11/12 md:w-2/3 rounded-2xl shadow-2xl p-6 relative">
                    <button id="close-workout-modal" class="absolute top-3 right-3 rounded-full bg-gray-200 hover:bg-gray-300 p-2">
                        <i class="fas fa-times"></i>
                    </button>
                    <h3 id="workout-modal-title" class="text-2xl font-bold text-gray-800 mb-3"></h3>
                    <p id="workout-modal-personal" class="text-sm text-gray-600 mb-3"></p>
                    <div id="workout-modal-body" class="space-y-2 max-h-72 overflow-y-auto"></div>
                    <div class="mt-5 flex justify-end gap-2">
                        <button id="workout-modal-start" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">Iniciar</button>
                        <button id="workout-modal-close" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg">Fechar</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Statistics Section -->
        <section id="stats" class="mb-12">
            <div class="flex items-center gap-3 mb-6" data-aos="fade-right">
                <i class="fas fa-chart-bar text-yellow-500 text-2xl"></i>
                <h2 class="text-2xl font-bold text-gray-800">Estatísticas</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Chart Card 1 -->
                <div class="bg-white rounded-2xl shadow-lg p-6" data-aos="fade-up">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-chart-line text-blue-500"></i>
                        Treinos por Semana
                    </h3>
                    <div id="weekly-chart" class="flex items-end justify-between h-40 gap-2"></div>
                    <div id="weekly-chart-no-data" class="text-center text-gray-500 text-sm hidden">Sem dados de check-in nos últimos 7 dias.</div>
                </div>

                <!-- Stats Card 2 -->
                <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col justify-between" data-aos="fade-up" data-aos-delay="100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-green-500"></i>
                        Meta do Mês
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-700 font-semibold">Progresso</span>
                                <span class="text-gray-600" id="meta-progress">0 / 20 treinos</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-gradient-to-r from-green-400 to-green-600 h-3 rounded-full" id="meta-bar" style="width: 0%;"></div>
                            </div>
                        </div>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center" id="meta-status" style="display: none;">
                            <p class="text-green-700 font-bold">✓ Meta atingida!</p>
                            <p class="text-sm text-green-600">Você já completou <span id="meta-percent">0%</span> da meta</p>
                        </div>
                    </div>
                </div>

                <!-- Stats Card 3 -->
                <div class="bg-white rounded-2xl shadow-lg p-6" data-aos="fade-up" data-aos-delay="200">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <i class="fas fa-crown text-yellow-500"></i>
                        Resumo Geral
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">Total de treinos</span>
                            <span class="text-2xl font-bold text-blue-600" id="total-treinos">0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">Check-ins no mês</span>
                            <span class="text-2xl font-bold text-green-600" id="checkins-mes">0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">Sequência atual</span>
                            <span class="text-2xl font-bold text-red-600" id="sequencia-atual">0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">Média semanal</span>
                            <span class="text-2xl font-bold text-purple-600" id="media-semanal">0.0</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-2xl shadow-lg p-6" data-aos="fade-up" data-aos-delay="300">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-history text-orange-500"></i>
                        Atividades Recentes
                    </h3>
                        <div id="recent-activities" class="space-y-3"></div>
                    <div id="recent-activities-empty" class="text-center text-gray-400 text-sm hidden">Nenhuma atividade recente encontrada.</div>
                </div>
            </div>
        </section>

    </main>

    <footer class="bg-gradient-to-r from-gray-800 to-gray-900 text-white py-6 mt-12">
        <div class="container mx-auto text-center">
            <p>&copy; 2026 Sthenos Gym. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init();

        // Function to show in-page notification
        function showNotification(title, body = '', type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
            
            // Set colors based on type
            const colors = {
                success: 'bg-green-500 text-white',
                error: 'bg-red-500 text-white',
                warning: 'bg-yellow-500 text-black',
                info: 'bg-blue-500 text-white'
            };
            
            notification.classList.add(...colors[type].split(' '));
            
            notification.innerHTML = `
                <div class="flex items-start justify-between">
                    <div>
                        <h4 class="font-bold">${title}</h4>
                        ${body ? `<p class="text-sm mt-1">${body}</p>` : ''}
                    </div>
                    <button class="ml-4 text-current hover:opacity-75" onclick="this.parentElement.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 300);
            }, 5000);
        }

        // Mobile nav toggle
        const mobileNavToggle = document.getElementById('mobile-nav-toggle');
        const mobileNav = document.getElementById('mobile-nav');
        if (mobileNavToggle && mobileNav) {
            mobileNavToggle.addEventListener('click', () => {
                mobileNav.classList.toggle('hidden');
            });
            // Close mobile nav when link is clicked
            mobileNav.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    mobileNav.classList.add('hidden');
                });
            });
        }

        // Check-in functionality
        const checkinBtn = document.getElementById('checkin-btn');
        const checkinStatus = document.getElementById('checkin-status');
        
        // Função para realizar check-in
        async function performCheckin(treinoId = null) {
            try {
                const formData = new URLSearchParams();
                if (treinoId) {
                    formData.append('treino_id', treinoId);
                }
                
                console.log('Iniciando check-in com dados:', Object.fromEntries(formData));
                
                const response = await fetch('../../API/checkin/post_checkin.php', {
                    method: 'POST',
                    credentials: 'include',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData
                });
                
                console.log('Response status:', response.status, 'ok:', response.ok);
                
                const data = await response.json();
                console.log('Response data:', data);
                
                if (data.success) {
                    console.log('Check-in realizado com sucesso');
                    // Check-in realizado com sucesso
                    checkinBtn.setAttribute('disabled', 'disabled');
                    checkinBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    checkinStatus.classList.remove('hidden');
                    checkinBtn.innerHTML = '<i class="fas fa-check"></i> Check-in Realizado!';
                    showNotification('Check-in Realizado!', 'Seu check-in diário foi registrado com sucesso.', 'success');
                    
                    // Recarregar estatísticas após check-in
                    loadStats();
                    return true;
                } else {
                    // Já fez check-in hoje ou erro
                    console.error('Erro no check-in:', data.message || 'Erro desconhecido');
                    showNotification('Atenção', data.message || 'Erro ao realizar check-in', data.message === 'Check-in já realizado hoje' ? 'warning' : 'error');
                    if (data.message === 'Check-in já realizado hoje') {
                        checkinBtn.setAttribute('disabled', 'disabled');
                        checkinBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        checkinStatus.classList.remove('hidden');
                        checkinBtn.innerHTML = '<i class="fas fa-check"></i> Check-in Realizado!';
                    }
                    return false;
                }
            } catch (error) {
                console.error('Erro ao fazer check-in:', error);
                showNotification('Erro', 'Erro ao conectar com o servidor. Tente novamente.', 'error');
                return false;
            }
        }
        
        // Verificar status do check-in ao carregar página (sem realizar check-in)
        async function checkCheckinStatus() {
            try {
                const response = await fetch('../../API/checkin/get_checkin_status.php', {
                    method: 'GET',
                    credentials: 'include',
                    headers: { 'Accept': 'application/json' }
                });
                
                const data = await response.json();
                
                if (data.success && data.checked_in) {
                    // Já fez check-in hoje
                    checkinBtn.setAttribute('disabled', 'disabled');
                    checkinBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    checkinStatus.classList.remove('hidden');
                    checkinBtn.innerHTML = '<i class="fas fa-check"></i> Check-in Realizado!';
                }
            } catch (error) {
                console.error('Erro ao verificar status do check-in:', error);
            }
        }
        
        checkinBtn.addEventListener('click', performCheckin);
        
        // Chamar verificação de status ao carregar página
        checkCheckinStatus();

        function renderTreinoCard(treino, corIndex) {
            const colors = [
                { gradient: 'from-blue-400 to-blue-600', icon: 'text-blue-500' },
                { gradient: 'from-green-400 to-green-600', icon: 'text-green-500' },
                { gradient: 'from-purple-400 to-purple-600', icon: 'text-purple-500' },
                { gradient: 'from-red-400 to-red-600', icon: 'text-red-500' },
                { gradient: 'from-yellow-400 to-yellow-600', icon: 'text-yellow-500' },
                { gradient: 'from-indigo-400 to-indigo-600', icon: 'text-indigo-500' }
            ];
            const color = colors[corIndex % colors.length];

            const exercicios = treino.exercicios || [];
            const totalExercicios = exercicios.length;
            const totalRepeticoes = exercicios.reduce((acc, e) => acc + (e.series * e.repeticoes), 0);

            // Determinar badge de status
            const statusConfig = {
                'nao_iniciado': { text: 'Não Iniciado', color: 'bg-gray-100 text-gray-700', icon: 'fa-clock' },
                'em_andamento': { text: 'Em Andamento', color: 'bg-blue-100 text-blue-700', icon: 'fa-play' },
                'completo': { text: 'Completo', color: 'bg-green-100 text-green-700', icon: 'fa-check' },
                'atrasado': { text: 'Atrasado', color: 'bg-red-100 text-red-700', icon: 'fa-exclamation-triangle' }
            };
            
            const status = statusConfig[treino.status] || statusConfig['nao_iniciado'];

            const card = document.createElement('article');
            card.className = 'bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300';

            let actionButton = '';
            if (status.text === 'Em Andamento') {
                actionButton = `
                    <button class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2 rounded-lg transition font-semibold flex items-center justify-center gap-2" onclick="encerrarTreino(${treino.id})">
                        <i class="fas fa-stop"></i> Encerrar
                    </button>
                `;
            } else if (status.text === 'Completo') {
                actionButton = `
                    <button class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg font-semibold cursor-not-allowed" disabled>
                        <i class="fas fa-check"></i> Concluído
                    </button>
                `;
            } else {
                actionButton = `
                    <button class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg transition font-semibold flex items-center justify-center gap-2" onclick="iniciarTreino(${treino.id})">
                        <i class="fas fa-play"></i> Iniciar
                    </button>
                `;
            }

            card.innerHTML = `
                <div class="bg-gradient-to-r ${color.gradient} h-2"></div>
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-dumbbell ${color.icon}"></i>
                                Treino #${treino.id}
                            </h3>
                            <p class="text-gray-600 text-sm mt-1">Personal: ${treino.personal_nome || 'Sem personal'}</p>
                        </div>
                        <span class="${status.color} px-3 py-1 rounded-full text-sm font-semibold flex items-center gap-1">
                            <i class="fas ${status.icon}"></i>
                            ${status.text}
                        </span>
                    </div>
                    <div class="space-y-2 mb-4 text-sm text-gray-600">
                        <p><i class="fas fa-list-check text-gray-400 mr-2"></i>${totalExercicios} exercícios</p>
                        <p><i class="fas fa-repeat text-gray-400 mr-2"></i>Aprox. ${totalRepeticoes} repetições totais</p>
                    </div>
                    <div class="flex gap-2">
                        ${actionButton}
                        <button class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 rounded-lg transition font-semibold flex items-center justify-center gap-2" onclick="verDetalhes(${treino.id})">
                            <i class="fas fa-info-circle"></i> Info
                        </button>
                    </div>
                </div>
            `;

            return card;
        }

        async function loadWorkouts() {
            const list = document.getElementById('workouts-list');
            const empty = document.getElementById('workouts-empty');
            const loading = document.getElementById('workouts-loading');

            list.innerHTML = '';
            empty.classList.add('hidden');
            loading.classList.remove('hidden');

            try {
                const response = await fetch('../../API/treino/get_treino.php', {
                    method: 'GET',
                    credentials: 'include',
                    headers: { 'Accept': 'application/json' }
                });

                console.log('Response status:', response.status);
                console.log('Response ok:', response.ok);

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Erro na resposta:', errorText);
                    throw new Error(`HTTP ${response.status}: ${errorText}`);
                }

                const data = await response.json();
                console.log('Dados recebidos:', data);
                if (!data.success || !Array.isArray(data.data) || data.data.length === 0) {
                    empty.classList.remove('hidden');
                    return;
                }

                window.workoutsData = data.data; // armazenar para uso no modal
                data.data.forEach((treino, index) => {
                    const card = renderTreinoCard(treino, index);
                    list.appendChild(card);
                });
            } catch (error) {
                console.error('Erro ao carregar treinos:', error);
                empty.querySelector('h3').textContent = 'Erro ao carregar treinos';
                empty.querySelector('p').textContent = 'Tente novamente mais tarde.';
                empty.classList.remove('hidden');
            } finally {
                loading.classList.add('hidden');
            }
        }

        function verDetalhes(treinoId) {
            const treino = (window.workoutsData || []).find(t => t.id === treinoId);
            if (!treino) {
                showNotification('Erro', 'Treino não encontrado.', 'error');
                return;
            }

            const modal = document.getElementById('workout-modal');
            const title = document.getElementById('workout-modal-title');
            const personal = document.getElementById('workout-modal-personal');
            const body = document.getElementById('workout-modal-body');
            const startBtn = document.getElementById('workout-modal-start');

            title.textContent = `Treino #${treino.id}`;
            personal.textContent = `Personal: ${treino.personal_nome || 'Sem personal'}`;

            if (!Array.isArray(treino.exercicios) || treino.exercicios.length === 0) {
                body.innerHTML = '<p class="text-gray-600">Nenhum exercício cadastrado para este treino.</p>';
            } else {
                body.innerHTML = treino.exercicios.map(ex => `
                    <div class="p-3 border border-gray-200 rounded-lg">
                        <div class="flex items-center justify-between">
                            <h4 class="font-semibold text-gray-800">${ex.nome}</h4>
                            <span class="text-xs text-gray-500">${ex.carga} kg</span>
                        </div>
                        <p class="text-sm text-gray-600">Séries: ${ex.series} • Repetições: ${ex.repeticoes}</p>
                    </div>
                `).join('');
            }

            if (treino.status === 'em_andamento') {
                startBtn.textContent = 'Encerrar';
                startBtn.className = 'bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg';
                startBtn.onclick = () => encerrarTreino(treinoId);
            } else if (treino.status === 'completo') {
                startBtn.textContent = 'Concluído';
                startBtn.className = 'bg-gray-300 text-gray-700 px-4 py-2 rounded-lg cursor-not-allowed';
                startBtn.onclick = null;
                startBtn.disabled = true;
            } else {
                startBtn.textContent = 'Iniciar';
                startBtn.className = 'bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg';
                startBtn.onclick = () => iniciarTreino(treinoId);
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        async function updateStatusTreino(treinoId, novoStatus) {
            try {
                const formData = new URLSearchParams();
                formData.append('treino_id', treinoId);
                formData.append('status', novoStatus);

                const response = await fetch('../../API/treino/update_status.php', {
                    method: 'POST',
                    credentials: 'include',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData
                });

                const data = await response.json();
                if (data.success) {
                    await loadWorkouts();
                    return true;
                }

                showNotification('Erro', data.message || 'Não foi possível atualizar o status do treino', 'error');
                return false;
            } catch (error) {
                console.error('Erro ao atualizar status do treino:', error);
                showNotification('Erro', 'Erro ao conectar com o servidor ao atualizar status do treino.', 'error');
                return false;
            }
        }

        async function iniciarTreino(treinoId) {
            const checkinOk = await performCheckin(treinoId);
            if (!checkinOk) {
                return;
            }

            const updated = await updateStatusTreino(treinoId, 'em_andamento');
            if (updated) {
                showNotification('Treino Iniciado!', `Treino #${treinoId} agora está em andamento.`, 'success');
            }
        }

        async function encerrarTreino(treinoId) {
            const updated = await updateStatusTreino(treinoId, 'completo');
            if (updated) {
                showNotification('Treino Encerrado!', `Treino #${treinoId} foi finalizado.`, 'success');
            }
        }

        function closeModal() {
            const modal = document.getElementById('workout-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.getElementById('close-workout-modal').addEventListener('click', closeModal);
        document.getElementById('workout-modal-close').addEventListener('click', closeModal);
        document.getElementById('workout-modal').addEventListener('click', function (e) {
            if (e.target === this) closeModal();
        });

        loadWorkouts();

        // Carregar próximo treino
        async function loadNextWorkout() {
            const container = document.getElementById('next-workout-container');
            const empty = document.getElementById('next-workout-empty');
            const loading = document.getElementById('next-workout-loading');
            
            try {
                const response = await fetch('../../API/treino/get_next_workout.php', {
                    method: 'GET',
                    credentials: 'include',
                    headers: { 'Accept': 'application/json' }
                });

                if (!response.ok) {
                    throw new Error('Erro ao carregar próximo treino');
                }

                const data = await response.json();

                if (!data.success || !data.data) {
                    empty.classList.remove('hidden');
                    loading.classList.add('hidden');
                    container.innerHTML = '';
                    return;
                }

                const treino = data.data;
                const totalExercicios = treino.exercicios?.length || 0;
                
                container.innerHTML = `
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Treino #${treino.id}</h3>
                            <p class="text-gray-600 flex items-center gap-2 mb-1">
                                <i class="fas fa-user text-gray-500"></i>
                                Personal: ${treino.personal_nome}
                            </p>
                            <p class="text-gray-600 flex items-center gap-2">
                                <i class="fas fa-dumbbell text-green-500"></i>
                                ${totalExercicios} exercício${totalExercicios !== 1 ? 's' : ''}
                            </p>
                        </div>
                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-full transition flex items-center gap-2" onclick="verDetalhes(${treino.id})">
                            <i class="fas fa-eye"></i> Ver Detalhes
                        </button>
                    </div>
                `;

                empty.classList.add('hidden');
                loading.classList.add('hidden');

            } catch (error) {
                console.error('Erro ao carregar próximo treino:', error);
                empty.innerHTML = '<p>Erro ao carregar próximo treino. Tente novamente mais tarde.</p>';
                empty.classList.remove('hidden');
                loading.classList.add('hidden');
            }
        }

        loadNextWorkout();

        // Função para carregar estatísticas
        async function loadStats() {
            try {
                const response = await fetch('../../API/checkin/get_stats.php', {
                    method: 'GET',
                    credentials: 'include',
                    headers: { 'Accept': 'application/json' }
                });

                if (!response.ok) {
                    throw new Error('Falha ao carregar estatísticas');
                }

                const data = await response.json();
                if (data.success) {
                    updateStats(data.data);
                }
            } catch (error) {
                console.error('Erro ao carregar estatísticas:', error);
            }
        }

        // Função para atualizar os elementos da página com as estatísticas
        function updateStats(stats) {
            // Dashboard principal
            document.getElementById('completados-mes').textContent = stats.checkins_mes;
            document.getElementById('sequencia-dashboard').textContent = stats.sequencia_consecutiva;

            // Meta do mês (assumindo meta de 20 treinos por mês)
            const metaMensal = 20;
            const progressoPercent = Math.min((stats.checkins_mes / metaMensal) * 100, 100);
            document.getElementById('meta-progress').textContent = `${stats.checkins_mes} / ${metaMensal} treinos`;
            document.getElementById('meta-bar').style.width = `${progressoPercent}%`;

            if (stats.checkins_mes >= metaMensal) {
                document.getElementById('meta-status').style.display = 'block';
                document.getElementById('meta-percent').textContent = `${Math.round(progressoPercent)}%`;
            } else {
                document.getElementById('meta-status').style.display = 'none';
            }

            // Resumo geral
            document.getElementById('total-treinos').textContent = stats.total_treinos;
            document.getElementById('checkins-mes').textContent = stats.checkins_mes;
            document.getElementById('sequencia-atual').textContent = stats.sequencia_consecutiva;
            document.getElementById('media-semanal').textContent = stats.media_semanal;

            // Gráfico semanal
            if (stats.weekly_checkins) {
                updateWeeklyChart(stats.weekly_checkins);
            }

            // Atividades recentes
            if (stats.recent_activities) {
                updateRecentActivities(stats.recent_activities);
            }
        }

        function updateRecentActivities(recentActivities) {
            const container = document.getElementById('recent-activities');
            const empty = document.getElementById('recent-activities-empty');
            container.innerHTML = '';

            if (!Array.isArray(recentActivities) || recentActivities.length === 0) {
                empty.classList.remove('hidden');
                return;
            }

            recentActivities.forEach(act => {
                const item = document.createElement('div');
                item.className = 'flex items-start gap-3 pb-3 border-b border-gray-100';
                item.innerHTML = `
                    <i class="fas fa-check-circle text-green-500 mt-1"></i>
                    <div>
                        <p class="font-semibold text-gray-800">${act.title}</p>
                        <p class="text-xs text-gray-600">${act.subtitle}</p>
                    </div>
                `;
                container.appendChild(item);
            });

            empty.classList.add('hidden');
        }

        function updateWeeklyChart(weeklyData) {
            const container = document.getElementById('weekly-chart');
            const noData = document.getElementById('weekly-chart-no-data');

            if (!container) return;

            container.innerHTML = '';
            if (!Array.isArray(weeklyData) || weeklyData.length === 0) {
                noData.classList.remove('hidden');
                return;
            }

            const maxValue = Math.max(...weeklyData.map(d => d.total), 1);
            weeklyData.forEach(item => {
                const heightPercent = Math.round((item.total / maxValue) * 100);
                const bar = document.createElement('div');
                bar.className = 'flex flex-col items-center flex-1 h-full justify-end';

                bar.innerHTML = `
                    <div class="w-3/4 bg-blue-500 rounded-t-lg transition-all duration-300" style="height: ${heightPercent}%"></div>
                    <p class="text-xs text-gray-600 mt-2">${item.label}</p>
                    <p class="text-xs text-gray-600">${item.total}</p>
                `;

                container.appendChild(bar);
            });

            noData.classList.add('hidden');
        }

        // Carregar estatísticas
        loadStats();

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href !== '#') {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });
    </script>
</body>
</html>
