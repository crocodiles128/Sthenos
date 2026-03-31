<?php
session_start();
require_once __DIR__ . '/../auth/routeAuthorization.php';
require_once __DIR__ . '/../config/conecta.php';

// Verifica se o usuário está logado
$allowedRoles = ['usuario_cadastrado', 'aluno_pagante', 'professor', 'colaborador_baixo', 'administrador'];
requirePermission($allowedRoles, '../../pages/public/login.php');

// Obtém o ID do usuário da sessão
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

try {
    // 1. Check-ins no mês atual
    $stmt_mes = $conn->prepare("
        SELECT COUNT(*) as checkins_mes
        FROM treinos_realizados
        WHERE user_id = ? AND YEAR(data_treino) = YEAR(CURDATE()) AND MONTH(data_treino) = MONTH(CURDATE())
    ");
    $stmt_mes->bind_param('i', $user_id);
    $stmt_mes->execute();
    $result_mes = $stmt_mes->get_result();
    $checkins_mes = $result_mes->fetch_assoc()['checkins_mes'];
    $stmt_mes->close();

    // 2. Sequência de check-ins consecutivos (all time)
    // Buscar todas as datas de check-in ordenadas por data decrescente
    $stmt_datas = $conn->prepare("
        SELECT DISTINCT DATE(data_treino) as data_checkin
        FROM treinos_realizados
        WHERE user_id = ?
        ORDER BY data_treino DESC
    ");
    $stmt_datas->bind_param('i', $user_id);
    $stmt_datas->execute();
    $result_datas = $stmt_datas->get_result();

    $sequencia_atual = 0;
    $data_anterior = null;
    $hoje = date('Y-m-d');
    $ontem = date('Y-m-d', strtotime('-1 day'));

    while ($row = $result_datas->fetch_assoc()) {
        $data_atual = $row['data_checkin'];

        if ($data_anterior === null) {
            // Primeiro registro (mais recente)
            if ($data_atual === $hoje) {
                $sequencia_atual = 1;
            } else {
                // Se o último check-in não foi hoje, sequência é 0
                $sequencia_atual = 0;
                break;
            }
        } else {
            // Calcular diferença em dias
            $diff = (strtotime($data_anterior) - strtotime($data_atual)) / (60 * 60 * 24);

            if ($diff == 1) {
                // Dias consecutivos
                $sequencia_atual++;
            } else {
                // Quebrou a sequência
                break;
            }
        }

        $data_anterior = $data_atual;
    }

    $stmt_datas->close();

    // 3. Total de treinos realizados (all time)
    $stmt_total = $conn->prepare("
        SELECT COUNT(*) as total_treinos
        FROM treinos_realizados
        WHERE user_id = ?
    ");
    $stmt_total->bind_param('i', $user_id);
    $stmt_total->execute();
    $result_total = $stmt_total->get_result();
    $total_treinos = $result_total->fetch_assoc()['total_treinos'];
    $stmt_total->close();

    // 4. Média semanal (baseada nos últimos 30 dias)
    $stmt_media = $conn->prepare("
        SELECT COUNT(*) as treinos_30_dias
        FROM treinos_realizados
        WHERE user_id = ? AND data_treino >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    ");
    $stmt_media->bind_param('i', $user_id);
    $stmt_media->execute();
    $result_media = $stmt_media->get_result();
    $treinos_30_dias = $result_media->fetch_assoc()['treinos_30_dias'];
    $media_semanal = round($treinos_30_dias / 4.3, 1); // Aproximadamente 4.3 semanas em 30 dias
    $stmt_media->close();

    // 5. Check-ins últimos 7 dias (dos últimos 6 dias + hoje)
    $stmt_weekly = $conn->prepare("
        SELECT DATE(data_treino) as dia, COUNT(*) as total
        FROM treinos_realizados
        WHERE user_id = ? AND data_treino >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
        GROUP BY DATE(data_treino)
        ORDER BY DATE(data_treino)
    ");
    $stmt_weekly->bind_param('i', $user_id);
    $stmt_weekly->execute();
    $result_weekly = $stmt_weekly->get_result();

    $weekly_data = [];
    $labelsMap = ['Sun'=>'Dom','Mon'=>'Seg','Tue'=>'Ter','Wed'=>'Qua','Thu'=>'Qui','Fri'=>'Sex','Sat'=>'Sab'];

    // Inicializa todos os dias com zero
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $dayKey = date('D', strtotime($date));
        $weekly_data[$date] = [
            'date' => $date,
            'label' => $labelsMap[$dayKey] ?? $dayKey,
            'total' => 0
        ];
    }

    while ($row = $result_weekly->fetch_assoc()) {
        $date = $row['dia'];
        if (isset($weekly_data[$date])) {
            $weekly_data[$date]['total'] = (int)$row['total'];
        }
    }
    $stmt_weekly->close();

    // 6. Atividades recentes (check-in/treinos realizados)
    $stmt_recent = $conn->prepare("\n        SELECT DATE_FORMAT(data_treino, '%Y-%m-%d') as dia, data_treino as dia_raw\n        FROM treinos_realizados\n        WHERE user_id = ?\n        ORDER BY data_treino DESC\n        LIMIT 4\n    ");
    $stmt_recent->bind_param('i', $user_id);
    $stmt_recent->execute();
    $result_recent = $stmt_recent->get_result();

    $recent_activities = [];
    while ($row = $result_recent->fetch_assoc()) {
        $date = $row['dia'];
        $weekday = date('D', strtotime($row['dia_raw']));
        $dayMonth = date('d/m', strtotime($row['dia_raw']));
        $labelsMap = ['Sun'=>'Dom','Mon'=>'Seg','Tue'=>'Ter','Wed'=>'Qua','Thu'=>'Qui','Fri'=>'Sex','Sat'=>'Sab'];
        $label = $labelsMap[$weekday] ?? $weekday;

        $recent_activities[] = [
            'title' => "Check-in realizado",
            'subtitle' => "$label, $dayMonth",
            'date' => $date
        ];
    }
    $stmt_recent->close();

    // Retornar dados
    echo json_encode([
        'success' => true,
        'data' => [
            'checkins_mes' => (int)$checkins_mes,
            'sequencia_consecutiva' => (int)$sequencia_atual,
            'total_treinos' => (int)$total_treinos,
            'media_semanal' => (float)$media_semanal,
            'weekly_checkins' => array_values($weekly_data),
            'recent_activities' => $recent_activities
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor: ' . $e->getMessage()]);
}

$conn->close();
?>