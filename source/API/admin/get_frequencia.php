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

    // Check-ins por dia
    $stmt = $conn->prepare("
        SELECT DATE(data) as data_dia, COUNT(*) as total_checkins
        FROM checkins
        WHERE DATE(data) >= ?
        GROUP BY DATE(data)
        ORDER BY DATE(data)
    ");
    $stmt->bind_param('s', $data_inicio);
    $stmt->execute();
    $result = $stmt->get_result();

    $checkins_por_dia = [];
    while ($row = $result->fetch_assoc()) {
        $checkins_por_dia[] = $row;
    }
    $stmt->close();

    // Total de check-ins no período
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM checkins WHERE DATE(data) >= ?");
    $stmt->bind_param('s', $data_inicio);
    $stmt->execute();
    $total_checkins = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Média diária
    $media_diaria = $dias > 0 ? round($total_checkins / $dias, 2) : 0;

    echo json_encode([
        'success' => true,
        'data' => [
            'checkins_por_dia' => $checkins_por_dia,
            'total_checkins' => $total_checkins,
            'media_diaria' => $media_diaria,
            'periodo_dias' => $dias
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}

$conn->close();
?>