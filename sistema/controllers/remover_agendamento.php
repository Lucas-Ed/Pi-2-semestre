<?php
require_once __DIR__ . '/../init.php'; // Inicializa o ambiente e a conexão com o banco de dados

// Previne qualquer saída inesperada
ob_start();
header('Content-Type: application/json');

// Captura erros e warnings como exceções
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// Captura fatal errors
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_COMPILE_ERROR, E_CORE_ERROR])) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro fatal: ' . $error['message']
        ]);
        ob_end_clean();
    }
});

try {
    // Lê o JSON
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'] ?? null;

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID do agendamento não informado.']);
        exit;
    }

    // Buscar dados do agendamento
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
    $status = strtolower(trim($agendamento['executado'] ?? ''));

    // Normalizar status (remover acentos)
    $statusNormalizado = preg_replace(
        ['/[áàãâä]/u', '/[éèêë]/u', '/[íìîï]/u', '/[óòõôö]/u', '/[úùûü]/u', '/ç/u'],
        ['a', 'e', 'i', 'o', 'u', 'c'],
        $status
    );
    // DEBUG: Exibir o status tratado (remova em produção)
    // para ver o log usar php -S, no terminal, e acessar o arquivo de log
    //error_log("Status executado tratado: " . $status);
    // echo json_encode(['debug' => true, 'status' => $statusExecutado]); exit;
    // Debug para verificar o valor do $status
    error_log("Status executado: '$status'"); // Isso deve registrar "concluida" quando o status estiver correto

    $statusBloqueado = ['fila de espera', 'em andamento'];

    $dataHoraAgendada = new DateTime("$dataAgendada $horaAgendada");
    $agora = new DateTime();
    $diffEmSegundos = $dataHoraAgendada->getTimestamp() - $agora->getTimestamp();

    // Verificações de regra
    if (in_array($statusNormalizado, $statusBloqueado)) {
        echo json_encode([
            'success' => false,
            'message' => "Agendamento com status '$status'. Não é possível cancelar."
        ]);
        exit;
    }

    // if ($diffEmSegundos < 3600) {
    //     echo json_encode([
    //         'success' => false,
    //         'message' => 'O cancelamento só é permitido com no mínimo 1 hora de antecedência.'
    //     ]);
    //     exit;
    // }
    // Verifica se o agendamento está no futuro e se o cancelamento é permitido
    if ($dataHoraAgendada > $agora && $diffEmSegundos < 3600) {
        echo json_encode([
            'success' => false,
            'message' => 'O cancelamento só é permitido com no mínimo 1 hora de antecedência.'
        ]);
        exit;
    }


    // Remove status_ag (se existir)
    $sqlDeleteStatus = "DELETE FROM status_ag WHERE agendamentos_idagendamentos = ?";
    $stmtStatus = $conn->prepare($sqlDeleteStatus);
    if ($stmtStatus) {
        $stmtStatus->bind_param("i", $id);
        $stmtStatus->execute();
        $stmtStatus->close();
    }

    // Remove agendamento
    $sqlDelete = "DELETE FROM agendamentos WHERE idagendamentos = ?";
    $stmt = $conn->prepare($sqlDelete);
    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao deletar agendamento: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao preparar DELETE: ' . $conn->error]);
    }
} catch (Throwable $e) {
    http_response_code(500);
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Exceção: ' . $e->getMessage()
    ]);
}
?>