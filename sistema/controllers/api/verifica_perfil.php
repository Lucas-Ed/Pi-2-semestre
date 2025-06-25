<?php
// Rota de api que verifica se o perfil do usuário está completo.
session_start();
require_once __DIR__ . '/../../init.php';

header('Content-Type: application/json');

$response = ['status' => 'incompleto'];

// Verifica se o usuário está logado
if (!isset($_SESSION["idusuarios"])) {
    echo json_encode(['status' => 'nao_logado']);
    exit;
}
// Obtém o ID do usuário da sessão
$userId = $_SESSION["idusuarios"];

// Consulta dados do usuário
$sql = "SELECT cpf, telefone FROM usuarios WHERE idusuarios = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
// Verifica se os dados do usuário foi encontrado.
$cpf = $user['cpf'] ?? '';
$telefone = $user['telefone'] ?? '';

// Consulta dados de endereço
$sqlEndereco = "SELECT rua, numero, bairro, cep FROM enderecos WHERE usuarios_idusuarios = ?";
$stmt = $conn->prepare($sqlEndereco);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$endereco = $result->fetch_assoc();

// Verifica se todos os dados estão preenchidos
if (
    !empty($cpf) && !empty($telefone) &&
    !empty($endereco['rua']) && !empty($endereco['numero']) &&
    !empty($endereco['bairro']) && !empty($endereco['cep'])
) {
    $response['status'] = 'completo';
}

echo json_encode($response);
?>