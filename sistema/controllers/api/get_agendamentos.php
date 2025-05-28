<?php
// conectar ao banco de dados mysqli
require_once __DIR__ . '/../../init.php';

header('Content-Type: application/json'); //-- referente ao select de horario do modal agendamentos


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
} //--

// $sql = "SELECT 
//     a.idagendamentos,
//     a.data_agendamento,
//     a.hora_agendamento,
//     a.leva_e_tras,
//     u.nome AS nome_usuario,
//     u.telefone,
//     v.modelo AS modelo_carro,
//     v.placa
// FROM agendamentos a
// JOIN usuarios u ON a.usuarios_idusuarios = u.idusuarios
// JOIN veiculos v ON a.veiculos_idveiculos = v.idveiculos
// ORDER BY a.data_agendamento, a.hora_agendamento";

$sql = "SELECT 
    a.idagendamentos,
    a.data_agendamento,
    a.hora_agendamento,
    a.leva_e_tras,
    a.servico,
    u.nome AS nome_usuario,
    u.telefone,
    v.modelo AS modelo_carro,
    v.placa
FROM agendamentos a
JOIN usuarios u ON a.usuarios_idusuarios = u.idusuarios
JOIN veiculos v ON a.veiculos_idveiculos = v.idveiculos
WHERE u.idusuarios = ?
ORDER BY a.data_agendamento, a.hora_agendamento";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();

$agendamentos = [];

while ($row = $result->fetch_assoc()) {
    $agendamentos[] = [
        'idagendamentos' => $row['idagendamentos'],
        'nome' => $row['nome_usuario'],
        'telefone' => $row['telefone'],
        'data' => $row['data_agendamento'],
        'hora' => $row['hora_agendamento'],
        'car_modelo' => $row['modelo_carro'],
        'servico' => $row['servico'],
        'car_placa' => $row['placa'],
        'leva_e_traz' => (bool)$row['leva_e_tras']
    ];
}

echo json_encode($agendamentos);
exit;

// $result = $conn->query($sql);
// $agendamentos = [];

// if ($result && $result->num_rows > 0) {
//     while ($row = $result->fetch_assoc()) {
//         $agendamentos[] = [
//             'id' => $row['idagendamentos'],
//             'nome' => $row['nome_usuario'],
//             'telefone' => $row['telefone'],
//             'data' => $row['data_agendamento'],
//             'hora' => $row['hora_agendamento'],
//             'veiculo' => [
//                 'modelo' => $row['modelo_carro'],
//                 'placa' => $row['placa']
//             ],
//             'levaETraz' => $row['leva_e_tras']
//         ];
//     }
// } else {
//     $agendamentos = []; // Nenhum agendamento encontrado
//     echo 'Nenhum agendamento encontrado';
//     exit;
// }

// header('Content-Type: application/json');
// echo json_encode($agendamentos);
// exit();

// buscar horários já agendados

?>