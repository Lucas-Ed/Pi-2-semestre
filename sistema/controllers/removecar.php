<?php
require_once __DIR__ . '/../init.php';

ob_start(); // captura qualquer saída inesperada

header("Content-Type: application/json");
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['idusuarios'])) {
    echo json_encode(["success" => false, "message" => "Usuário não autenticado."]);
    exit;
}

$idUsuario = $_SESSION['idusuarios'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Método não permitido"]);
    exit;
}

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

$stmt = $conn->prepare("SELECT COUNT(*) FROM veiculos WHERE idveiculos = ? AND usuarios_idusuarios = ? AND ativo = 1");
$stmt->bind_param("ii", $carId, $idUsuario);
$stmt->execute();
$stmt->bind_result($veiculoCount);
$stmt->fetch();
$stmt->close();

if ($veiculoCount === 0) {
    echo json_encode(["success" => false, "message" => "Veículo não encontrado ou já removido."]);
    exit;
}

// Verifica se há agendamentos não concluídos
$checkStatus = $conn->prepare("
    SELECT COUNT(*) 
    FROM agendamentos a
    JOIN status_ag s ON a.idagendamentos = s.agendamentos_idagendamentos
    WHERE a.veiculos_idveiculos = ? AND s.executado != 'Concluída'
");
$checkStatus->bind_param("i", $carId);
$checkStatus->execute();
$checkStatus->bind_result($pendentes);
$checkStatus->fetch();
$checkStatus->close();

if ($pendentes > 0) {
    echo json_encode([
        "success" => false,
        "message" => "Só é possível remover veículos com todos os agendamentos concluídos."
    ]);
    exit;
}

// Verifica se o veículo tem agendamentos vinculados
// $checkStmt = $conn->prepare("SELECT COUNT(*) FROM agendamentos WHERE veiculos_idveiculos = ?");
// $checkStmt->bind_param("i", $carId);
// $checkStmt->execute();
// $checkStmt->bind_result($agendamentoCount);
// $checkStmt->fetch();
// $checkStmt->close();

// if ($agendamentoCount > 0) {
//     echo json_encode([
//         "success" => false,
//         "message" => "Não é possível remover o veículo pois existem agendamentos vinculados a ele."
//     ]);
//     exit;
// }

// Marca o veículo como inativo (remoção lógica)
$updateStmt = $conn->prepare("UPDATE veiculos SET ativo = 0 WHERE idveiculos = ? AND usuarios_idusuarios = ?");
$updateStmt->bind_param("ii", $carId, $idUsuario);
$updateStmt->execute();

$success = $updateStmt->affected_rows > 0;

$updateStmt->close();
$conn->close();

// Verifica se houve saída inesperada
$output = ob_get_clean();
if (!empty($output)) {
    echo json_encode(["success" => false, "message" => "Erro inesperado: " . trim($output)]);
    exit;
}

// Envia resposta final
if ($success) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Falha ao atualizar o status do veículo."]);
}
exit;
?>



<!--- abaixo somente deleta veículos do usuário autenticado --->
<!-- ?<php

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
?> -->