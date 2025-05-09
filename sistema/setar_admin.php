<?php
// Conexão com o banco
$servername = "localhost";
$username = "root"; // Usuário do banco de dados
$password = "";
$dbname = "lava_rapido";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Dados do novo admin
$nome = "admin";
$cpf = 12345678900;
$telefone = 11999999999;
$email = "admin@seudominio.com";
$senha_plana = "123456"; // Senha padrão que você quer
$senha_hash = password_hash($senha_plana, PASSWORD_DEFAULT); // Criptografar a senha
$tipo = "admin";
$termos = true; // Aceitou os termos (bool)

// Montar o INSERT
$sql = "INSERT INTO usuarios (nome, cpf, telefone, email, senha, tipo, termos)
VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("siisssi", $nome, $cpf, $telefone, $email, $senha_hash, $tipo, $termos);

// Executar
if ($stmt->execute()) {
    echo "Admin cadastrado com sucesso!";
} else {
    echo "Erro: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
