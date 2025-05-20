<?php
require_once __DIR__ . '/../init.php';

header("Content-Type: application/json");
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Apenas aceita POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Método não permitido"]);
    exit;
}

// Lê o corpo da requisição
$input = json_decode(file_get_contents('php://input'), true);

// Verificação de JSON malformado
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "JSON inválido"]);
    exit;
}

// Validação de ID
if (!isset($input['id']) || !is_numeric($input['id'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "ID inválido"]);
    exit;
}

$carId = intval($input['id']);

// Conexão ao banco
require_once __DIR__ . '/../model/db.php';

// Executa DELETE
$stmt = $conn->prepare("DELETE FROM veiculos WHERE idveiculos = ?");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erro na preparação da query: " . $conn->error]);
    exit;
}

$stmt->bind_param("i", $carId);
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