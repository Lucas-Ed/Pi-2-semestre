<?php
// Arquivo necessario para conectar ao banco de dados MySQL para Login e Cadastro
// require_once '../vendor/autoload.php'; 
require_once __DIR__ . '/../vendor/autoload.php';// Certifique-se de que o autoload do Composer está carregado

// use Dotenv\Dotenv;

// // Carregar variáveis do .env
// $dotenv = Dotenv::createImmutable(__DIR__);
// $dotenv->load();

// Definir as variáveis do banco de dados
$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];

// Criar conexão com MySQL
$conn = new mysqli($host, $user, $pass, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
?>
