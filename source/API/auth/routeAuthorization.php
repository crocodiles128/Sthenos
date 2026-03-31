<?php
// Garantir que a sessão está ativa (seguro contra múltiplas inclusões)
if (!session_id()) {
    session_start();
}

require_once __DIR__ . '/../config/conecta.php';

/**
 * Verifica se o usuário logado tem permissão para acessar uma página com base nos cargos permitidos.
 *
 * @param array|string $requiredRoles Os cargos necessários para acessar a página (ex: ['aluno', 'personal'] ou 'admin').
 * @return bool Retorna true se o usuário tem permissão, false caso contrário.
 */
function checkPermission($requiredRoles) {
    global $conn;
    
    // Verifica se o usuário está logado
    if (!isset($_SESSION['user_id'])) {
        return false;
    }

    $user_id = $_SESSION['user_id'];

    // Busca o cargo do usuário no banco de dados
    $stmt = $conn->prepare("SELECT cargo FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return false; // Usuário não encontrado
    }

    $row = $result->fetch_assoc();
    $userRole = $row['cargo'];

    // Fecha a consulta
    $stmt->close();

    // Se $requiredRoles for uma string, converte para array
    if (!is_array($requiredRoles)) {
        $requiredRoles = [$requiredRoles];
    }

    // Verifica se o cargo do usuário permite acesso
    // Administrador pode acessar tudo, ou se o cargo está na lista permitida
    if ($userRole === 'administrador' || in_array($userRole, $requiredRoles)) {
        return true;
    }

    return false;
}

/**
 * Função auxiliar para redirecionar se não autorizado.
 *
 * @param array|string $requiredRoles Os cargos necessários.
 * @param string $redirectUrl URL para redirecionar se não autorizado (padrão: login).
 */
function requirePermission($requiredRoles, $redirectUrl = '../../pages/public/login.php') {
    if (!checkPermission($requiredRoles)) {
        header('Location: ' . $redirectUrl . '?error=' . urlencode('Acesso negado'));
        exit;
    }
}
?>







