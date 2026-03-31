<?php
require_once __DIR__ . '/../auth/routeAuthorization.php';

// Verifica se o usuário está logado e tem permissão para acessar
// Apenas usuários com estes cargos podem acessar:
$allowedRoles = ['professor', 'administrador'];
requirePermission($allowedRoles, '../../pages/public/login.php');

header('Content-Type: application/json');

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Método não permitido'
    ]);
    exit;
}

// Obter dados JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode([
        'success' => false,
        'message' => 'Dados inválidos'
    ]);
    exit;
}

$aluno_ids = $input['alunos'] ?? [];
$exercicios = $input['exercicios'] ?? [];
$is_recurring = $input['is_recurring'] ?? false;
$recurring_days = $input['recurring_days'] ?? [];
$end_date = $input['end_date'] ?? null;
$horario = $input['horario'] ?? '08:00';

if (!is_array($aluno_ids) || empty($aluno_ids) || !is_array($exercicios) || empty($exercicios)) {
    echo json_encode([
        'success' => false,
        'message' => 'Dados obrigatórios não fornecidos (alunos e exercícios)'
    ]);
    exit;
}

// Validar dados de recorrência
if ($is_recurring && (!is_array($recurring_days) || empty($recurring_days))) {
    echo json_encode([
        'success' => false,
        'message' => 'Para treinos recorrentes, deve fornecer os dias da semana'
    ]);
    exit;
}

if ($is_recurring) {
    $recurring_days = array_values(array_unique(array_filter($recurring_days, function($day) {
        return is_numeric($day) && $day >= 0 && $day <= 6;
    })));
    if (empty($recurring_days)) {
        echo json_encode([
            'success' => false,
            'message' => 'Dias da semana inválidos para recorrência'
        ]);
        exit;
    }
    if (!$end_date || !strtotime($end_date)) {
        echo json_encode([
            'success' => false,
            'message' => 'Para treinos recorrentes, deve fornecer a data final'
        ]);
        exit;
    }
} elseif ($end_date && !strtotime($end_date)) {
    echo json_encode([
        'success' => false,
        'message' => 'Data final inválida'
    ]);
    exit;
}

// Validar horário
if (!$horario || !preg_match('/^\d{2}:\d{2}$/', $horario)) {
    echo json_encode([
        'success' => false,
        'message' => 'Horário inválido (use formato HH:MM)'
    ]);
    exit;
}

// Garantir IDs únicos e válidos de aluno
$aluno_ids = array_values(array_unique(array_filter($aluno_ids, function($id) {
    return is_numeric($id) && $id > 0;
})));

if (empty($aluno_ids)) {
    echo json_encode([
        'success' => false,
        'message' => 'Nenhum aluno válido fornecido'
    ]);
    exit;
}

$personal_id = $_SESSION['user_id'];
$aluno_id = (int) $aluno_ids[0]; // Um treino tem apenas um aluno

try {
    // Iniciar transação
    $conn->begin_transaction();

    // Inserir novo treino
    $recurring_json = $is_recurring ? json_encode($recurring_days) : null;
    $stmt = $conn->prepare("INSERT INTO treinos (user_id, personal_id, status, is_recurring, recurring_days, end_date) VALUES (?, ?, 'criado', ?, ?, ?)");
    $stmt->bind_param('iiiss', $aluno_id, $personal_id, $is_recurring, $recurring_json, $end_date);
    if (!$stmt->execute()) {
        throw new Exception('Erro ao inserir treino: ' . $stmt->error);
    }
    $treino_id = $conn->insert_id;

    // Inserir exercícios do treino
    $stmt_exercicio = $conn->prepare("INSERT INTO treino_exercicios (treino_id, exercicio_id, series, repeticoes, carga) VALUES (?, ?, ?, ?, ?)");

    foreach ($exercicios as $exercicio) {
        $exercicio_id = $exercicio['exercicio_id'] ?? null;
        $series = $exercicio['series'] ?? null;
        $repeticoes = $exercicio['repeticoes'] ?? null;
        $carga = $exercicio['carga'] ?? null;

        if (!$exercicio_id || !$series || !$repeticoes) {
            throw new Exception('Dados do exercício inválidos');
        }

        $stmt_exercicio->bind_param('iiiid', $treino_id, $exercicio_id, $series, $repeticoes, $carga);
        $stmt_exercicio->execute();
    }

    // Confirmar transação
    $conn->commit();

    // Criar agendamentos
    if ($is_recurring && $end_date) {
        // Criar agendamentos recorrentes
        $start_date = date('Y-m-d');
        $current_date = strtotime($start_date);
        $end_timestamp = strtotime($end_date);

        $stmt_agendamento = $conn->prepare("INSERT INTO agendamentos (usuario_id, treino_id, data_hora, status) VALUES (?, ?, ?, 'pendente')");

        while ($current_date <= $end_timestamp) {
            $day_of_week = date('w', $current_date);
            if (in_array((int)$day_of_week, $recurring_days)) {
                $data_hora = date('Y-m-d', $current_date) . ' ' . $horario . ':00';
                $stmt_agendamento->bind_param('iis', $aluno_id, $treino_id, $data_hora);
                $stmt_agendamento->execute();
            }
            $current_date = strtotime('+1 day', $current_date);
        }
    } elseif ($end_date) {
        // Agendamento único
        $data_hora = $end_date . ' ' . $horario . ':00';
        $stmt_agendamento = $conn->prepare("INSERT INTO agendamentos (usuario_id, treino_id, data_hora, status) VALUES (?, ?, ?, 'pendente')");
        $stmt_agendamento->bind_param('iis', $aluno_id, $treino_id, $data_hora);
        $stmt_agendamento->execute();
    }

    echo json_encode([
        'success' => true,
        'message' => 'Treino criado com sucesso',
        'data' => [
            'treino_id' => $treino_id
        ]
    ]);

} catch (Exception $e) {
    // Reverter transação em caso de erro
    $conn->rollback();

    error_log('Erro ao criar treino: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao criar treino: ' . $e->getMessage()
    ]);
}
?>