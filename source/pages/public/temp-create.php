<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Temp Create - Usuários de Teste</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-b from-gray-100 to-gray-200 text-gray-900">

<?php
include_once __DIR__ . '/../../API/auth/conecta.php';

$mensagem = '';
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $nome = trim($_POST['nome_completo'] ?? '');
    $peso = trim($_POST['peso'] ?? '');
    $altura = trim($_POST['altura'] ?? '');
    $cargo = $_POST['cargo'] ?? 'usuario_cadastrado';
    $status = $_POST['status'] ?? 'ativo';
    $senha = trim($_POST['senha'] ?? '');

    if (!$email || !$nome || !$peso || !$altura || !$cargo || !$status) {
        $erro = 'Preencha todos os campos obrigatórios.';
    } else {
        $stmt = $conn->prepare('INSERT INTO users (email, nome_completo, peso, altura, cargo, status) VALUES (?, ?, ?, ?, ?, ?)');
        if ($stmt) {
            $stmt->bind_param('ssddss', $email, $nome, $peso, $altura, $cargo, $status);
            if ($stmt->execute()) {
                $mensagem = 'Usuário criado com sucesso (ID: ' . $stmt->insert_id . ').';

                if ($senha !== '') {
                    $hash = password_hash($senha, PASSWORD_DEFAULT);
                    $stmtAuth = $conn->prepare('INSERT INTO auth (email, senha) VALUES (?, ?)');
                    if ($stmtAuth) {
                        $stmtAuth->bind_param('ss', $email, $hash);
                        $stmtAuth->execute();
                        $stmtAuth->close();
                        $mensagem .= ' Credenciais de login adicionadas em auth.';
                    }
                }
            } else {
                $erro = 'Erro ao inserir usuário: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $erro = 'Erro na query de inserção: ' . $conn->error;
        }
    }
}

$cargos = [
    'usuario_cadastrado' => 'Usuário Cadastrado',
    'aluno_pagante' => 'Aluno Pagante',
    'professor' => 'Professor',
    'colaborador_baixo' => 'Colaborador Nível 1',
    'administrador' => 'Administrador',
];

$usuarios = [];
$result = $conn->query('SELECT * FROM users ORDER BY cargo, nome_completo');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
    $result->close();
}

function filtrarCargo(array $usuarios, string $cargo): array {
    return array_filter($usuarios, fn($u) => $u['cargo'] === $cargo);
}

function tabelaUsuarios(array $lista): string {
    if (count($lista) === 0) {
        return '<p class="text-sm text-gray-500">Nenhum usuário encontrado nesta categoria.</p>';
    }

    $html = '<div class="overflow-x-auto"><table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm"><thead><tr class="bg-gray-100"><th class="px-4 py-2 text-left">ID</th><th class="px-4 py-2 text-left">Email</th><th class="px-4 py-2 text-left">Nome completo</th><th class="px-4 py-2 text-left">Peso</th><th class="px-4 py-2 text-left">Altura</th><th class="px-4 py-2 text-left">Cargo</th><th class="px-4 py-2 text-left">Status</th></tr></thead><tbody>';
    foreach ($lista as $u) {
        $html .= '<tr class="border-t border-gray-100 hover:bg-gray-50">';
        $html .= '<td class="px-4 py-2">' . htmlspecialchars($u['id']) . '</td>';
        $html .= '<td class="px-4 py-2">' . htmlspecialchars($u['email']) . '</td>';
        $html .= '<td class="px-4 py-2">' . htmlspecialchars($u['nome_completo']) . '</td>';
        $html .= '<td class="px-4 py-2">' . htmlspecialchars($u['peso']) . '</td>';
        $html .= '<td class="px-4 py-2">' . htmlspecialchars($u['altura']) . '</td>';
        $html .= '<td class="px-4 py-2">' . htmlspecialchars($u['cargo']) . '</td>';
        $html .= '<td class="px-4 py-2">' . htmlspecialchars($u['status']) . '</td>';
        $html .= '</tr>';
    }
    $html .= '</tbody></table></div>';

    return $html;
}
?>

<div class="container mx-auto p-6">
    <div class="max-w-3xl bg-white rounded-3xl shadow-lg border border-gray-200 p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Usuários de Teste (temp-create)</h1>

        <?php if ($erro): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <?php if ($mensagem): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>

        <form method="post" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700">Email *</label>
                    <input id="email" name="email" type="email" required class="w-full px-4 py-2 border rounded-lg" placeholder="email@teste.com" />
                </div>
                <div>
                    <label for="nome_completo" class="block text-sm font-semibold text-gray-700">Nome completo *</label>
                    <input id="nome_completo" name="nome_completo" type="text" required class="w-full px-4 py-2 border rounded-lg" placeholder="João da Silva" />
                </div>
                <div>
                    <label for="peso" class="block text-sm font-semibold text-gray-700">Peso (kg) *</label>
                    <input id="peso" name="peso" type="number" step="0.01" required class="w-full px-4 py-2 border rounded-lg" placeholder="74.50" />
                </div>
                <div>
                    <label for="altura" class="block text-sm font-semibold text-gray-700">Altura (m) *</label>
                    <input id="altura" name="altura" type="number" step="0.01" required class="w-full px-4 py-2 border rounded-lg" placeholder="1.76" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="cargo" class="block text-sm font-semibold text-gray-700">Cargo *</label>
                    <select id="cargo" name="cargo" required class="w-full px-4 py-2 border rounded-lg">
                        <?php foreach ($cargos as $key => $label): ?>
                            <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700">Status *</label>
                    <select id="status" name="status" required class="w-full px-4 py-2 border rounded-lg">
                        <option value="ativo">Ativo</option>
                        <option value="inativo">Inativo</option>
                        <option value="pendente">Pendente</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="senha" class="block text-sm font-semibold text-gray-700">Senha (opcional, grava em auth)</label>
                <input id="senha" name="senha" type="password" class="w-full px-4 py-2 border rounded-lg" placeholder="senha123" />
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-full font-bold transition">Criar usuário de teste</button>
        </form>
    </div>

    <div class="mt-8 space-y-6">
        <div class="bg-white rounded-3xl border border-gray-200 p-4">
            <h2 class="text-xl font-bold mb-2">Todos os usuários cadastrados</h2>
            <?= tabelaUsuarios($usuarios) ?>
        </div>

        <?php foreach ($cargos as $key => $label): ?>
            <div class="bg-white rounded-3xl border border-gray-200 p-4">
                <h2 class="text-xl font-bold mb-2"><?= htmlspecialchars($label) ?></h2>
                <?= tabelaUsuarios(filtrarCargo($usuarios, $key)) ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
