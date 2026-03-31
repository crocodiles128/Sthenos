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

$periodo = $_GET['periodo'] ?? '30'; // dias

try {
    $dias = (int)$periodo;
    $data_inicio = date('Y-m-d', strtotime("-{$dias} days"));

    // Treinos realizados por dia
    $stmt = $conn->prepare("
        SELECT DATE(data_treino) as data_dia, COUNT(*) as total_treinos
        FROM treinos_realizados
        WHERE DATE(data_treino) >= ?
        GROUP BY DATE(data_treino)
        ORDER BY DATE(data_treino)
    ");
    $stmt->bind_param('s', $data_inicio);
    $stmt->execute();
    $result = $stmt->get_result();

    $treinos_por_dia = [];
    while ($row = $result->fetch_assoc()) {
        $treinos_por_dia[] = $row;
    }
    $stmt->close();

    // Total de treinos realizados
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM treinos_realizados WHERE DATE(data_treino) >= ?");
    $stmt->bind_param('s', $data_inicio);
    $stmt->execute();
    $total_treinos = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Média de treinos por dia
    $media_treinos_diaria = $dias > 0 ? round($total_treinos / $dias, 2) : 0;

    // Usuários ativos (que fizeram check-in no período)
    $stmt = $conn->prepare("SELECT COUNT(DISTINCT user_id) as usuarios_ativos FROM checkins WHERE DATE(data) >= ?");
    $stmt->bind_param('s', $data_inicio);
    $stmt->execute();
    $usuarios_ativos = $stmt->get_result()->fetch_assoc()['usuarios_ativos'];
    $stmt->close();

    // Média de treinos por usuário ativo
    $media_por_usuario = $usuarios_ativos > 0 ? round($total_treinos / $usuarios_ativos, 2) : 0;

    echo json_encode([
        'success' => true,
        'data' => [
            'treinos_por_dia' => $treinos_por_dia,
            'total_treinos' => $total_treinos,
            'media_treinos_diaria' => $media_treinos_diaria,
            'usuarios_ativos' => $usuarios_ativos,
            'media_por_usuario' => $media_por_usuario,
            'periodo_dias' => $dias
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}

$conn->close();
?>