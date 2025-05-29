<?php
require_once __DIR__ . '/../init.php';// Incluir o arquivo de inicialização
header('Content-Type: application/json'); // Definir o cabeçalho para JSON

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos.']);
    exit;
}

$id = intval($data['id']);
$status = trim($data['status']);

$stmt = $conn->prepare("
    INSERT INTO status_ag (agendamentos_idagendamentos, executado) 
    VALUES (?, ?)
    ON DUPLICATE KEY UPDATE executado = VALUES(executado)
");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erro na preparação da consulta.']);
    exit;
}

$stmt->bind_param("is", $id, $status);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar o status.']);
}

$stmt->close();
$conn->close();
?>