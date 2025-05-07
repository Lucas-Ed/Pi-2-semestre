<?php
// Inicia a sessão
session_start();
// carrega o init.php
// Este arquivo deve conter a configuração do autoload e outras configurações globais
require_once __DIR__ . '/../init.php'; // Conecta via mysqli

// error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");
// require_once __DIR__ . '/../../model/config.php'; // Conecta via mysqli

// Verifica se o usuário está autenticado
$idUsuario = $_SESSION['idusuarios'] ?? 0;
if ($idUsuario === 0) {
    echo json_encode(["success" => false, "message" => "Usuário não autenticado."]);
    exit;
}
// Recebe os dados JSON do frontend
$data = json_decode(file_get_contents("php://input"), true);

$modelo = trim($data['modelo'] ?? '');
$placa = strtoupper(trim($data['placa'] ?? ''));
// $idUsuario = intval($data['idusuarios'] ?? 0);

// Validações de segurança
if (empty($modelo) || empty($placa) || $idUsuario === 0) {
    echo json_encode(["success" => false, "message" => "Dados inválidos recebidos no salvamento."]);
    exit;
}

// Usa prepared statement com mysqli
$stmt = $conn->prepare("INSERT INTO veiculos (modelo, placa, usuarios_idusuarios) VALUES (?, ?, ?)");
if ($stmt) {
    $stmt->bind_param("ssi", $modelo, $placa, $idUsuario);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro ao salvar no banco: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Erro na preparação da query: " . $conn->error]);
}

$conn->close();
?>