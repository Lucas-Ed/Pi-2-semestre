<?php
require_once __DIR__ . '/../init.php';

// erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


header('Content-Type: application/json'); // <- ESSENCIAL para retornar JSON

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID do agendamento não informado.']);
    exit;
}

$sql = "DELETE FROM agendamentos WHERE idagendamentos = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}
