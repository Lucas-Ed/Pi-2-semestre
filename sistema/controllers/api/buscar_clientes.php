<?php
// Inclui o arquivo de inicialização
require_once __DIR__ . '/../../init.php';

// Verifica se o usuário está autenticado
if (!isset($_SESSION['idusuarios'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

$idUsuario = $_SESSION['idusuarios'];

$busca = trim($_GET['q'] ?? '');

if ($busca === '') {
    echo json_encode(['erro' => 'Digite algo para pesquisar.']);
    exit;
}


// Verifica se o termo de busca contém apenas caracteres válidos, previnindo injeção de SQL e outros problemas de segurança.
if (!preg_match('/^[\p{L}0-9@.\s+-]+$/u', $busca)) {
    echo json_encode([
        'erro' => 'Termo inválido. Só é possível buscar por nome, CPF, telefone ou e-mail.'
    ]);
    exit;
}

// Conecta ao banco de dados
$conn->set_charset("utf8");
// Prepara a consulta para buscar clientes
$stmt = $conn->prepare("
    SELECT nome, telefone, email, cpf
    FROM usuarios
    WHERE tipo = 'cliente' AND (
        nome LIKE CONCAT('%', ?, '%')
        OR cpf = ?
        OR telefone = ?
        OR email = ?
    )
    LIMIT 1
");
// Verifica se a preparação da consulta foi bem-sucedida
$stmt->bind_param("ssss", $busca, $busca, $busca, $busca);
$stmt->execute();
$result = $stmt->get_result();
// Verifica se a consulta retornou algum resultado
if ($cliente = $result->fetch_assoc()) {
    echo json_encode(['sucesso' => true, 'cliente' => $cliente]);
} else {
    echo json_encode(['erro' => 'Cliente não encontrado. Você só pode buscar por nome, CPF, telefone ou e-mail.']);
}
// Fecha a consulta e a conexão
$stmt->close();
$conn->close();
?>