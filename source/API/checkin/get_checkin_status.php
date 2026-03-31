<?php
session_start();
require_once __DIR__ . '/../auth/routeAuthorization.php';

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

require_once __DIR__ . '/../config/conecta.php';

try {
    // Verifica se já fez check-in hoje em checkins ou treinos_realizados
    $stmt = $conn->prepare("
        SELECT id FROM checkins WHERE user_id = ? AND DATE(data) = CURDATE()
        UNION
        SELECT id FROM treinos_realizados WHERE user_id = ? AND DATE(data_treino) = CURDATE()
        LIMIT 1
    ");
    $stmt->bind_param('ii', $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $checkin = $result->fetch_assoc();
    
    if ($checkin) {
        echo json_encode(['success' => true, 'checked_in' => true, 'message' => 'Check-in já realizado hoje']);
    } else {
        echo json_encode(['success' => true, 'checked_in' => false, 'message' => 'Check-in pendente']);
    }
    
    $stmt->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}
?>