<?php

require_once __DIR__ . '/../init.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log para ver se o PHP está sendo chamado
file_put_contents('log.txt', "Chamou o PHP\n", FILE_APPEND);

header('Content-Type: application/json');

// Verifica se o usuário está logado
if (!isset($_SESSION['idusuarios'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado.']);
    exit;
}

// Captura e decodifica os dados JSON enviados
$data = json_decode(file_get_contents("php://input"), true);

// Salva os dados recebidos para depuração
file_put_contents('debug_agendamento.txt', print_r($data, true));

// Validação básica dos campos obrigatórios
$camposObrigatorios = ['veiculos_idveiculos', 'data_agendamento', 'hora_agendamento', 'servico'];

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

// Prepara e executa o SQL
$sql = "INSERT INTO agendamentos (
    usuarios_idusuarios,
    veiculos_idveiculos,
    data_agendamento,
    hora_agendamento,
    leva_e_tras,
    servico
) VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("iissis", $usuario, $veiculo, $data_agendamento, $hora_agendamento, $leva_e_tras, $servico);

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