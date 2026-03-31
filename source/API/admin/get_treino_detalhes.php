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

$treino_id = (int)($_GET['treino_id'] ?? 0);

if ($treino_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de treino inválido']);
    exit;
}

try {
    // Busca detalhes do treino
    $stmt = $conn->prepare("
        SELECT t.id, t.user_id, t.personal_id, t.status, t.data_treino, t.data_atualizacao,
               u.nome_completo as aluno_nome, p.nome_completo as personal_nome
        FROM treinos t
        JOIN users u ON t.user_id = u.id
        JOIN users p ON t.personal_id = p.id
        WHERE t.id = ?
    ");
    $stmt->bind_param('i', $treino_id);
    $stmt->execute();
    $treino = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$treino) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Treino não encontrado']);
        exit;
    }

    // Busca exercícios do treino
    $stmt = $conn->prepare("
        SELECT te.series, te.repeticoes, te.carga, e.nome as exercicio_nome
        FROM treino_exercicios te
        JOIN exercicios e ON te.exercicio_id = e.id
        WHERE te.treino_id = ?
    ");
    $stmt->bind_param('i', $treino_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $exercicios = [];
    while ($row = $result->fetch_assoc()) {
        $exercicios[] = $row;
    }
    $stmt->close();

    echo json_encode([
        'success' => true,
        'data' => [
            'treino' => $treino,
            'exercicios' => $exercicios
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}

$conn->close();
?>