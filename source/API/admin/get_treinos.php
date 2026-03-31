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

try {
    $stmt = $conn->prepare("
        SELECT t.id, t.user_id, t.personal_id, t.status, t.data_treino, t.data_atualizacao,
               u.nome_completo as aluno_nome, p.nome_completo as personal_nome
        FROM treinos t
        JOIN users u ON t.user_id = u.id
        JOIN users p ON t.personal_id = p.id
        ORDER BY t.data_atualizacao DESC
    ");
    $stmt->execute();
    $result = $stmt->get_result();

    $treinos = [];
    while ($row = $result->fetch_assoc()) {
        $treinos[] = $row;
    }
    $stmt->close();

    echo json_encode(['success' => true, 'data' => $treinos]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}

$conn->close();
?>