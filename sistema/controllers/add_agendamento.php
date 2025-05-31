<?php

require_once __DIR__ . '/../init.php'; // Caminho para o arquivo de inicialização e conexão com o banco de dados

// Configurações de exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Log simples para confirmar que o script foi chamado
//file_put_contents('log.txt', "Chamou o PHP\n", FILE_APPEND);

// Verifica se o usuário está logado
if (!isset($_SESSION['idusuarios'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado.']);
    exit;
}

// Captura e decodifica os dados JSON enviados
$data = json_decode(file_get_contents("php://input"), true);

// log para verificar os dados recebidos
//file_put_contents('debug_agendamento.txt', print_r($data, true)); 


// Validação básica dos campos obrigatórios
$camposObrigatorios = ['veiculos_idveiculos', 'data_agendamento', 'hora_agendamento', 'servico', 'preco'];
foreach ($camposObrigatorios as $campo) {
    if (!isset($data[$campo]) || trim($data[$campo]) === '') {
        echo json_encode(['success' => false, 'message' => "Campo obrigatório ausente: $campo"]);
        exit;
    }
}

// Prepara os dados
$usuario = $_SESSION['idusuarios'];
$veiculo = (int) $data['veiculos_idveiculos'];
$data_agendamento = $data['data_agendamento'];
$hora_agendamento = $data['hora_agendamento'];
$leva_e_tras = !empty($data['leva_e_tras']) ? 1 : 0;
$servico = $data['servico'];
$preco = (float) $data['preco'];

// Verifica se a data e hora do agendamento são válidas (futuro)
try {
    $agendamentoDateTime = new DateTime($data_agendamento . ' ' . $hora_agendamento);
    $agora = new DateTime();

    if ($agendamentoDateTime < $agora) {
        echo json_encode(['success' => false, 'message' => 'Não é possível agendar para uma data ou hora passada.']);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao processar data/hora.']);
    exit;
}

// Verifica se já existe agendamento para a mesma data e hora
$verifica_sql = "SELECT idagendamentos FROM agendamentos WHERE data_agendamento = ? AND hora_agendamento = ?";
$verifica_stmt = $conn->prepare($verifica_sql);
if ($verifica_stmt) {
    $verifica_stmt->bind_param("ss", $data_agendamento, $hora_agendamento);
    $verifica_stmt->execute();
    $verifica_stmt->store_result();

    if ($verifica_stmt->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Este horário já está reservado. Por favor, escolha outro.'
        ]);
        exit;
    }

    $verifica_stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao verificar disponibilidade: ' . $conn->error]);
    exit;
}

// Insere o novo agendamento
$sql = "INSERT INTO agendamentos (
    usuarios_idusuarios,
    veiculos_idveiculos,
    data_agendamento,
    hora_agendamento,
    leva_e_tras,
    servico,
    preco
) VALUES (?, ?, ?, ?, ?, ?, ?)";


$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("iissisd", $usuario, $veiculo, $data_agendamento, $hora_agendamento, $leva_e_tras, $servico, $preco);


    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao salvar agendamento: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Erro na preparação da query: ' . $conn->error]);
}
?>