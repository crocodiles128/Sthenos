<?php
session_start();
require_once __DIR__ . '/../auth/routeAuthorization.php';
require_once __DIR__ . '/../config/conecta.php';

// Apenas administrador pode acessar
$allowedRoles = ['administrador'];
if (!checkPermission($allowedRoles)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

$user_id = (int)($_POST['user_id'] ?? 0);
$nome_completo = trim($_POST['nome_completo'] ?? '');
$email = trim($_POST['email'] ?? '');
$cargo = $_POST['cargo'] ?? '';
$peso = isset($_POST['peso']) ? trim($_POST['peso']) : null;
$altura = isset($_POST['altura']) ? trim($_POST['altura']) : null;

// Validações
$errors = [];

if ($user_id <= 0) {
    $errors[] = 'ID de usuário inválido';
}

if (empty($nome_completo)) {
    $errors[] = 'Nome completo é obrigatório';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email válido é obrigatório';
}

$validCargos = ['usuario_cadastrado', 'aluno_pagante', 'professor', 'colaborador_baixo', 'administrador'];
if (!in_array($cargo, $validCargos)) {
    $errors[] = 'Cargo inválido';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Erros de validação', 'errors' => $errors]);
    exit;
}

try {
    // Verifica se o usuário existe
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        $stmt->close();
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Usuário não encontrado']);
        exit;
    }
    $stmt->close();

    // Verifica se email já existe em outro usuário
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param('si', $email, $user_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $stmt->close();
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Email já cadastrado por outro usuário']);
        exit;
    }
    $stmt->close();

    // Atualiza na tabela users
    $stmt = $conn->prepare("UPDATE users SET nome_completo = ?, email = ?, cargo = ?, peso = ?, altura = ? WHERE id = ?");
    $stmt->bind_param('sssssi', $nome_completo, $email, $cargo, $peso, $altura, $user_id);
    if (!$stmt->execute()) {
        throw new Exception('Erro ao atualizar usuário');
    }
    $stmt->close();

    echo json_encode(['success' => true, 'message' => 'Usuário atualizado com sucesso']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}

$conn->close();
?>