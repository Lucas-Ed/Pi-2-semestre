<?php
session_start();
header("Content-Type: application/json");

require_once __DIR__ . '/../init.php'; // Inclui o arquivo de inicialização

if (!isset($_SESSION['idusuarios'])) {
    echo json_encode(["success" => false, "message" => "Usuário não autenticado."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data["modelo"], $data["placa"])) {
    $modelo = trim($data["modelo"]);
    $placa = strtoupper(trim($data["placa"]));
    $idUsuario = $_SESSION['idusuarios'];

    // Validação dos campos
    $erros = [];

    if (empty($modelo)) {
        $erros[] = "O campo 'modelo' é obrigatório.";
    }

    if (empty($placa)) {
        $erros[] = "O campo 'placa' é obrigatório.";
    }

    if (!empty($erros)) {
        echo json_encode(["success" => false, "message" => implode(" ", $erros)]);
        exit;
    }

    try {
        $stmt = DB::prepare("INSERT INTO veiculos (modelo, placa, usuarios_idusuarios) VALUES (?, ?, ?)");
        $stmt->execute([$modelo, $placa, $idUsuario]);

        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Erro ao inserir no banco: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Dados inválidos recebidos."]);
}
?>