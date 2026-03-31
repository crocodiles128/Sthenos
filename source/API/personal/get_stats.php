<?php
session_start();
require_once __DIR__ . '/../config/conecta.php';
require_once __DIR__ . '/../auth/routeAuthorization.php';

// Verifica se o usuário está logado e tem permissão para acessar
$allowedRoles = ['professor', 'colaborador_baixo', 'administrador'];
if (!checkPermission($allowedRoles)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

try {
    // 1. Total de alunos ativos do personal
    $stmt_alunos = $conn->prepare("
        SELECT COUNT(DISTINCT u.id) as total_alunos
        FROM users u
        INNER JOIN treinos t ON u.id = t.user_id
        WHERE t.personal_id = ? AND u.cargo IN ('usuario_cadastrado', 'aluno_pagante') AND u.status = 'ativo'
    ");
    $stmt_alunos->bind_param('i', $user_id);
    $stmt_alunos->execute();
    $total_alunos = $stmt_alunos->get_result()->fetch_assoc()['total_alunos'];
    $stmt_alunos->close();

    // 2. Treinos realizados pelos alunos no mês atual
    $stmt_treinos = $conn->prepare("
        SELECT COUNT(tr.id) as treinos_realizados_mes
        FROM treinos_realizados tr
        INNER JOIN treinos t ON tr.user_id = t.user_id
        WHERE t.personal_id = ? AND YEAR(tr.data_treino) = YEAR(CURDATE()) AND MONTH(tr.data_treino) = MONTH(CURDATE())
    ");
    $stmt_treinos->bind_param('i', $user_id);
    $stmt_treinos->execute();
    $treinos_realizados_mes = $stmt_treinos->get_result()->fetch_assoc()['treinos_realizados_mes'];
    $stmt_treinos->close();

    // 3. Check-ins realizados hoje pelos alunos do personal
    $stmt_checkins = $conn->prepare("
        SELECT COUNT(tr.id) as checkins_hoje
        FROM treinos_realizados tr
        INNER JOIN treinos t ON tr.user_id = t.user_id
        WHERE t.personal_id = ? AND DATE(tr.data_treino) = CURDATE()
    ");
    $stmt_checkins->bind_param('i', $user_id);
    $stmt_checkins->execute();
    $checkins_hoje = $stmt_checkins->get_result()->fetch_assoc()['checkins_hoje'];
    $stmt_checkins->close();

    // Retornar dados
    echo json_encode([
        'success' => true,
        'data' => [
            'total_alunos' => (int)$total_alunos,
            'treinos_realizados_mes' => (int)$treinos_realizados_mes,
            'checkins_hoje' => (int)$checkins_hoje
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor: ' . $e->getMessage()]);
}

$conn->close();
?>