<?php
session_start();
require_once '../config/conecta.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/public/login.php?error=' . urlencode('Método não permitido'));
    exit;
}

$email = trim($_POST['email'] ?? '');
$senha = $_POST['password'] ?? '';

if (empty($email) || empty($senha)) {
    header('Location: ../../pages/public/login.php?error=' . urlencode('Email e senha são obrigatórios'));
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../../pages/public/login.php?error=' . urlencode('Email inválido'));
    exit;
}

// Preparar consulta para buscar a senha hashada
$stmt = $conn->prepare("SELECT id, senha FROM auth WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: ../../pages/public/login.php?error=' . urlencode('Email ou senha incorretos'));
    exit;
}

$row = $result->fetch_assoc();
$hashed_password = $row['senha'];

if (password_verify($senha, $hashed_password)) {
    // Buscar informações completas do usuário na tabela users
    $stmt2 = $conn->prepare("SELECT id, nome_completo, cargo FROM users WHERE email = ?");
    $stmt2->bind_param("s", $email);
    $stmt2->execute();
    $user_result = $stmt2->get_result();
    
    if ($user_result->num_rows === 0) {
        header('Location: ../../pages/public/login.php?error=' . urlencode('Erro interno: usuário não encontrado'));
        exit;
    }
    
    $user_row = $user_result->fetch_assoc();
    
    // Login bem-sucedido - armazenar todas as informações na sessão
    $_SESSION['user_id'] = $user_row['id'];
    $_SESSION['email'] = $email;
    $_SESSION['nome_completo'] = $user_row['nome_completo'];
    $_SESSION['cargo'] = $user_row['cargo'];
    header('Location: ../../pages/private/home.php');
    exit;
} else {
    header('Location: ../../pages/public/login.php?error=' . urlencode('Email ou senha incorretos'));
    exit;
}

$stmt->close();
$stmt2->close();
$conn->close();
?>