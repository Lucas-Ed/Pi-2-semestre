<?php 
session_start();
header("Content-Type: application/json");

require_once __DIR__ . '/../init.php';

if (!isset($_SESSION['idusuarios'])) {
    echo json_encode(["success" => false, "message" => "Usuário não autenticado."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$tipo = trim($data["tipo"] ?? '');
$marca = trim($data["marca"] ?? '');
$modelo = trim($data["modelo"] ?? '');
$placa = strtoupper(trim($data["placa"] ?? ''));
$idUsuario = $_SESSION['idusuarios'];

$erros = [];

if (empty($tipo)) $erros[] = "O campo 'tipo' é obrigatório.";
if (empty($marca)) $erros[] = "O campo 'marca' é obrigatório.";
if (empty($modelo)) $erros[] = "O campo 'modelo' é obrigatório.";
if (empty($placa)) $erros[] = "O campo 'placa' é obrigatório.";

if (!empty($erros)) {
    echo json_encode(["success" => false, "message" => implode(" ", $erros)]);
    exit;
}

try {
    $stmt = DB::prepare("INSERT INTO veiculos (tipo, marca, modelo, placa, usuarios_idusuarios) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$tipo, $marca, $modelo, $placa, $idUsuario]);

    echo json_encode(["success" => true, "message" => "Veículo salvo com sucesso."]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Erro ao inserir no banco: " . $e->getMessage()]);
}
?>