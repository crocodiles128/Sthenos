<?php
session_start();
require_once __DIR__ . '/../auth/routeAuthorization.php';
require_once __DIR__ . '/../config/conecta.php';

// Verifica permissão de acesso (apenas usuários autenticados)
$allowedRoles = ['usuario_cadastrado', 'aluno_pagante', 'professor', 'colaborador_baixo', 'administrador'];
if (!checkPermission($allowedRoles)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

// Validar conexão com banco de dados
if (!$conn || $conn->connect_error) {
    error_log("API post_checkin.php: Erro na conexão com banco de dados: " . ($conn->connect_error ?? 'Conexão não inicializada'));
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao conectar com banco de dados']);
    exit;
}

// user_id do usuário logado em sessão
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

// Verifica se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

// Verificar se é check-in de treino específico
$treino_id = $_POST['treino_id'] ?? null;

// Data de hoje - usar formato correto
$data_hoje = date('Y-m-d');
error_log("API post_checkin.php: Iniciando check-in para user_id: $user_id, data_hoje: $data_hoje");

// Verifica se já existe check-in para hoje (checkins ou treinos_realizados)
$stmt_check = $conn->prepare(
    "SELECT id FROM checkins WHERE user_id = ? AND DATE(data) = ? UNION SELECT id FROM treinos_realizados WHERE user_id = ? AND DATE(data_treino) = ?"
);

if (!$stmt_check) {
    error_log("API post_checkin.php: Erro na preparação da query de verificação: " . $conn->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao verificar check-in anterior']);
    exit;
}

$stmt_check->bind_param('isiss', $user_id, $data_hoje, $user_id, $data_hoje);

if (!$stmt_check->execute()) {
    error_log("API post_checkin.php: Erro na execução da query de verificação: " . $stmt_check->error);
    $stmt_check->close();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao verificar check-in anterior']);
    exit;
}

$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    // Já fez check-in hoje
    error_log("API post_checkin.php: Usuário $user_id já fez check-in hoje");
    $stmt_check->close();
    echo json_encode(['success' => false, 'message' => 'Check-in já realizado hoje']);
    exit;
}

error_log("API post_checkin.php: Nenhum check-in anterior encontrado para $user_id");
$stmt_check->close();

// Insere novo check-in (APENAS registra presença, não ativa treinos automaticamente)
// Insere check-in na tabela checkins
$stmt_insert_checkins = $conn->prepare("INSERT INTO checkins (user_id, data) VALUES (?, ?)");
if (!$stmt_insert_checkins) {
    error_log("API post_checkin.php: Erro na preparação da query INSERT checkins: " . $conn->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao preparar inserção no checkins: ' . $conn->error]);
    exit;
}
$stmt_insert_checkins->bind_param('is', $user_id, $data_hoje);

// Insere também em treinos_realizados (para integridade com relatório e checagem)
$stmt_insert_treinos = $conn->prepare("INSERT INTO treinos_realizados (user_id, data_treino) VALUES (?, ?)");
if (!$stmt_insert_treinos) {
    error_log("API post_checkin.php: Erro na preparação da query INSERT treinos_realizados: " . $conn->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao preparar inserção em treinos_realizados: ' . $conn->error]);
    exit;
}
$stmt_insert_treinos->bind_param('is', $user_id, $data_hoje);

error_log("API post_checkin.php: Tentando inserir check-in para user_id: $user_id, data: $data_hoje");

if ($stmt_insert_checkins->execute() && $stmt_insert_treinos->execute()) {
    error_log("API post_checkin.php: Check-in inserido com sucesso para user_id: $user_id");
    $stmt_insert_checkins->close();
    $stmt_insert_treinos->close();

    // IMPORTANTE: Check-in NÃO deve ativar treinos automaticamente
    // A ativação de treino deve ser feita explicitamente pelo usuário ou personal
    // através de uma ação separada (não durante check-in)

    echo json_encode(['success' => true, 'message' => 'Check-in realizado com sucesso']);
} else {
    error_log("API post_checkin.php: Erro ao inserir check-in checkins: " . $stmt_insert_checkins->error . " treinos: " . $stmt_insert_treinos->error);
    $stmt_insert_checkins->close();
    $stmt_insert_treinos->close();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao registrar check-in']);
}
exit;
