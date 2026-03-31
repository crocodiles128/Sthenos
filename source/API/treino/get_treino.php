<?php
require_once __DIR__ . '/../auth/routeAuthorization.php';
require_once __DIR__ . '/../config/conecta.php';

header('Content-Type: application/json; charset=utf-8');

// Verifica permissão de acesso (apenas usuários autenticados)
$allowedRoles = ['usuario_cadastrado', 'aluno_pagante', 'professor', 'colaborador_baixo', 'administrador'];
if (!checkPermission($allowedRoles)) {
    error_log("API get_treino.php: Acesso negado. Session user_id: " . ($_SESSION['user_id'] ?? 'null') . ", cargo: " . ($_SESSION['cargo'] ?? 'null'));
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

error_log("API get_treino.php: Permissões verificadas com sucesso");

// user_id do usuário logado em sessão
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

error_log("API get_treino.php: Iniciando para user_id: $user_id");

try {
    // Busca os treinos vinculados ao usuário
    $treinos = [];
    $stmt = $conn->prepare(
        "SELECT t.id, t.user_id, t.personal_id, t.status, u.nome_completo AS personal_nome
         FROM treinos t
         LEFT JOIN users u ON t.personal_id = u.id
         WHERE t.user_id = ?
         ORDER BY t.id DESC"
    );
    if (!$stmt) {
        error_log("API get_treino.php: Erro na preparação da query: " . $conn->error);
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erro na preparação da query: ' . $conn->error]);
        exit;
    }

    error_log("API get_treino.php: Query preparada com sucesso");

    $stmt->bind_param('i', $user_id);
    if (!$stmt->execute()) {
        error_log("API get_treino.php: Erro na execução da query: " . $stmt->error);
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erro na execução da query: ' . $stmt->error]);
        exit;
    }

    error_log("API get_treino.php: Query executada com sucesso");

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $treinoId = (int) $row['id'];

    // Buscar exercicios desse treino
    $stmtEx = $conn->prepare(
        "SELECT te.id, te.series, te.repeticoes, te.carga, COALESCE(e.nome, 'Exercício não encontrado') AS exercicio_nome
         FROM treino_exercicios te
         LEFT JOIN exercicios e ON te.exercicio_id = e.id
         WHERE te.treino_id = ?"
    );
    if (!$stmtEx) {
        error_log("API get_treino.php: Erro na preparação da query de exercícios para treino $treinoId: " . $conn->error);
        $exercicios = [];
    } else {
        $stmtEx->bind_param('i', $treinoId);
        if (!$stmtEx->execute()) {
            error_log("API get_treino.php: Erro na execução da query de exercícios para treino $treinoId: " . $stmtEx->error);
            $exercicios = [];
        } else {
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
        }
        $stmtEx->close();
    }

    // Determinar status baseado no status atual do banco
    $status_atual = $row['status'];
    if (empty($status_atual) || $status_atual === null) {
        $status_atual = 'nao_iniciado';
    }

    $treinos[] = [
        'id' => $treinoId,
        'personal_id' => (int) $row['personal_id'],
        'personal_nome' => $row['personal_nome'] ?? 'Sem personal',
        'status' => $status_atual,
        'exercicios' => $exercicios,
    ];
}
$stmt->close();

// Sucesso
error_log("API get_treino.php: Retornando " . count($treinos) . " treinos para user_id: $user_id");
echo json_encode(['success' => true, 'data' => $treinos]);

} catch (Exception $e) {
    error_log("API get_treino.php: Erro fatal: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor: ' . $e->getMessage()]);
}
