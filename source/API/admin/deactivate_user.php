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

$user_id = (int)($_POST['user_id'] ?? 0);

if ($user_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de usuário inválido']);
    exit;
}

try {
    // Verifica se o usuário existe e não é admin
    $stmt = $conn->prepare("SELECT cargo FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        $stmt->close();
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Usuário não encontrado']);
        exit;
    }
    $row = $result->fetch_assoc();
    if ($row['cargo'] === 'administrador') {
        $stmt->close();
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Não é possível desativar administradores']);
        exit;
    }
    $stmt->close();

    // Desativa o usuário
    $stmt = $conn->prepare("UPDATE users SET status = 'inativo' WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    if (!$stmt->execute()) {
        throw new Exception('Erro ao desativar usuário');
    }
    $stmt->close();

    echo json_encode(['success' => true, 'message' => 'Usuário desativado com sucesso']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}

$conn->close();
?>