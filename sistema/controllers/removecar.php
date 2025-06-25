<?php
// Controlador para remoção(inativar) veículos do usuário autenticado.
require_once __DIR__ . '/../init.php'; // Inicializa o ambiente e a conexão com o banco de dados

ob_start(); // captura qualquer saída inesperada
// Define o tipo de conteúdo como JSON
header("Content-Type: application/json");
// Habilita exibição de erros para depuração
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
// Verifica se o usuário está autenticado
if (!isset($_SESSION['idusuarios'])) {
    echo json_encode(["success" => false, "message" => "Usuário não autenticado."]);
    exit;
}
// Obtém o ID do usuário da sessão
$idUsuario = $_SESSION['idusuarios'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Método não permitido"]);
    exit;
}
// Lê e valida o corpo da requisição
$input = json_decode(file_get_contents('php://input'), true);
// Verifica se o JSON foi decodificado corretamente
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "JSON inválido"]);
    exit;
}
// Verifica se o ID do veículo foi fornecido e é um número válido.
if (!isset($input['id']) || !is_numeric($input['id'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "ID inválido"]);
    exit;
}
// Converte o ID do veículo para inteiro
$carId = intval($input['id']);
// Conexão com o banco de dados, e executa a consulta para verificar se o veículo existe e pertence ao usuário autenticado.
$stmt = $conn->prepare("SELECT COUNT(*) FROM veiculos WHERE idveiculos = ? AND usuarios_idusuarios = ? AND ativo = 1");
$stmt->bind_param("ii", $carId, $idUsuario);
$stmt->execute();
$stmt->bind_result($veiculoCount);
$stmt->fetch();
$stmt->close();
// Se o veículo não existir ou já tiver sido removido, retorna uma mensagem de erro.
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
// Se houver agendamentos pendentes, retorna uma mensagem de erro.
if ($pendentes > 0) {
    echo json_encode([
        "success" => false,
        "message" => "Só é possível remover veículos com todos os agendamentos concluídos.\nOu remova o agendamento para remover o veículo primeiro."
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
// Verifica se a atualização foi bem-sucedida.
$success = $updateStmt->affected_rows > 0;
// Se não houver linhas afetadas, significa que o veículo não foi encontrado ou já estava inativo.
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