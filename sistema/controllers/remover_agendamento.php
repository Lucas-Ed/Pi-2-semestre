<?php
require_once __DIR__ . '/../init.php';

// Debug completo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


header('Content-Type: application/json');

// Receber dados JSON do frontend
$data = json_decode(file_get_contents("php://input"), true);
// A variável 'id' deve ser lida a partir do JSON (corpo da requisição POST)
$id = $data['id'] ?? null; // Tenta acessar o parâmetro 'id' no corpo da requisição

// Debug para verificar se o ID foi capturado corretamente
error_log("ID recebido: " . print_r($id, true));  // Log do ID recebido

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID do agendamento não informado.']);
    exit;
}

// Buscar data, hora e status do agendamento
$sql = "
    SELECT 
        a.data_agendamento, 
        a.hora_agendamento,
        sa.executado
    FROM agendamentos a
    LEFT JOIN status_ag sa ON sa.agendamentos_idagendamentos = a.idagendamentos
    WHERE a.idagendamentos = ?
";
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

// Debug para verificar os dados do agendamento recuperado
error_log("Agendamento recuperado: " . print_r($agendamento, true));

if (!$agendamento) {
    echo json_encode(['success' => false, 'message' => 'Agendamento não encontrado.']);
    exit;
}

$dataAgendada = $agendamento['data_agendamento'];
$horaAgendada = $agendamento['hora_agendamento'];
$statusExecutado = strtolower(trim($agendamento['executado'] ?? ''));

// Remover acentos e caracteres invisíveis
$statusExecutado = preg_replace(
    ['/[áàãâä]/u', '/[éèêë]/u', '/[íìîï]/u', '/[óòõôö]/u', '/[úùûü]/u', '/ç/u'],
    ['a', 'e', 'i', 'o', 'u', 'c'],
    $statusExecutado
);

// DEBUG: Exibir o status tratado (remova em produção)
// para ver o log usar php -S, no terminal, e acessar o arquivo de log
//error_log("Status executado tratado: " . $statusExecutado);
// echo json_encode(['debug' => true, 'statusExecutado' => $statusExecutado]); exit;

// Debug para verificar o valor do statusExecutado
error_log("Status executado: '$statusExecutado'"); // Isso deve registrar "concluida" quando o status estiver correto

// Se o status for "concluída", permite deletar mesmo fora das regras de data/hora
if (mb_strtolower(trim($statusExecutado)) === 'concluida') {
    // Remover entrada em status_ag primeiro
    $sqlDeleteStatus = "DELETE FROM status_ag WHERE agendamentos_idagendamentos = ?";
    $stmtStatus = $conn->prepare($sqlDeleteStatus);
    if ($stmtStatus) {
        $stmtStatus->bind_param("i", $id);
        $stmtStatus->execute();
        $stmtStatus->close();
    } else {
        error_log("Erro ao excluir status_ag: " . $conn->error);
    }

    // Agora remover o agendamento
    $sqlDelete = "DELETE FROM agendamentos WHERE idagendamentos = ?";
    $stmt = $conn->prepare($sqlDelete);

    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            $errorMessage = $stmt->error;
            error_log("Erro ao deletar agendamento: $errorMessage"); // Log do erro
            echo json_encode(['success' => false, 'message' => 'Erro ao deletar agendamento: ' . $errorMessage]);
        }
        $stmt->close();
    } else {
        $errorMessage = $conn->error;
        error_log("Erro ao preparar DELETE: $errorMessage"); // Log do erro
        echo json_encode(['success' => false, 'message' => 'Erro ao preparar DELETE: ' . $errorMessage]);
    }
    exit;
}
// Regras normais para outros agendamentos

$dataHoraAgendada = new DateTime("$dataAgendada $horaAgendada");
$agora = new DateTime();

// Regra 1: só permite cancelamento a partir do dia seguinte
if ($dataHoraAgendada->format('Y-m-d') <= $agora->format('Y-m-d')) {
    echo json_encode([
        'success' => false,
        'message' => 'Você só pode cancelar agendamentos com data futura (a partir de amanhã).'
    ]);
    exit;
}

// Regra 2: pelo menos 1 hora de antecedência
$diffEmSegundos = $dataHoraAgendada->getTimestamp() - $agora->getTimestamp();
if ($diffEmSegundos < 3600) {
    echo json_encode([
        'success' => false,
        'message' => 'O cancelamento só é permitido com no mínimo 1 hora de antecedência.'
    ]);
    exit;
}

// Se passou nas regras, pode deletar
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
?>