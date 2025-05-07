<?php

// conecta ao banco de dados
require_once __DIR__ . '/../../init.php';


$data = json_decode(file_get_contents("php://input"), true);

$usuario = $data['usuarios_idusuarios'];
$veiculo = $data['veiculos_idveiculos'];
$data_agendamento = $data['data_agendamento'];
$hora_agendamento = $data['hora_agendamento'];
$leva_e_tras = $data['leva_e_tras'] ? 1 : 0;

$sql = "INSERT INTO agendamentos (
    usuarios_idusuarios,
    veiculos_idveiculos,
    data_agendamento,
    hora_agendamento,
    leva_e_tras
) VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("iissi", $usuario, $veiculo, $data_agendamento, $hora_agendamento, $leva_e_tras);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}
