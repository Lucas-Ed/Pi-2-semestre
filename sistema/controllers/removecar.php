<?php

require_once __DIR__ . '/../init.php'; // Inicializa o ambiente

header("Content-Type: application/json");
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Inicia a sessão (se ainda não estiver iniciada)
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Verifica se o usuário está autenticado
if (!isset($_SESSION['idusuarios'])) {
    echo json_encode(["success" => false, "message" => "Usuário não autenticado."]);
    exit;
}

$idUsuario = $_SESSION['idusuarios'];

// Apenas aceita POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Método não permitido"]);
    exit;
}

// Lê e valida o corpo da requisição
$input = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "JSON inválido"]);
    exit;
}

if (!isset($input['id']) || !is_numeric($input['id'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "ID inválido"]);
    exit;
}

$carId = intval($input['id']);

// Conexão com banco de dados
require_once __DIR__ . '/../model/db.php';

// Prepara e executa a query de exclusão
$stmt = $conn->prepare("DELETE FROM veiculos WHERE idveiculos = ? AND usuarios_idusuarios = ?");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erro na preparação da query: " . $conn->error]);
    exit;
}

$stmt->bind_param("ii", $carId, $idUsuario);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Veículo não encontrado ou já removido."]);
}

$stmt->close();
$conn->close();
exit;
?>