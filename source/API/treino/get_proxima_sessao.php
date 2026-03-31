<?php
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
    // Busca próxima sessão: aluno com treino ativo que ainda não fez check-in hoje
    // Primeiro tenta encontrar alunos com treinos em andamento sem check-in hoje
    $stmt = $conn->prepare("
        SELECT DISTINCT
            u.id as aluno_id,
            u.nome_completo as aluno_nome,
            t.id as treino_id,
            t.status as treino_status,
            COUNT(te.id) as total_exercicios,
            MAX(tr.data_treino) as ultimo_checkin
        FROM users u
        INNER JOIN treinos t ON u.id = t.user_id
        LEFT JOIN treino_exercicios te ON t.id = te.treino_id
        LEFT JOIN treinos_realizados tr ON u.id = tr.user_id AND DATE(tr.data_treino) = CURDATE()
        WHERE t.personal_id = ?
            AND t.status IN ('em_andamento', 'criado')
            AND u.cargo IN ('usuario_cadastrado', 'aluno_pagante')
            AND u.status = 'ativo'
            AND tr.id IS NULL
        GROUP BY u.id, u.nome_completo, t.id, t.status
        ORDER BY t.data_treino DESC
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
        // Nenhuma sessão pendente
        echo json_encode(['success' => true, 'data' => null, 'message' => 'Nenhuma sessão pendente']);
        exit;
    }

    $row = $result->fetch_assoc();
    $stmt->close();

    $proxima_sessao = [
        'aluno_id' => (int) $row['aluno_id'],
        'aluno_nome' => $row['aluno_nome'],
        'treino_id' => (int) $row['treino_id'],
        'treino_atual' => 'Treino #' . $row['treino_id'] . ' (' . $row['total_exercicios'] . ' exercícios)',
        'horario' => 'Hoje', // Como não temos horário específico, usamos "Hoje"
        'status' => $row['treino_status'] === 'em_andamento' ? 'Em andamento' : 'Pronto para iniciar'
    ];

    echo json_encode(['success' => true, 'data' => $proxima_sessao]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>