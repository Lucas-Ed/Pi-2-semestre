<?php
// Script para cadastrar um usuário admin no banco de dados.
// Conexão com o banco
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lava_rapido";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Carrega variáveis do .env
require_once __DIR__ . '/Vendor/autoload.php';
require_once 'init.php';


// Dados do novo admin
$nome = "admin";
$cpfOriginal = '12345678900';
$telefone = '11999999999';
$email = "admin@admin.com";
$senha_plana = "admin123";
$senha_hash = password_hash($senha_plana, PASSWORD_DEFAULT);
$tipo = "admin";
$termos = true;

// Criptografa o CPF
$key = base64_decode($_ENV['CHAVE_CPF']); // se a chave do .env estiver em base64
$cpfCriptografado = openssl_encrypt($cpfOriginal, 'AES-128-ECB', $key);

// Montar o INSERT
$sql = "INSERT INTO usuarios (nome, cpf, telefone, email, senha, tipo, termos)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssi", $nome, $cpfCriptografado, $telefone, $email, $senha_hash, $tipo, $termos);

// Executar
if ($stmt->execute()) {
    echo "Admin cadastrado com sucesso!";
} else {
    echo "Erro: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>