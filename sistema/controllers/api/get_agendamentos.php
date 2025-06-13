<?php
// conectar ao banco de dados mysqli
require_once __DIR__ . '/../../init.php';

header('Content-Type: application/json'); //-- referente ao select de horario do modal agendamentos

// Verifica se o usuário está autenticado
if (!isset($_SESSION['idusuarios'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

$idUsuario = $_SESSION['idusuarios'];

// Verifica se está buscando apenas os horários ocupados em uma data //-- referente ao select de horario do modal agendamentos
if (isset($_GET['data'])) {
    $data = $_GET['data'];

    $stmt = $conn->prepare("SELECT hora_agendamento FROM agendamentos WHERE data_agendamento = ?");
    $stmt->bind_param("s", $data);
    $stmt->execute();
    $result = $stmt->get_result();

    $horariosOcupados = [];
    while ($row = $result->fetch_assoc()) {
        // Formata o horário para HH:MM
        $horariosOcupados[] = substr($row['hora_agendamento'], 0, 5);
    }

    echo json_encode($horariosOcupados);
    exit;
}
// Consulta para buscar todos os agendamentos do usuário autenticado
// $sql = "
// SELECT 
//     a.idagendamentos,
//     a.data_agendamento,
//     a.hora_agendamento,
//     a.leva_e_tras,
//     a.servico,
//     a.preco,  -- Coluna que contém o preço do serviço
//     u.nome AS nome_usuario,
//     u.telefone,
//     v.modelo AS modelo_carro,
//     v.placa,
//     s.executado
// FROM agendamentos a
// JOIN usuarios u ON a.usuarios_idusuarios = u.idusuarios
// JOIN veiculos v ON a.veiculos_idveiculos = v.idveiculos
// LEFT JOIN (
//     SELECT s1.* 
//     FROM status_ag s1
//     INNER JOIN (
//         SELECT agendamentos_idagendamentos, MAX(idstatus_ag) AS max_id
//         FROM status_ag
//         GROUP BY agendamentos_idagendamentos
//     ) s2 ON s1.idstatus_ag = s2.max_id
// ) s ON s.agendamentos_idagendamentos = a.idagendamentos
// WHERE u.idusuarios = ?
// ORDER BY a.data_agendamento, a.hora_agendamento
// ";

// Consulta para buscar apnas os 4 últimos agendamentos do usuário autenticado
$sql = "
SELECT 
    a.idagendamentos,
    a.data_agendamento,
    a.hora_agendamento,
    a.leva_e_tras,
    a.servico,
    a.preco,
    u.nome AS nome_usuario,
    u.telefone,
    u.cpf,
    v.modelo AS modelo_carro,
    v.placa,
    s.executado
FROM agendamentos a
JOIN usuarios u ON a.usuarios_idusuarios = u.idusuarios
JOIN veiculos v ON a.veiculos_idveiculos = v.idveiculos
LEFT JOIN (
    SELECT s1.* 
    FROM status_ag s1
    INNER JOIN (
        SELECT agendamentos_idagendamentos, MAX(idstatus_ag) AS max_id
        FROM status_ag
        GROUP BY agendamentos_idagendamentos
    ) s2 ON s1.idstatus_ag = s2.max_id
) s ON s.agendamentos_idagendamentos = a.idagendamentos
WHERE u.idusuarios = ?
ORDER BY a.data_agendamento DESC, a.hora_agendamento DESC
LIMIT 4
";

// deixar a ordem do mais antigo para o mais novo (ainda limitado aos 4 últimos), você pode:trocar no SELECT
//ORDER BY a.data_agendamento DESC, a.hora_agendamento DESC
//LIMIT 4
// e depois inverter o array com $agendamentos = array_reverse($agendamentos);


// Prepara e executa a consulta
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();

$key = base64_decode($_ENV['CHAVE_CPF'] ?? '');

function descriptografarCPF($cpf_criptografado, $chave) {
    $cipher = "AES-256-CBC";
    $key = hash('sha256', $chave, true);
    $cpf_criptografado = base64_decode($cpf_criptografado);
    $iv = substr($cpf_criptografado, 0, 16);
    $ciphertext = substr($cpf_criptografado, 16);
    return openssl_decrypt($ciphertext, $cipher, $key, OPENSSL_RAW_DATA, $iv);
}


$agendamentos = [];

while ($row = $result->fetch_assoc()) {
    // Descriptografa o CPF
    $cpf_descriptografado = descriptografarCPF($row['cpf'], $key);
    $agendamentos[] = [
        'idagendamentos' => $row['idagendamentos'],
        'nome' => $row['nome_usuario'],
        'telefone' => $row['telefone'],
        'cpf' => $cpf_descriptografado,
        'data' => $row['data_agendamento'],
        'hora' => $row['hora_agendamento'],
        'car_modelo' => $row['modelo_carro'],
        'servico' => $row['servico'],
        'preco_servico' => $row['preco'],
        'car_placa' => $row['placa'],
        'leva_e_traz' => (bool)$row['leva_e_tras'],
        'executado' => $row['executado'] 
    ];
}
//var_dump($agendamentos);
echo json_encode($agendamentos);
exit;
?>