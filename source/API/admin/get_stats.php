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

header('Content-Type: application/json; charset=utf-8');

try {
    $stats = [];

    // Total de usuários ativos
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE status = 'ativo'");
    $stmt->execute();
    $stats['total_users'] = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Alunos ativos (pagantes)
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE cargo = 'aluno_pagante' AND status = 'ativo'");
    $stmt->execute();
    $stats['active_students'] = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Check-ins hoje (checkins + treinos_realizados)
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM (SELECT id FROM checkins WHERE DATE(data) = CURDATE() UNION ALL SELECT id FROM treinos_realizados WHERE DATE(data_treino) = CURDATE()) as u");
    $stmt->execute();
    $stats['checkins_today'] = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Treinos criados
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM treinos");
    $stmt->execute();
    $stats['created_workouts'] = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    echo json_encode(['success' => true, 'stats' => $stats]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}

$conn->close();
?>