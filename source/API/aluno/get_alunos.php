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
    // Busca TODOS os alunos pagantes ativos (um personal pode criar treino para qualquer aluno pagante)
    $stmt = $conn->prepare("
        SELECT 
            u.id,
            u.nome_completo,
            u.email,
            u.peso,
            u.altura,
            u.cargo,
            u.status,
            MAX(tr.data_treino) as ultimo_treino,
            COUNT(DISTINCT tr.id) as total_treinos
        FROM users u
        LEFT JOIN treinos_realizados tr ON u.id = tr.user_id
        WHERE u.cargo = 'aluno_pagante' OR u.cargo = 'administrador'
            AND u.status = 'ativo'
        GROUP BY u.id, u.nome_completo, u.email, u.peso, u.altura, u.cargo, u.status
        ORDER BY u.nome_completo ASC
    ");

    if (!$stmt) {
        throw new Exception('Erro na preparação da query: ' . $conn->error);
    }

    if (!$stmt->execute()) {
        throw new Exception('Erro na execução da query: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    $alunos = [];

    while ($row = $result->fetch_assoc()) {
        $alunos[] = [
            'id' => (int) $row['id'],
            'nome_completo' => $row['nome_completo'],
            'email' => $row['email'],
            'peso' => $row['peso'] ? (float) $row['peso'] : null,
            'altura' => $row['altura'] ? (float) $row['altura'] : null,
            'cargo' => $row['cargo'],
            'status' => $row['status'],
            'ultimo_treino' => $row['ultimo_treino'] ? date('d/m/Y', strtotime($row['ultimo_treino'])) : 'Nunca',
            'total_treinos' => (int) $row['total_treinos']
        ];
    }

    $stmt->close();

    echo json_encode([
        'success' => true,
        'data' => $alunos,
        'total' => count($alunos)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>