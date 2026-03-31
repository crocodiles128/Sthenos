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

header('Content-Type: application/json; charset=utf-8');

$role = $_GET['role'] ?? '';

$validRoles = ['aluno_pagante', 'professor'];
if (!in_array($role, $validRoles)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Role inválido']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT id, nome_completo, email, cargo, status, peso, altura FROM users WHERE cargo = ? AND status = 'ativo' ORDER BY nome_completo");
    $stmt->bind_param('s', $role);
    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $stmt->close();

    echo json_encode(['success' => true, 'data' => $users]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}

$conn->close();
?>