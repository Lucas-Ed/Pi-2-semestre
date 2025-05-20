<?php
// conectar ao banco de dados mysqli
require_once __DIR__ . '/../../init.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['idagendamentos'];

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