<?php
require_once __DIR__ . '/../auth/routeAuthorization.php';

// Verifica se o usuário está logado e tem permissão para acessar
$allowedRoles = ['professor', 'administrador'];
requirePermission($allowedRoles, '../../pages/public/login.php');

header('Content-Type: application/json');

$personal_id = $_SESSION['user_id'];
$period = $_GET['period'] ?? 'today'; // today, tomorrow, week

try {
    $conn->begin_transaction();

    $today = date('Y-m-d');
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    $weekStart = $today;
    $weekEnd = date('Y-m-d', strtotime('+6 days'));

    // Determinar as datas baseado no período
    if ($period === 'today') {
        $startDate = $today;
        $endDate = $today;
    } elseif ($period === 'tomorrow') {
        $startDate = $tomorrow;
        $endDate = $tomorrow;
    } elseif ($period === 'week') {
        $startDate = $weekStart;
        $endDate = $weekEnd;
    } else {
        throw new Exception('Período inválido');
    }

    // Buscar agendamentos do personal para o período
    $stmt = $conn->prepare("
        SELECT 
            a.id as agendamento_id,
            a.data_hora,
            a.status,
            u.id as aluno_id,
            u.nome_completo as aluno_nome,
            t.id as treino_id,
            t.status as treino_status
        FROM agendamentos a
        JOIN treinos t ON a.treino_id = t.id
        JOIN users u ON a.usuario_id = u.id
        WHERE t.personal_id = ?
        AND DATE(a.data_hora) >= ?
        AND DATE(a.data_hora) <= ?
        ORDER BY a.data_hora ASC
    ");

    $stmt->bind_param('iss', $personal_id, $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $agendamentos = [];
    while ($row = $result->fetch_assoc()) {
        $agendamentos[] = $row;
    }

    $conn->commit();

    echo json_encode([
        'success' => true,
        'period' => $period,
        'data' => $agendamentos,
        'count' => count($agendamentos)
    ]);

} catch (Exception $e) {
    $conn->rollback();
    error_log('Erro ao carregar agendamentos do período: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao carregar agendamentos: ' . $e->getMessage()
    ]);
}
?>