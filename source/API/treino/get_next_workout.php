<?php
session_start();
require_once __DIR__ . '/../config/conecta.php';
require_once __DIR__ . '/../auth/routeAuthorization.php';

// Verifica se o usuário está logado
$allowedRoles = ['usuario_cadastrado', 'aluno_pagante', 'professor', 'colaborador_baixo', 'administrador'];
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
    // Busca o primeiro treino com status 'nao_iniciado' ou 'em_andamento'
    // Se não houver, retorna o mais recente
    $stmt = $conn->prepare("
        SELECT t.id, t.user_id, t.personal_id, t.status, u.nome_completo AS personal_nome
        FROM treinos t
        LEFT JOIN users u ON t.personal_id = u.id
        WHERE t.user_id = ? AND t.status IN ('nao_iniciado', 'em_andamento')
        ORDER BY t.id ASC
        LIMIT 1
    ");
    
    if (!$stmt) {
        throw new Exception('Erro na preparação da query: ' . $conn->error);
    }
    
    $stmt->bind_param('i', $user_id);
    if (!$stmt->execute()) {
        throw new Exception('Erro na execução da query: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Se não houver treino não iniciado, retorna null (sem treino próximo)
        $stmt->close();
        echo json_encode(['success' => true, 'data' => null, 'message' => 'Nenhum treino disponível']);
        exit;
    }
    
    $treino = $result->fetch_assoc();
    $treinoId = (int) $treino['id'];
    $stmt->close();
    
    // Buscar exercícios desse treino
    $stmtEx = $conn->prepare("
        SELECT te.id, te.series, te.repeticoes, te.carga, COALESCE(e.nome, 'Exercício não encontrado') AS exercicio_nome
        FROM treino_exercicios te
        LEFT JOIN exercicios e ON te.exercicio_id = e.id
        WHERE te.treino_id = ?
    ");
    
    if (!$stmtEx) {
        throw new Exception('Erro na preparação query exercícios: ' . $conn->error);
    }
    
    $stmtEx->bind_param('i', $treinoId);
    if (!$stmtEx->execute()) {
        throw new Exception('Erro execução query exercícios: ' . $stmtEx->error);
    }
    
    $exResult = $stmtEx->get_result();
    $exercicios = [];
    
    while ($ex = $exResult->fetch_assoc()) {
        $exercicios[] = [
            'id' => (int) $ex['id'],
            'nome' => $ex['exercicio_nome'],
            'series' => (int) $ex['series'],
            'repeticoes' => (int) $ex['repeticoes'],
            'carga' => (float) $ex['carga'],
        ];
    }
    $stmtEx->close();
    
    $nextWorkout = [
        'id' => $treinoId,
        'personal_id' => (int) $treino['personal_id'],
        'personal_nome' => $treino['personal_nome'] ?? 'Sem personal',
        'status' => $treino['status'],
        'exercicios' => $exercicios,
    ];
    
    echo json_encode(['success' => true, 'data' => $nextWorkout]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>
