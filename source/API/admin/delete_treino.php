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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

$treino_id = (int)($_POST['treino_id'] ?? 0);

if ($treino_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de treino inválido']);
    exit;
}

try {
    // Verifica se o treino existe
    $stmt = $conn->prepare("SELECT id FROM treinos WHERE id = ?");
    $stmt->bind_param('i', $treino_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        $stmt->close();
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Treino não encontrado']);
        exit;
    }
    $stmt->close();

    // Deleta exercícios do treino primeiro
    $stmt = $conn->prepare("DELETE FROM treino_exercicios WHERE treino_id = ?");
    $stmt->bind_param('i', $treino_id);
    $stmt->execute();
    $stmt->close();

    // Deleta o treino
    $stmt = $conn->prepare("DELETE FROM treinos WHERE id = ?");
    $stmt->bind_param('i', $treino_id);
    if (!$stmt->execute()) {
        throw new Exception('Erro ao deletar treino');
    }
    $stmt->close();

    echo json_encode(['success' => true, 'message' => 'Treino deletado com sucesso']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}

$conn->close();
?>