<?php
session_start();
require_once __DIR__ . '/../../API/auth/routeAuthorization.php';

// Verifica se o usuário está logado e é colaborador_baixo
$allowedRoles = ['colaborador_baixo'];
requirePermission($allowedRoles, '../../pages/public/login.php');

// Obtém informações do usuário logado
$user_id = $_SESSION['user_id'];
$user_data = [
    'nome_completo' => $_SESSION['nome_completo'] ?? 'Usuário',
    'email' => $_SESSION['email'] ?? '',
    'cargo' => $_SESSION['cargo'] ?? ''
];

// Validação adicional
if (empty($user_data['nome_completo']) || empty($user_data['email'])) {
    header('Location: ../../pages/public/login.php?error=' . urlencode('Sessão inválida. Faça login novamente.'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Novo Usuário - Sthenos Gym</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">

    <header class="bg-gradient-to-r from-gray-800 to-gray-900 text-white p-4 md:p-6 shadow-lg sticky top-0 z-50">
        <div class="container mx-auto flex items-center justify-between">
            <h1 class="text-2xl md:text-3xl font-bold flex items-center gap-2">
                <i class="fas fa-dumbbell text-yellow-500"></i>Sthenos Gym
            </h1>
            <div class="flex items-center gap-4">
                <div class="text-right text-sm">
                    <p class="font-semibold"><?php echo htmlspecialchars($user_data['nome_completo']); ?></p>
                    <p class="text-gray-400 text-xs">Colaborador</p>
                </div>
                <a href="../../API/auth/logout.php" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg transition flex items-center gap-2">
                    <i class="fas fa-sign-out-alt"></i>Sair
                </a>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8 max-w-2xl">
        <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
            <div class="mb-6">
                <h2 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-user-plus text-yellow-500"></i>Criar Novo Usuário
                </h2>
                <p class="text-gray-600 mt-2">Preencha os dados abaixo para registrar um novo usuário no sistema</p>
            </div>

            <!-- Mensagem de Sucesso ou Erro -->
            <div id="message-container"></div>

            <form id="form-create-user" class="space-y-5">
                <!-- Nome Completo -->
                <div>
                    <label for="nome_completo" class="block text-sm font-semibold text-gray-700 mb-2">Nome Completo <span class="text-red-500">*</span></label>
                    <input 
                        type="text" 
                        id="nome_completo" 
                        name="nome_completo" 
                        placeholder="Digite o nome completo"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                    />
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="exemplo@email.com"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                    />
                </div>

                <!-- Cargo -->
                <div>
                    <label for="cargo" class="block text-sm font-semibold text-gray-700 mb-2">Tipo de Usuário <span class="text-red-500">*</span></label>
                    <select 
                        id="cargo" 
                        name="cargo" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                    >
                        <option value="">-- Selecione um tipo --</option>
                        <option value="usuario_cadastrado">Usuário Cadastrado</option>
                        <option value="aluno_pagante">Aluno Pagante</option>
                        <option value="professor">Professor</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle"></i> Você pode criar apenas estes tipos de usuários
                    </p>
                </div>

                <!-- Peso e Altura (em grid) -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="peso" class="block text-sm font-semibold text-gray-700 mb-2">Peso (kg)</label>
                        <input 
                            type="number" 
                            id="peso" 
                            name="peso" 
                            placeholder="Ex: 75.5"
                            step="0.1"
                            min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                        />
                    </div>
                    <div>
                        <label for="altura" class="block text-sm font-semibold text-gray-700 mb-2">Altura (m)</label>
                        <input 
                            type="number" 
                            id="altura" 
                            name="altura" 
                            placeholder="Ex: 1.80"
                            step="0.01"
                            min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                        />
                    </div>
                </div>

                <!-- Senha -->
                <div>
                    <label for="senha" class="block text-sm font-semibold text-gray-700 mb-2">Senha <span class="text-red-500">*</span></label>
                    <input 
                        type="password" 
                        id="senha" 
                        name="senha" 
                        placeholder="Mínimo 6 caracteres"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                    />
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-lock"></i> Use uma senha segura com pelo menos 6 caracteres
                    </p>
                </div>

                <!-- Confirmar Senha -->
                <div>
                    <label for="confirmar_senha" class="block text-sm font-semibold text-gray-700 mb-2">Confirmar Senha <span class="text-red-500">*</span></label>
                    <input 
                        type="password" 
                        id="confirmar_senha" 
                        name="confirmar_senha" 
                        placeholder="Confirme a senha"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                    />
                </div>

                <!-- Nota de Restrição -->
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Restrições:</strong> Você não pode criar usuários com nível de administrador ou igual ao seu.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Botões -->
                <div class="flex gap-4 pt-4">
                    <button 
                        type="submit" 
                        class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center justify-center gap-2"
                    >
                        <i class="fas fa-check-circle"></i>Criar Usuário
                    </button>
                    <a 
                        href="home.php" 
                        class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center justify-center gap-2"
                    >
                        <i class="fas fa-times-circle"></i>Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const form = document.getElementById('form-create-user');
        const messageContainer = document.getElementById('message-container');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Limpa mensagens anteriores
            messageContainer.innerHTML = '';

            // Validação de cliente
            const senha = document.getElementById('senha').value;
            const confirmarSenha = document.getElementById('confirmar_senha').value;

            if (senha !== confirmarSenha) {
                showMessage('As senhas não coincidem', 'error');
                return;
            }

            if (senha.length < 6) {
                showMessage('A senha deve ter pelo menos 6 caracteres', 'error');
                return;
            }

            const formData = new FormData(form);

            try {
                const response = await fetch('../../API/colaborador/add_user.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showMessage(`✓ ${data.message}`, 'success');
                    form.reset();
                    
                    // Redireciona após 2 segundos
                    setTimeout(() => {
                        window.location.href = 'home.php';
                    }, 2000);
                } else {
                    if (data.errors) {
                        showMessage(`✗ ${data.errors.join(', ')}`, 'error');
                    } else {
                        showMessage(`✗ ${data.message}`, 'error');
                    }
                }
            } catch (error) {
                showMessage(`✗ Erro ao comunicar com o servidor: ${error.message}`, 'error');
            }
        });

        function showMessage(message, type) {
            const alertClass = type === 'success' 
                ? 'bg-green-50 border-green-400 text-green-700' 
                : 'bg-red-50 border-red-400 text-red-700';
            
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

            messageContainer.innerHTML = `
                <div class="mb-4 p-4 rounded-lg border-l-4 ${alertClass}">
                    <div class="flex items-center gap-2">
                        <i class="fas ${icon}"></i>
                        <span>${message}</span>
                    </div>
                </div>
            `;
        }
    </script>

</body>
</html>
