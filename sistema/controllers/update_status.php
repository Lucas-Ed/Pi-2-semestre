<?php
require_once __DIR__ . '/../init.php'; // Incluir o arquivo de inicialização
header('Content-Type: application/json'); // Definir o cabeçalho para JSON

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos.']);
    exit;
}

$id = intval($data['id']);
$status = trim($data['status']);

// Verificar se já existe um status para o agendamento
$stmt = $conn->prepare("SELECT * FROM status_ag WHERE agendamentos_idagendamentos = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Se já existe, atualize o status
    $updateStmt = $conn->prepare("
        UPDATE status_ag 
        SET executado = ? 
        WHERE agendamentos_idagendamentos = ?
    ");
    $updateStmt->bind_param("si", $status, $id);

    if ($updateStmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar o status.']);
    }

    $updateStmt->close();
} else {
    // Se não existe, insira o status pela primeira vez
    $insertStmt = $conn->prepare("
        INSERT INTO status_ag (agendamentos_idagendamentos, executado) 
        VALUES (?, ?)
    ");
    $insertStmt->bind_param("is", $id, $status);

    if ($insertStmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao inserir o status.']);
    }

    $insertStmt->close();
}

$stmt->close();
$conn->close();
?>