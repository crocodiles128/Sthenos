<?php
// Configurações de conexão com o banco de dados default
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'Sthenos';


$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}


$conn->set_charset("utf8");

?>
