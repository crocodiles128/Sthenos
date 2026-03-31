<?php
session_start();
require_once __DIR__ . '/../auth/routeAuthorization.php';
require_once __DIR__ . '/../config/conecta.php';

// Verifica se o usuário está logado
$allowedRoles = ['usuario_cadastrado', 'aluno_pagante', 'professor', 'colaborador_baixo', 'administrador'];
requirePermission($allowedRoles, '../../pages/public/login.php');

// Obtém o ID do usuário da sessão
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Obtém dados do POST
$treino_id = $_POST['treino_id'] ?? null;
$novo_status = $_POST['status'] ?? null;

if (!$treino_id || !$novo_status) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
    exit;
}

// Verifica se o treino pertence ao usuário
$stmt_verifica = $conn->prepare("SELECT id FROM treinos WHERE id = ? AND user_id = ?");
$stmt_verifica->bind_param('ii', $treino_id, $user_id);
$stmt_verifica->execute();
$result_verifica = $stmt_verifica->get_result();

if ($result_verifica->num_rows === 0) {
    $stmt_verifica->close();
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Treino não encontrado ou não pertence ao usuário']);
    exit;
}
$stmt_verifica->close();

// Status válidos
$status_validos = ['nao_iniciado', 'em_andamento', 'completo', 'atrasado'];
if (!in_array($novo_status, $status_validos)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Status inválido']);
    exit;
}

// Atualiza o status do treino
$stmt_update = $conn->prepare("UPDATE treinos SET status = ? WHERE id = ? AND user_id = ?");
$stmt_update->bind_param('sii', $novo_status, $treino_id, $user_id);

if ($stmt_update->execute()) {
    $stmt_update->close();
    echo json_encode(['success' => true, 'message' => 'Status do treino atualizado com sucesso']);
} else {
    $stmt_update->close();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar status do treino']);
}

$conn->close();
?>