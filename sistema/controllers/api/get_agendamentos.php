<?php
// conectar ao banco de dados mysqli
require_once __DIR__ . '/../../init.php';


if (!isset($_SESSION['idusuarios'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

$idUsuario = $_SESSION['idusuarios'];

$sql = "SELECT 
    a.idagendamentos,
    a.data_agendamento,
    a.hora_agendamento,
    a.leva_e_tras,
    u.nome AS nome_usuario,
    u.telefone,
    v.modelo AS modelo_carro,
    v.placa
FROM agendamentos a
JOIN usuarios u ON a.usuarios_idusuarios = u.idusuarios
JOIN veiculos v ON a.veiculos_idveiculos = v.idveiculos
ORDER BY a.data_agendamento, a.hora_agendamento";

$result = $conn->query($sql);
$agendamentos = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $agendamentos[] = [
            'id' => $row['idagendamentos'],
            'nome' => $row['nome_usuario'],
            'telefone' => $row['telefone'],
            'data' => $row['data_agendamento'],
            'hora' => $row['hora_agendamento'],
            'veiculo' => [
                'modelo' => $row['modelo_carro'],
                'placa' => $row['placa']
            ],
            'levaETraz' => $row['leva_e_tras']
        ];
    }
} else {
    $agendamentos = []; // Nenhum agendamento encontrado
    echo 'Nenhum agendamento encontrado';
    exit;
}

header('Content-Type: application/json');
echo json_encode($agendamentos);
exit();
?>