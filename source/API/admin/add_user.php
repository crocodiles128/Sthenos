<?php
session_start();
require_once __DIR__ . '/../auth/routeAuthorization.php';
require_once __DIR__ . '/../config/conecta.php';

// Verifica se o usuário está logado e é administrador
$allowedRoles = ['administrador'];
if (!checkPermission($allowedRoles)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

// Obtém dados do POST
$nome_completo = trim($_POST['nome_completo'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';
$confirmar_senha = $_POST['confirmar_senha'] ?? '';
$cargo = $_POST['cargo'] ?? '';
$peso = isset($_POST['peso']) ? trim($_POST['peso']) : null;
$altura = isset($_POST['altura']) ? trim($_POST['altura']) : null;

// Validações
$errors = [];

if (empty($nome_completo)) {
    $errors[] = 'Nome completo é obrigatório';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email válido é obrigatório';
}

if (empty($senha) || strlen($senha) < 6) {
    $errors[] = 'Senha deve ter pelo menos 6 caracteres';
}

if ($senha !== $confirmar_senha) {
    $errors[] = 'Senhas não coincidem';
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
    // Verifica se email já existe
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $stmt->close();
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Email já cadastrado']);
        exit;
    }
    $stmt->close();

    // Hash da senha
    $hashed_password = password_hash($senha, PASSWORD_DEFAULT);

    // Insere na tabela auth
    $stmt = $conn->prepare("INSERT INTO auth (email, senha) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $hashed_password);
    if (!$stmt->execute()) {
        throw new Exception('Erro ao inserir na tabela auth');
    }
    $auth_id = $stmt->insert_id;
    $stmt->close();

    // Insere na tabela users
    $stmt = $conn->prepare("INSERT INTO users (nome_completo, email, cargo, status, peso, altura) VALUES (?, ?, ?, 'ativo', ?, ?)");
    $stmt->bind_param("sssss", $nome_completo, $email, $cargo, $peso, $altura);
    if (!$stmt->execute()) {
        throw new Exception('Erro ao inserir na tabela users');
    }
    $stmt->close();

    echo json_encode(['success' => true, 'message' => 'Usuário adicionado com sucesso']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor: ' . $e->getMessage()]);
}

$conn->close();
?>