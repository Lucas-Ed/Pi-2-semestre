<?php
// Controlador que permite o admin deletar um agendamento.
require_once __DIR__ . '/../init.php'; // Conexão via $conn global

header('Content-Type: application/json');
// Debug completo
ini_set('display_errors', 1);
error_reporting(E_ALL);

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID do agendamento não informado']);
    exit;
}

$id = intval($data['id']);

$conn->begin_transaction();

try {
    // Deleta status_ag vinculado
    $stmt1 = $conn->prepare("DELETE FROM status_ag WHERE agendamentos_idagendamentos = ?");
    $stmt1->bind_param("i", $id);
    $stmt1->execute();
    $stmt1->close();

    // Deleta pagamentos vinculados
    $stmt2 = $conn->prepare("DELETE FROM pagamentos WHERE agendamentos_idagendamentos = ?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $stmt2->close();

    // Deleta agendamento
    $stmt3 = $conn->prepare("DELETE FROM agendamentos WHERE idagendamentos = ?");
    $stmt3->bind_param("i", $id);
    $stmt3->execute();
    $stmt3->close();
    // Deleta agendamento do histórico
    $conn->commit();
    // imprime o JSON de sucesso.
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Erro ao deletar: ' . $e->getMessage()]);
}
?>