<?php
session_start();
// Verifica se o CSRF token está presente e é válido.
// if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
//     die('CSRF token inválido.');
// }

require_once __DIR__ . '/../init.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json");

$idUsuario = $_SESSION['idusuarios'] ?? 0;
if ($idUsuario === 0) {
    echo json_encode(["success" => false, "message" => "Usuário não autenticado."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$tipo = trim($data['tipo'] ?? '');
$marca = trim($data['marca'] ?? '');
$modelo = trim($data['modelo'] ?? '');
$placa = strtoupper(trim($data['placa'] ?? ''));

if (empty($tipo) || empty($marca) || empty($modelo) || empty($placa)) {
    echo json_encode(["success" => false, "message" => "Todos os campos são obrigatórios."]);
    exit;
}

// Adicionando valor fixo para 'ativo'
$ativo = 1;

$stmt = $conn->prepare("INSERT INTO veiculos (tipo, marca, modelo, placa, usuarios_idusuarios, ativo) VALUES (?, ?, ?, ?, ?, ?)");
if ($stmt) {
    $stmt->bind_param("ssssii", $tipo, $marca, $modelo, $placa, $idUsuario, $ativo);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Veículo salvo com sucesso."]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro ao salvar: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Erro na query: " . $conn->error]);
}

$conn->close();
?>