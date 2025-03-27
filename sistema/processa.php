<?php
require 'vendor/autoload.php'; // Carrega as bibliotecas do Composer

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Conectar ao banco de dados
$servername = $_ENV["DB_HOST"];
$username = $_ENV["DB_USER"];
$password = $_ENV["DB_PASS"];
$database = $_ENV["DB_NAME"];

$conn = new mysqli($servername, $username, $password, $database);

// Verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Captura os dados do formulário
$username = $_POST["name"];
$password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Criptografando a senha

// Inserindo os dados na tabela "user"
$sql = "INSERT INTO users (username, password) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password);

if ($stmt->execute()) {
    header("Location: cadastrado.php?status=sucesso");
    exit();
} else {
    echo "Erro ao cadastrar: " . $stmt->error;
}

// Fechar conexão
$stmt->close();
$conn->close();
?>
