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
    // Parâmetros de filtro de período customizado
    $from = isset($_GET['from']) ? $_GET['from'] : date('Y-m-d', strtotime('-30 days'));
    $to = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');

    // Validação mínima de datas
    if (!strtotime($from) || !strtotime($to)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Período inválido']);
        exit;
    }

    // total de checkins no período
    $stmt = $conn->prepare("SELECT COUNT(*) as total_checkins FROM treinos_realizados WHERE data_treino BETWEEN ? AND ?");
    $stmt->bind_param('ss', $from, $to);
    $stmt->execute();
    $total_checkins = $stmt->get_result()->fetch_assoc()['total_checkins'];
    $stmt->close();

    // total de treinos no período
    $stmt = $conn->prepare("SELECT COUNT(*) as total_treinos FROM treinos WHERE DATE(data_treino) BETWEEN ? AND ?");
    $stmt->bind_param('ss', $from, $to);
    $stmt->execute();
    $total_treinos = (int)$stmt->get_result()->fetch_assoc()['total_treinos'];
    $stmt->close();

    // usuários ativos
    $stmt = $conn->prepare("SELECT COUNT(*) as total_usuarios_ativos FROM users WHERE status = 'ativo'");
    $stmt->execute();
    $total_usuarios_ativos = (int)$stmt->get_result()->fetch_assoc()['total_usuarios_ativos'];
    $stmt->close();

    // Verificar se coluna created_at existe (algumas bases antigas não possuem)
    $result = $conn->query("SHOW COLUMNS FROM users LIKE 'created_at'");
    $hasCreatedAt = ($result && $result->num_rows > 0);

    if ($hasCreatedAt) {
        $stmt = $conn->prepare("SELECT COUNT(*) as novos_usuarios FROM users WHERE DATE(created_at) BETWEEN ? AND ?");
        $stmt->bind_param('ss', $from, $to);
        $stmt->execute();
        $novos_usuarios = (int)$stmt->get_result()->fetch_assoc()['novos_usuarios'];
        $stmt->close();
    } else {
        $novos_usuarios = 0;
    }

    $media_treinos_por_usuario = $total_usuarios_ativos > 0 ? round($total_treinos / $total_usuarios_ativos, 2) : 0;

    $media_treinos_por_usuario = $total_usuarios_ativos > 0 ? round($total_treinos / $total_usuarios_ativos, 2) : 0;

    echo json_encode([
        'success' => true,
        'data' => [
            'total_checkins' => $total_checkins,
            'novos_usuarios' => $novos_usuarios,
            'total_treinos' => $total_treinos,
            'total_usuarios_ativos' => $total_usuarios_ativos,
            'media_treinos_por_usuario' => $media_treinos_por_usuario,
            'from' => $from,
            'to' => $to
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}

$conn->close();
?>