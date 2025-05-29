<?php
require_once __DIR__ . '/../init.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID do agendamento não informado.']);
    exit;
}

// Buscar data e hora do agendamento
$sql = "SELECT data_agendamento, hora_agendamento FROM agendamentos WHERE idagendamentos = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erro ao preparar consulta: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$agendamento = $result->fetch_assoc();
$stmt->close();

if (!$agendamento) {
    echo json_encode(['success' => false, 'message' => 'Agendamento não encontrado.']);
    exit;
}

$dataAgendada = $agendamento['data_agendamento'];
$horaAgendada = $agendamento['hora_agendamento'];

// Criar objeto DateTime do agendamento
$dataHoraAgendada = new DateTime("$dataAgendada $horaAgendada");
$agora = new DateTime();

// Regra 1: Só pode cancelar se for dia seguinte ou posterior
if ($dataHoraAgendada->format('Y-m-d') <= $agora->format('Y-m-d')) {
    echo json_encode([
        'success' => false,
        'message' => 'Você só pode cancelar agendamentos com data futura (a partir de amanhã).'
    ]);
    exit;
}

// Regra 2: Cancelamento precisa ter no mínimo 1 hora de antecedência
$diffEmSegundos = $dataHoraAgendada->getTimestamp() - $agora->getTimestamp();
if ($diffEmSegundos < 3600) {
    echo json_encode([
        'success' => false,
        'message' => 'O cancelamento só é permitido com no mínimo 1 hora de antecedência.'
    ]);
    exit;
}

// Executar remoção
$sqlDelete = "DELETE FROM agendamentos WHERE idagendamentos = ?";
$stmt = $conn->prepare($sqlDelete);

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
