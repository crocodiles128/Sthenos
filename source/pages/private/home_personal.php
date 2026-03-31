<?php
require_once __DIR__ . '/../../API/auth/routeAuthorization.php';

// Verifica se o usuário está logado e tem permissão para acessar
// Apenas usuários com estes cargos podem acessar:
$allowedRoles = ['professor', 'administrador'];
requirePermission($allowedRoles, '../../pages/public/login.php');

// Obtém informações do usuário logado da sessão (armazenadas no login)
$user_id = $_SESSION['user_id'];
$user_data = [
    'nome_completo' => $_SESSION['nome_completo'] ?? 'Personal',
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
    <title>Sthenos Gym - Painel do Personal</title>
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
                    <a href="#alunos" class="hover:text-yellow-400 transition flex items-center gap-1">
                        <i class="fas fa-users"></i>Meus Alunos
                    </a>
                    <a href="#treinos" class="hover:text-yellow-400 transition flex items-center gap-1">
                        <i class="fas fa-list"></i>Treinos
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
            <a href="#alunos" class="block px-4 py-3 text-white hover:bg-gray-800 border-b border-gray-700">
                <i class="fas fa-users"></i> Meus Alunos
            </a>
            <a href="#treinos" class="block px-4 py-3 text-white hover:bg-gray-800 border-b border-gray-700">
                <i class="fas fa-list"></i> Treinos
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
                <!-- Card: Total de Alunos -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-2xl p-6 shadow-lg transform hover:scale-105 transition duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold">Meus Alunos</h3>
                        <i class="fas fa-users text-3xl opacity-50"></i>
                    </div>
                    <p class="text-4xl font-extrabold mb-2" id="total-alunos">0</p>
                    <p class="text-sm text-blue-100">alunos ativos</p>
                </div>

                <!-- Card: Treinos Realizados -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-2xl p-6 shadow-lg transform hover:scale-105 transition duration-300" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold">Treinos Realizados</h3>
                        <i class="fas fa-dumbbell text-3xl opacity-50"></i>
                    </div>
                    <p class="text-4xl font-extrabold mb-2" id="treinos-realizados">0</p>
                    <p class="text-sm text-green-100">treinos este mês</p>
                </div>

                <!-- Card: Check-ins Hoje -->
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-2xl p-6 shadow-lg transform hover:scale-105 transition duration-300" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold">Check-ins Hoje</h3>
                        <i class="fas fa-calendar-check text-3xl opacity-50"></i>
                    </div>
                    <p class="text-4xl font-extrabold mb-2" id="checkins-hoje">0</p>
                    <p class="text-sm text-purple-100">alunos treinando</p>
                </div>
            </div>

            <!-- Próxima Sessão -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-8" data-aos="fade-right">
                <div class="flex items-center justify-between gap-3 mb-6">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-calendar text-yellow-500 text-2xl"></i>
                        <h2 class="text-2xl font-bold text-gray-800">Próximas Sessões</h2>
                    </div>
                    <select id="periodo-select" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="today">Hoje</option>
                        <option value="tomorrow">Amanhã</option>
                        <option value="week">Esta Semana</option>
                    </select>
                </div>
                <div id="proxima-sessao-container" class="space-y-4"></div>
                <div id="proxima-sessao-empty" class="text-center text-gray-500 py-8 hidden">
                    <i class="fas fa-calendar-times text-gray-300 text-4xl mb-2"></i>
                    <p id="proxima-sessao-empty-text">Nenhuma sessão agendada para o período selecionado.</p>
                </div>
                <div id="proxima-sessao-loading" class="text-center text-gray-500 py-8">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Carregando próximas sessões...
                </div>
            </div>
        </section>

        <!-- Alunos Section -->
        <section id="alunos" class="mb-12">
            <div class="flex items-center gap-3 mb-6" data-aos="fade-left">
                <i class="fas fa-users text-yellow-500 text-2xl"></i>
                <h2 class="text-2xl font-bold text-gray-800">Meus Alunos</h2>
            </div>

            <div id="alunos-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
            <div id="alunos-empty" class="hidden bg-white rounded-2xl shadow-lg p-8 text-center" data-aos="fade-up">
                <i class="fas fa-users text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Nenhum aluno encontrado</h3>
                <p class="text-gray-600">Você ainda não possui alunos cadastrados.</p>
            </div>
            <div id="alunos-loading" class="text-center py-8 text-gray-500">
                <i class="fas fa-spinner fa-spin mr-2"></i> Carregando alunos...
            </div>
        </section>

        <!-- Treinos Section -->
        <section id="treinos" class="mb-12">
            <div class="flex items-center gap-3 mb-6" data-aos="fade-right">
                <i class="fas fa-list text-yellow-500 text-2xl"></i>
                <h2 class="text-2xl font-bold text-gray-800">Gerenciar Treinos</h2>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <div class="flex flex-wrap gap-4 mb-6">
                    <button id="btn-novo-treino" onclick="criarTreino()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                        <i class="fas fa-plus"></i> Novo Treino
                    </button>
                    <button id="btn-ver-todos-treinos" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg transition flex items-center gap-2">
                        <i class="fas fa-list"></i> Ver Todos
                    </button>
                </div>

                <div id="treinos-recentes" class="grid grid-cols-1 md:grid-cols-2 gap-6"></div>
                <div id="treinos-empty" class="hidden text-center text-gray-500 py-8">
                    <i class="fas fa-dumbbell text-gray-300 text-4xl mb-2"></i>
                    <p>Nenhum treino criado recentemente.</p>
                </div>
                <div id="treinos-loading" class="text-center py-8 text-gray-500">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Carregando treinos...
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

        // Carregar estatísticas do personal
        async function loadPersonalStats() {
            try {
                const response = await fetch('../../API/personal/get_stats.php', {
                    method: 'GET',
                    credentials: 'include',
                    headers: { 'Accept': 'application/json' }
                });

                if (!response.ok) {
                    throw new Error('Falha ao carregar estatísticas');
                }

                const data = await response.json();
                if (data.success) {
                    updatePersonalStats(data.data);
                }
            } catch (error) {
                // Error loading personal stats - removed console.error for production
            }
        }

        // Atualizar elementos da página com estatísticas
        function updatePersonalStats(stats) {
            document.getElementById('total-alunos').textContent = stats.total_alunos || 0;
            document.getElementById('treinos-realizados').textContent = stats.treinos_realizados_mes || 0;
            document.getElementById('checkins-hoje').textContent = stats.checkins_hoje || 0;
        }

        // Carregar alunos do personal
        async function loadAlunos() {
            const list = document.getElementById('alunos-list');
            const empty = document.getElementById('alunos-empty');
            const loading = document.getElementById('alunos-loading');

            list.innerHTML = '';
            empty.classList.add('hidden');
            loading.classList.remove('hidden');

            try {
                const response = await fetch('../../API/aluno/get_alunos.php', {
                    method: 'GET',
                    credentials: 'include',
                    headers: { 'Accept': 'application/json' }
                });

                if (!response.ok) {
                    throw new Error('Erro ao carregar alunos');
                }

                const data = await response.json();
                // console.log('Alunos carregados:', data); // Removed for production

                if (!data.success || !Array.isArray(data.data) || data.data.length === 0) {
                    empty.classList.remove('hidden');
                    // console.warn('Nenhum aluno encontrado ou erro na resposta'); // Removed for production
                    return;
                }

                alunosData = Array.isArray(data.data) ? data.data : [];
                // console.log('alunosData atualizado:', alunosData); // Removed for production
                
                data.data.forEach(aluno => {
                    const card = renderAlunoCard(aluno);
                    list.appendChild(card);
                });

            } catch (error) {
                // console.error('Erro ao carregar alunos:', error); // Removed for production
                empty.querySelector('h3').textContent = 'Erro ao carregar alunos';
                empty.querySelector('p').textContent = 'Tente novamente mais tarde.';
                empty.classList.remove('hidden');
            } finally {
                loading.classList.add('hidden');
            }
        }

        function renderAlunoCard(aluno) {
            const card = document.createElement('article');
            card.className = 'bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300';

            card.innerHTML = `
                <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-2"></div>
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-user text-blue-500"></i>
                                ${aluno.nome_completo}
                            </h3>
                            <p class="text-gray-600 text-sm mt-1">${aluno.email}</p>
                        </div>
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-semibold">
                            Ativo
                        </span>
                    </div>
                    <div class="space-y-2 mb-4 text-sm text-gray-600">
                        <p><i class="fas fa-calendar text-gray-400 mr-2"></i>Último treino: ${aluno.ultimo_treino || 'Nunca'}</p>
                        <p><i class="fas fa-dumbbell text-gray-400 mr-2"></i>${aluno.total_treinos || 0} treinos realizados</p>
                    </div>
                    <div class="flex gap-2">
                        <button class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg transition font-semibold flex items-center justify-center gap-2" onclick="verPerfilAluno(${aluno.id})">
                            <i class="fas fa-eye"></i> Ver Perfil
                        </button>
                        <button class="flex-1 bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg transition font-semibold flex items-center justify-center gap-2" onclick="criarTreino(${aluno.id})">
                            <i class="fas fa-plus"></i> Novo Treino
                        </button>
                    </div>
                </div>
            `;

            return card;
        }

        // Carregar treinos recentes
        async function loadTreinosRecentes() {
            const container = document.getElementById('treinos-recentes');
            const empty = document.getElementById('treinos-empty');
            const loading = document.getElementById('treinos-loading');

            container.innerHTML = '';
            empty.classList.add('hidden');
            loading.classList.remove('hidden');

            try {
                const response = await fetch('../../API/treino/get_treinos_recentes.php', {
                    method: 'GET',
                    credentials: 'include',
                    headers: { 'Accept': 'application/json' }
                });

                if (!response.ok) {
                    throw new Error('Erro ao carregar treinos');
                }

                const data = await response.json();

                if (!data.success || !Array.isArray(data.data) || data.data.length === 0) {
                    empty.classList.remove('hidden');
                    return;
                }

                data.data.forEach(treino => {
                    const card = renderTreinoCard(treino);
                    container.appendChild(card);
                });

            } catch (error) {
                // console.error('Erro ao carregar treinos:', error); // Removed for production
                empty.innerHTML = '<p>Erro ao carregar treinos. Tente novamente mais tarde.</p>';
                empty.classList.remove('hidden');
            } finally {
                loading.classList.add('hidden');
            }
        }

        function renderTreinoCard(treino) {
            const card = document.createElement('article');
            card.className = 'bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300';

            card.innerHTML = `
                <div class="bg-gradient-to-r from-green-400 to-green-600 h-2"></div>
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-dumbbell text-green-500"></i>
                                Treino #${treino.id}
                            </h3>
                            <p class="text-gray-600 text-sm mt-1">Alunos: ${treino.alunos || 'Nenhum aluno'}</p>
                        </div>
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold">
                            ${treino.status || 'Criado'}
                        </span>
                    </div>
                    <div class="space-y-2 mb-4 text-sm text-gray-600">
                        <p><i class="fas fa-list-check text-gray-400 mr-2"></i>${treino.total_exercicios || 0} exercícios</p>
                        <p><i class="fas fa-calendar text-gray-400 mr-2"></i>Criado em ${treino.data_criacao || 'Hoje'}</p>
                    </div>
                    <div class="flex gap-2">
                        <button class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg transition font-semibold flex items-center justify-center gap-2" onclick="editarTreino(${treino.id})">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 rounded-lg transition font-semibold flex items-center justify-center gap-2" onclick="verDetalhesTreino(${treino.id})">
                            <i class="fas fa-info-circle"></i> Detalhes
                        </button>
                    </div>
                </div>
            `;

            return card;
        }

        // Carregar próxima sessão
        async function loadProximaSessao(period = 'today') {
            const container = document.getElementById('proxima-sessao-container');
            const empty = document.getElementById('proxima-sessao-empty');
            const emptyText = document.getElementById('proxima-sessao-empty-text');
            const loading = document.getElementById('proxima-sessao-loading');

            loading.classList.remove('hidden');
            empty.classList.add('hidden');
            container.innerHTML = '';

            try {
                const response = await fetch(`../../API/agendamento/get_agendamentos_periodo.php?period=${period}`, {
                    method: 'GET',
                    credentials: 'include',
                    headers: { 'Accept': 'application/json' }
                });

                if (!response.ok) {
                    throw new Error('Erro ao carregar agendamentos');
                }

                const data = await response.json();

                if (!data.success || !data.data || data.data.length === 0) {
                    const periodTexts = {
                        'today': 'Nenhuma sessão agendada para hoje.',
                        'tomorrow': 'Nenhuma sessão agendada para amanhã.',
                        'week': 'Nenhuma sessão agendada para esta semana.'
                    };
                    emptyText.textContent = periodTexts[period] || 'Nenhuma sessão agendada para o período selecionado.';
                    empty.classList.remove('hidden');
                    loading.classList.add('hidden');
                    return;
                }

                // Renderizar cada agendamento
                data.data.forEach((agendamento, index) => {
                    const dataHora = new Date(agendamento.data_hora);
                    const dataFormatada = dataHora.toLocaleDateString('pt-BR');
                    const horaFormatada = dataHora.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
                    
                    const card = document.createElement('div');
                    card.className = 'border-l-4 border-blue-500 pl-6 py-4 bg-gray-50 rounded-lg';
                    
                    card.innerHTML = `
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800 mb-2">${agendamento.aluno_nome}</h3>
                                <p class="text-gray-600 flex items-center gap-2 mb-1">
                                    <i class="fas fa-calendar-alt text-blue-500 w-4"></i>
                                    ${dataFormatada} às ${horaFormatada}
                                </p>
                                <p class="text-gray-600 flex items-center gap-2">
                                    <i class="fas fa-dumbbell text-green-500 w-4"></i>
                                    Treino ID: ${agendamento.treino_id}
                                </p>
                                <p class="text-gray-500 text-sm mt-2">
                                    Status: <span class="font-semibold ${getStatusColor(agendamento.status)}">${capitalize(agendamento.status)}</span>
                                </p>
                            </div>
                            <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-full transition flex items-center gap-2 text-sm" onclick="iniciarSessao(${agendamento.aluno_id})">
                                <i class="fas fa-play"></i> Iniciar
                            </button>
                        </div>
                    `;
                    
                    container.appendChild(card);
                });

                empty.classList.add('hidden');
                loading.classList.add('hidden');

            } catch (error) {
                // console.error('Erro ao carregar agendamentos:', error); // Removed for production
                emptyText.textContent = 'Erro ao carregar agendamentos. Tente novamente mais tarde.';
                empty.classList.remove('hidden');
                loading.classList.add('hidden');
            }
        }

        // Funções auxiliares
        function getStatusColor(status) {
            const colors = {
                'pendente': 'text-yellow-600',
                'confirmado': 'text-green-600',
                'cancelado': 'text-red-600',
                'realizado': 'text-blue-600'
            };
            return colors[status] || 'text-gray-600';
        }

        function capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        // Variáveis globais para o modal
        let exerciciosDisponiveis = [];
        let alunosData = [];
        let selectedAlunoId = null;
        let selectedAlunoName = '';

        // Carregar exercícios disponíveis
        async function loadExercicios() {
            try {
                const response = await fetch('../../API/treino/get_exercicios.php', {
                    method: 'GET',
                    credentials: 'include',
                    headers: { 'Accept': 'application/json' }
                });

                if (!response.ok) {
                    throw new Error('Erro ao carregar exercícios');
                }

                const data = await response.json();
                // console.log('Exercícios carregados:', data); // Removed for production
                
                if (data.success && Array.isArray(data.data)) {
                    exerciciosDisponiveis = data.data;
                    // console.log('exerciciosDisponiveis atualizado:', exerciciosDisponiveis); // Removed for production
                    return true;
                } else {
                    // console.warn('Resposta de exercícios sem sucesso ou dados não é array'); // Removed for production
                    return false;
                }
            } catch (error) {
                // console.error('Erro ao carregar exercícios:', error); // Removed for production
                return false;
            }
        }

        // Funções de ação
        function verPerfilAluno(alunoId) {
            showNotification('Em desenvolvimento', 'Funcionalidade será implementada em breve.', 'info');
        }

        function iniciarSessao(alunoId) {
            // Por enquanto, mostrar notificação e simular início de sessão
            showNotification('Iniciando sessão', `Preparando sessão para o aluno ${alunoId}...`, 'success');

            // Aqui poderia abrir um modal de check-in ou redirecionar para página específica
            // Por exemplo: window.location.href = `checkin.php?aluno=${alunoId}`;

            // Simulação: após 1 segundo, mostrar confirmação
            setTimeout(() => {
                showNotification('Sessão iniciada', 'O aluno pode começar seu treino!', 'success');
                // Recarregar a próxima sessão após iniciar uma
                loadProximaSessao();
            }, 1000);
        }

        function criarTreino(alunoId = null) {
            selectedAlunoId = alunoId;
            selectedAlunoName = '';
            const aluno = alunosData.find(a => a.id === alunoId);
            if (aluno) {
                selectedAlunoName = aluno.nome_completo;
            }
            openModalCriarTreino();
        }

        function openModalCriarTreino() {
            // Garantir que exercícios estão carregados
            if (!Array.isArray(exerciciosDisponiveis) || exerciciosDisponiveis.length === 0) {
                // console.log('Recarregando exercícios antes de abrir modal...'); // Removed for production
                loadExercicios().then(() => {
                    // console.log('Exercícios recarregados:', exerciciosDisponiveis); // Removed for production
                    document.getElementById('modal-criar-treino').classList.remove('hidden');
                    document.getElementById('exercicios-container').innerHTML = '';
                    renderAlunoSelects(alunosData);
                    addExercicioRow();
                });
            } else {
                document.getElementById('modal-criar-treino').classList.remove('hidden');
                document.getElementById('exercicios-container').innerHTML = '';
                renderAlunoSelects(alunosData);
                addExercicioRow(); // Adicionar primeira linha
            }
        }

        function closeModalCriarTreino() {
            // console.log('closeModalCriarTreino() chamada'); // Removed for production
            
            // Fechar o modal
            const modal = document.getElementById('modal-criar-treino');
            if (modal) {
                modal.classList.add('hidden');
                // console.log('Modal hide adicionada'); // Removed for production
            }
            
            // Limpar seleção de aluno
            selectedAlunoId = null;
            selectedAlunoName = '';
            
            // Limpar campo de busca de aluno
            const searchInput = document.getElementById('aluno-search-input');
            if (searchInput) {
                searchInput.value = '';
            }
            
            // Limpar informação de aluno selecionado
            const alunoInfo = document.getElementById('aluno-selecionado-info');
            if (alunoInfo) {
                alunoInfo.innerHTML = 'Aluno selecionado: <strong>Nenhum</strong>';
            }
            
            // Limpar dropdown de resultados
            const searchResults = document.getElementById('aluno-search-results');
            if (searchResults) {
                searchResults.classList.add('hidden');
                searchResults.innerHTML = '';
            }
            
            // Limpar todos os exercícios (remover todas as linhas)
            const exerciciosContainer = document.getElementById('exercicios-container');
            if (exerciciosContainer) {
                exerciciosContainer.innerHTML = '';
            }
            
            // Limpar agendamento
            const isRecurring = document.getElementById('is-recurring');
            if (isRecurring) {
                isRecurring.checked = false;
            }
            const recurringOptions = document.getElementById('recurring-options');
            if (recurringOptions) {
                recurringOptions.classList.add('hidden');
            }
            document.querySelectorAll('.recurring-day').forEach(cb => cb.checked = false);
            const endDate = document.getElementById('end-date');
            if (endDate) {
                endDate.value = '';
            }
            const treinoHorario = document.getElementById('treino-horario');
            if (treinoHorario) {
                treinoHorario.value = '08:00';
            }
            
            // console.log('Modal fechado e formulário zerado'); // Removed for production
        }

        function renderAlunoSelects(alunos) {
            const container = document.getElementById('alunos-select-container');
            container.innerHTML = '';

            // console.log('Renderizando selects com alunos:', alunos); // Removed for production

            if (!Array.isArray(alunos) || alunos.length === 0) {
                container.innerHTML = '<p class="text-gray-500">Nenhum aluno disponível.</p>';
                // console.warn('Nenhum aluno disponível para seleção'); // Removed for production
                return;
            }

            // Informação de aluno selecionado
            const selectedInfo = document.createElement('div');
            selectedInfo.className = 'mb-3 text-sm font-medium';
            selectedInfo.id = 'aluno-selecionado-info';
            selectedInfo.innerHTML = `Aluno selecionado: <strong>${selectedAlunoName || 'Nenhum'}</strong>`;

            // Container do input de busca
            const searchWrapper = document.createElement('div');
            searchWrapper.className = 'relative w-full';
            
            const searchInput = document.createElement('input');
            searchInput.id = 'aluno-search-input';
            searchInput.type = 'text';
            searchInput.className = 'w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent';
            searchInput.placeholder = 'Pesquisar aluno...';
            
            const resultsDiv = document.createElement('div');
            resultsDiv.id = 'aluno-search-results';
            resultsDiv.className = 'absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-lg max-h-52 overflow-y-auto hidden shadow-lg z-50';
            
            searchWrapper.appendChild(searchInput);
            searchWrapper.appendChild(resultsDiv);

            container.appendChild(selectedInfo);
            container.appendChild(searchWrapper);

            function updateResults(filterText) {
                const results = alunos
                    .filter(a =>
                        a.nome_completo.toLowerCase().includes(filterText.toLowerCase()) ||
                        (a.email && a.email.toLowerCase().includes(filterText.toLowerCase()))
                    )
                    .slice(0, 15);

                // console.log('Resultados filtrados para:', filterText, results); // Removed for production

                const resultsContainer = document.getElementById('aluno-search-results');
                resultsContainer.innerHTML = '';

                if (!results.length) {
                    resultsContainer.innerHTML = '<div class="px-3 py-2 text-sm text-gray-500">Nenhum aluno encontrado.</div>';
                    resultsContainer.classList.remove('hidden');
                    return;
                }

                results.forEach(aluno => {
                    const item = document.createElement('div');
                    item.className = 'px-3 py-2 text-sm hover:bg-blue-100 cursor-pointer border-b border-gray-100 last:border-b-0';
                    item.textContent = `${aluno.nome_completo} (${aluno.email})`;
                    item.addEventListener('click', () => {
                        selectedAlunoId = aluno.id;
                        selectedAlunoName = aluno.nome_completo;
                        document.getElementById('aluno-search-input').value = aluno.nome_completo;
                        document.getElementById('aluno-selecionado-info').innerHTML = `Aluno selecionado: <strong>${aluno.nome_completo}</strong>`;
                        resultsContainer.classList.add('hidden');
                        // console.log('Aluno selecionado:', aluno); // Removed for production
                    });
                    resultsContainer.appendChild(item);
                });

                resultsContainer.classList.remove('hidden');
            }

            searchInput.addEventListener('input', (e) => {
                const value = e.target.value.trim();
                // console.log('Input de busca:', value); // Removed for production
                if (!value) {
                    document.getElementById('aluno-search-results').classList.add('hidden');
                    selectedAlunoId = null;
                    selectedAlunoName = '';
                    document.getElementById('aluno-selecionado-info').innerHTML = 'Aluno selecionado: <strong>Nenhum</strong>';
                    return;
                }
                updateResults(value);
            });

            searchInput.addEventListener('focus', (e) => {
                const value = e.target.value.trim();
                if (value) {
                    updateResults(value);
                }
            });

            // Fechar dropdown ao clicar fora
            document.addEventListener('click', (e) => {
                const resultsContainer = document.getElementById('aluno-search-results');
                const searchInputElement = document.getElementById('aluno-search-input');
                if (resultsContainer && searchInputElement && !searchWrapper.contains(e.target)) {
                    resultsContainer.classList.add('hidden');
                }
            });

            if (selectedAlunoId) {
                const chosen = alunos.find(a => a.id === selectedAlunoId);
                if (chosen) {
                    searchInput.value = chosen.nome_completo;
                    selectedInfo.innerHTML = `Aluno selecionado: <strong>${chosen.nome_completo}</strong>`;
                }
            }
        }

        function getAlunoSelecionado() {
            return selectedAlunoId && Number.isInteger(selectedAlunoId) && selectedAlunoId > 0 ? selectedAlunoId : null;
        }

        function addExercicioRow() {
            const container = document.getElementById('exercicios-container');
            const rowIndex = container.children.length;

            // console.log('addExercicioRow() chamada. exerciciosDisponiveis:', exerciciosDisponiveis); // Removed for production

            const row = document.createElement('div');
            row.className = 'bg-gray-50 rounded-lg p-4 mb-4 border';
            
            // Criar elementos dinamicamente para ter controle total
            const numberBadge = document.createElement('span');
            numberBadge.className = 'bg-blue-500 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold';
            numberBadge.textContent = rowIndex + 1;
            
            const deleteBtn = document.createElement('button');
            deleteBtn.className = 'text-red-500 hover:text-red-700';
            deleteBtn.title = 'Remover exercício';
            deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
            deleteBtn.onclick = () => removeExercicioRow(deleteBtn);
            
            const headerDiv = document.createElement('div');
            headerDiv.className = 'flex items-center gap-3 mb-3';
            headerDiv.appendChild(numberBadge);
            headerDiv.appendChild(deleteBtn);
            
            // Criar select de exercícios
            const exercicioSelect = document.createElement('select');
            exercicioSelect.className = 'w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent';
            exercicioSelect.setAttribute('data-field', 'exercicio');
            
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = 'Carregando exercícios...';
            exercicioSelect.appendChild(defaultOption);
            
            // Adicionar todos os exercícios disponíveis
            if (Array.isArray(exerciciosDisponiveis) && exerciciosDisponiveis.length > 0) {
                // Remover opção "Carregando" se houver exercícios
                exercicioSelect.innerHTML = '<option value="">Selecione um exercício...</option>';
                
                exerciciosDisponiveis.forEach(ex => {
                    const option = document.createElement('option');
                    option.value = ex.id;
                    option.textContent = ex.nome;
                    exercicioSelect.appendChild(option);
                });
                // console.log('Adicionados ' + exerciciosDisponiveis.length + ' exercícios ao select'); // Removed for production
            } else {
                // console.warn('Nenhum exercício disponível ao adicionar linha'); // Removed for production
            }
            
            const exercicioDiv = document.createElement('div');
            exercicioDiv.className = '';
            const exercicioLabel = document.createElement('label');
            exercicioLabel.className = 'block text-sm font-medium text-gray-700 mb-1';
            exercicioLabel.textContent = 'Exercício';
            exercicioDiv.appendChild(exercicioLabel);
            exercicioDiv.appendChild(exercicioSelect);
            
            // Criar input de séries
            const seriesInput = document.createElement('input');
            seriesInput.type = 'number';
            seriesInput.min = '1';
            seriesInput.max = '10';
            seriesInput.className = 'w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent';
            seriesInput.placeholder = '3';
            seriesInput.setAttribute('data-field', 'series');
            
            const seriesDiv = document.createElement('div');
            seriesDiv.className = '';
            const seriesLabel = document.createElement('label');
            seriesLabel.className = 'block text-sm font-medium text-gray-700 mb-1';
            seriesLabel.textContent = 'Séries';
            seriesDiv.appendChild(seriesLabel);
            seriesDiv.appendChild(seriesInput);
            
            // Criar input de repetições
            const repeticoesInput = document.createElement('input');
            repeticoesInput.type = 'number';
            repeticoesInput.min = '1';
            repeticoesInput.max = '100';
            repeticoesInput.className = 'w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent';
            repeticoesInput.placeholder = '12';
            repeticoesInput.setAttribute('data-field', 'repeticoes');
            
            const repeticoesDiv = document.createElement('div');
            repeticoesDiv.className = '';
            const repeticoesLabel = document.createElement('label');
            repeticoesLabel.className = 'block text-sm font-medium text-gray-700 mb-1';
            repeticoesLabel.textContent = 'Repetições';
            repeticoesDiv.appendChild(repeticoesLabel);
            repeticoesDiv.appendChild(repeticoesInput);
            
            // Criar input de carga
            const cargaInput = document.createElement('input');
            cargaInput.type = 'number';
            cargaInput.min = '0';
            cargaInput.step = '0.5';
            cargaInput.className = 'w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent';
            cargaInput.placeholder = '20.0';
            cargaInput.setAttribute('data-field', 'carga');
            
            const cargaDiv = document.createElement('div');
            cargaDiv.className = '';
            const cargaLabel = document.createElement('label');
            cargaLabel.className = 'block text-sm font-medium text-gray-700 mb-1';
            cargaLabel.textContent = 'Carga (kg)';
            cargaDiv.appendChild(cargaLabel);
            cargaDiv.appendChild(cargaInput);
            
            // Agrupar inputs
            const gridDiv = document.createElement('div');
            gridDiv.className = 'grid grid-cols-1 md:grid-cols-4 gap-4';
            gridDiv.appendChild(exercicioDiv);
            gridDiv.appendChild(seriesDiv);
            gridDiv.appendChild(repeticoesDiv);
            gridDiv.appendChild(cargaDiv);
            
            row.appendChild(headerDiv);
            row.appendChild(gridDiv);
            container.appendChild(row);
            
            // console.log('Exercício adicionado. Total disponível:', exerciciosDisponiveis.length); // Removed for production
        }

        function removeExercicioRow(button) {
            const row = button.closest('.bg-gray-50');
            const container = document.getElementById('exercicios-container');
            if (container.children.length > 1) {
                row.remove();
                // Reordenar números
                const rows = container.querySelectorAll('.bg-gray-50');
                rows.forEach((r, index) => {
                    r.querySelector('.bg-blue-500').textContent = index + 1;
                });
            } else {
                showNotification('Erro', 'O treino deve ter pelo menos um exercício.', 'error');
            }
        }

        async function salvarTreino() {
            const exercicios = [];
            const rows = document.querySelectorAll('#exercicios-container .bg-gray-50');

            for (const row of rows) {
                const select = row.querySelector('select[data-field="exercicio"]');
                const seriesInput = row.querySelector('input[data-field="series"]');
                const repeticoesInput = row.querySelector('input[data-field="repeticoes"]');
                const cargaInput = row.querySelector('input[data-field="carga"]');

                const exercicio_id = select.value;
                const series_val = parseInt(seriesInput.value);
                const repeticoes_val = parseInt(repeticoesInput.value);
                const carga_val = parseFloat(cargaInput.value) || 0;

                if (!exercicio_id) {
                    showNotification('Erro', 'Selecione um exercício para todas as linhas.', 'error');
                    return;
                }
                if (!series_val || series_val < 1) {
                    showNotification('Erro', 'Informe um número válido de séries.', 'error');
                    return;
                }
                if (!repeticoes_val || repeticoes_val < 1) {
                    showNotification('Erro', 'Informe um número válido de repetições.', 'error');
                    return;
                }

                exercicios.push({
                    exercicio_id: parseInt(exercicio_id),
                    series: series_val,
                    repeticoes: repeticoes_val,
                    carga: carga_val
                });
            }

            const alunoSelecionadoId = getAlunoSelecionado();
            if (!alunoSelecionadoId) {
                showNotification('Erro', 'Selecione um aluno para este treino.', 'error');
                return;
            }

            if (exercicios.length === 0) {
                showNotification('Erro', 'Adicione pelo menos um exercício.', 'error');
                return;
            }

            const isRecurring = document.getElementById('is-recurring').checked;
            const endDate = document.getElementById('end-date').value;
            const treineHorario = document.getElementById('treino-horario').value;
            const recurringDays = [];
            if (isRecurring) {
                document.querySelectorAll('.recurring-day:checked').forEach(cb => {
                    recurringDays.push(parseInt(cb.value));
                });
                if (recurringDays.length === 0) {
                    showNotification('Erro', 'Selecione pelo menos um dia da semana para treino recorrente.', 'error');
                    return;
                }
            }
            if (!endDate) {
                showNotification('Erro', 'Informe a data final do treino.', 'error');
                return;
            }
            if (!treineHorario) {
                showNotification('Erro', 'Informe o horário do treino.', 'error');
                return;
            }

            try {
                const response = await fetch('../../API/treino/post_create_treino.php', {
                    method: 'POST',
                    credentials: 'include',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        alunos: [alunoSelecionadoId],
                        exercicios: exercicios,
                        is_recurring: isRecurring,
                        recurring_days: recurringDays,
                        end_date: endDate,
                        horario: treineHorario
                    })
                });

                if (!response.ok) {
                    throw new Error('Erro ao salvar treino');
                }

                const data = await response.json();

                if (data.success) {
                    showNotification('Sucesso', 'Treino criado com sucesso!', 'success');
                    closeModalCriarTreino();
                    // Recarregar treinos recentes
                    loadTreinosRecentes();
                } else {
                    showNotification('Erro', data.message || 'Erro ao criar treino.', 'error');
                }

            } catch (error) {
                // console.error('Erro ao salvar treino:', error); // Removed for production
                showNotification('Erro', 'Erro ao salvar treino. Tente novamente.', 'error');
            }
        }

        function editarTreino(treinoId) {
            showNotification('Em desenvolvimento', 'Funcionalidade será implementada em breve.', 'info');
        }

        function verDetalhesTreino(treinoId) {
            showNotification('Em desenvolvimento', 'Funcionalidade será implementada em breve.', 'info');
        }


        // Carregar dados ao inicializar
        loadPersonalStats();
        loadAlunos();
        loadTreinosRecentes();
        loadProximaSessao();
        loadExercicios(); // Carregar exercícios disponíveis

        // Aguardar DOM estar completamente carregado antes de adicionar event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // console.log('DOM carregado - adicionando event listeners'); // Removed for production
            
            // Toggle opções recorrentes
            const isRecurringCheckbox = document.getElementById('is-recurring');
            const recurringOptions = document.getElementById('recurring-options');
            if (isRecurringCheckbox && recurringOptions) {
                isRecurringCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        recurringOptions.classList.remove('hidden');
                    } else {
                        recurringOptions.classList.add('hidden');
                        // Desmarcar todos os dias
                        document.querySelectorAll('.recurring-day').forEach(cb => cb.checked = false);
                    }
                });
            }
            
            // Event listener para o select de período
            const periodoSelect = document.getElementById('periodo-select');
            if (periodoSelect) {
                periodoSelect.addEventListener('change', function() {
                    loadProximaSessao(this.value);
                });
            }
            
            // Event listeners para o modal
            const btnCloseModal = document.getElementById('btn-close-modal');
            const btnCancelarTreino = document.getElementById('btn-cancelar-treino');
            const btnAddExercicio = document.getElementById('btn-add-exercicio');
            const btnSalvarTreino = document.getElementById('btn-salvar-treino');
            
            // console.log('Elementos encontrados:', { ... }); // Removed for production
            
            if (btnCloseModal) {
                btnCloseModal.addEventListener('click', (e) => {
                    // console.log('Clicou em X'); // Removed for production
                    e.preventDefault();
                    e.stopPropagation();
                    closeModalCriarTreino();
                });
                // console.log('Event listener adicionado: btn-close-modal'); // Removed for production
            } else {
                // console.error('btn-close-modal não encontrado'); // Removed for production
            }
            
            if (btnCancelarTreino) {
                btnCancelarTreino.addEventListener('click', (e) => {
                    // console.log('Clicou em Cancelar'); // Removed for production
                    e.preventDefault();
                    e.stopPropagation();
                    closeModalCriarTreino();
                });
                // console.log('Event listener adicionado: btn-cancelar-treino'); // Removed for production
            } else {
                // console.error('btn-cancelar-treino não encontrado'); // Removed for production
            }
            
            if (btnAddExercicio) {
                btnAddExercicio.addEventListener('click', (e) => {
                    // console.log('Clicou em Adicionar Exercício'); // Removed for production
                    e.preventDefault();
                    addExercicioRow();
                });
            }
            
            if (btnSalvarTreino) {
                btnSalvarTreino.addEventListener('click', (e) => {
                    // console.log('Clicou em Salvar'); // Removed for production
                    e.preventDefault();
                    salvarTreino();
                });
            }
            
            // Fechar modal ao clicar fora
            const modalElement = document.getElementById('modal-criar-treino');
            if (modalElement) {
                modalElement.addEventListener('click', (e) => {
                    if (e.target.id === 'modal-criar-treino') {
                        closeModalCriarTreino();
                    }
                });
            }
        });

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

    <!-- Modal Criar Treino -->
    <div id="modal-criar-treino" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold flex items-center gap-2">
                        <i class="fas fa-plus"></i> Criar Novo Treino
                    </h2>
                    <button id="btn-close-modal" type="button" class="text-white hover:text-gray-200 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="p-6">
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Selecionar aluno</label>
                    <div id="alunos-select-container" class="relative border border-gray-200 rounded-lg p-3"></div>
                </div>

                <!-- Agendamento -->
                <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-3">Agendamento do Treino</h3>
                    
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" id="is-recurring" class="mr-2">
                            <span class="text-sm font-medium">Treino recorrente (repetir nos dias da semana)</span>
                        </label>
                    </div>

                    <div id="recurring-options" class="hidden mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dias da semana</label>
                        <div class="grid grid-cols-7 gap-2">
                            <label class="flex items-center">
                                <input type="checkbox" value="0" class="recurring-day mr-1"> Dom
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" value="1" class="recurring-day mr-1"> Seg
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" value="2" class="recurring-day mr-1"> Ter
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" value="3" class="recurring-day mr-1"> Qua
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" value="4" class="recurring-day mr-1"> Qui
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" value="5" class="recurring-day mr-1"> Sex
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" value="6" class="recurring-day mr-1"> Sáb
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Data final do treino</label>
                        <input type="date" id="end-date" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Para treino único, selecione a data desejada. Para recorrente, até quando repetir.</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Horário do treino</label>
                        <input type="time" id="treino-horario" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="08:00">
                        <p class="text-xs text-gray-500 mt-1">Horário padrão para os agendamentos.</p>
                    </div>
                </div>

                <div id="exercicios-container">
                    <!-- Exercícios serão adicionados aqui dinamicamente -->
                </div>

                <div class="flex gap-3 mt-6">
                    <button id="btn-add-exercicio" type="button" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                        <i class="fas fa-plus"></i> Adicionar Exercício
                    </button>
                </div>

                <div class="flex gap-3 mt-6 pt-6 border-t">
                    <button id="btn-salvar-treino" type="button" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition flex items-center gap-2 flex-1 justify-center">
                        <i class="fas fa-save"></i> Salvar Treino
                    </button>
                    <button id="btn-cancelar-treino" type="button" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition flex items-center gap-2">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
