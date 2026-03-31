<?php
require_once __DIR__ . '/../config/conecta.php';

// Script para agendar treinos recorrentes automaticamente
// Deve ser executado diariamente via cron job

$current_date = date('Y-m-d');
$current_day = (int) date('w'); // 0 = Domingo, 1 = Segunda, etc.

try {
    // Buscar treinos recorrentes para o dia atual
    $stmt = $conn->prepare("
        SELECT id, user_id, personal_id
        FROM treinos
        WHERE is_recurring = 1
        AND JSON_CONTAINS(recurring_days, ?)
    ");
    $day_json = json_encode($current_day);
    $stmt->bind_param('s', $day_json);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($treino = $result->fetch_assoc()) {
        $treino_id = $treino['id'];
        $user_id = $treino['user_id'];

        // Verificar se já existe agendamento para hoje
        $check_stmt = $conn->prepare("
            SELECT id FROM agendamentos
            WHERE usuario_id = ? AND treino_id = ? AND DATE(data_hora) = ?
        ");
        $check_stmt->bind_param('iis', $user_id, $treino_id, $current_date);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows == 0) {
            // Criar agendamento para hoje
            $data_hora = $current_date . ' 08:00:00'; // Horário padrão, pode ser configurável
            $insert_stmt = $conn->prepare("
                INSERT INTO agendamentos (usuario_id, treino_id, data_hora, status)
                VALUES (?, ?, ?, 'pendente')
            ");
            $insert_stmt->bind_param('iis', $user_id, $treino_id, $data_hora);
            $insert_stmt->execute();

            error_log("Agendamento automático criado para treino ID $treino_id no dia $current_date");
        }
    }

    echo "Script executado com sucesso em " . date('Y-m-d H:i:s') . "\n";

} catch (Exception $e) {
    error_log('Erro no script de agendamento automático: ' . $e->getMessage());
    echo 'Erro: ' . $e->getMessage() . "\n";
}
?>