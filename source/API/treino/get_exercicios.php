<?php
require_once __DIR__ . '/../auth/routeAuthorization.php';

// Verifica se o usuário está logado e tem permissão para acessar
// Apenas usuários com estes cargos podem acessar:
$allowedRoles = ['professor', 'administrador'];
requirePermission($allowedRoles, '../../pages/public/login.php');

header('Content-Type: application/json');

try {
    // Buscar todos os exercícios disponíveis
    $stmt = $conn->prepare("SELECT id, nome, tutorial, video FROM exercicios ORDER BY nome");
    $stmt->execute();
    $result = $stmt->get_result();

    $exercicios = [];
    while ($row = $result->fetch_assoc()) {
        $exercicios[] = [
            'id' => $row['id'],
            'nome' => $row['nome'],
            'tutorial' => $row['tutorial'],
            'video' => $row['video']
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => $exercicios
    ]);

} catch (Exception $e) {
    error_log('Erro ao buscar exercícios: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor'
    ]);
}
?>