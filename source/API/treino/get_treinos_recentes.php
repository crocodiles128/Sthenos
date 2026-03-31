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
    // Busca treinos recentes criados pelo personal, usando a coluna treinos.user_id
    $stmt = $conn->prepare("
        SELECT
            t.id,
            t.personal_id,
            t.user_id AS aluno_id,
            COALESCE(u.nome_completo, 'Sem aluno') AS aluno_nome,
            t.status,
            COUNT(DISTINCT te.id) AS total_exercicios,
            DATE_FORMAT(t.data_treino, '%d/%m/%Y') AS data_criacao
        FROM treinos t
        LEFT JOIN users u ON t.user_id = u.id
        LEFT JOIN treino_exercicios te ON t.id = te.treino_id
        WHERE t.personal_id = ?
        GROUP BY t.id, t.personal_id, t.user_id, u.nome_completo, t.status, t.data_treino
        ORDER BY t.id DESC
        LIMIT 6
    ");

    if (!$stmt) {
        throw new Exception('Erro na preparação da query: ' . $conn->error);
    }

    $stmt->bind_param('i', $user_id);
    if (!$stmt->execute()) {
        throw new Exception('Erro na execução da query: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    $treinos = [];

    while ($row = $result->fetch_assoc()) {
        $treinos[] = [
            'id' => (int) $row['id'],
            'personal_id' => (int) $row['personal_id'],
            'alunos' => $row['aluno_nome'] ?? 'Sem aluno',
            'status' => $row['status'] ?: 'criado',
            'total_exercicios' => (int) $row['total_exercicios'],
            'data_criacao' => $row['data_criacao'] ?: 'Hoje'
        ];
    }

    $stmt->close();

    echo json_encode([
        'success' => true,
        'data' => $treinos,
        'total' => count($treinos)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>