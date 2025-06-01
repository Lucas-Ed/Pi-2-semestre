<?php
// conectar ao banco de dados mysqli
require_once __DIR__ . '/../../init.php';

// verifica se o usuário está autenticado
if (!isset($_SESSION['idusuarios'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}



$idUsuario = $_SESSION['idusuarios'];

$stmt = $conn->prepare("SELECT
    v.idveiculos AS idveiculos,
    v.modelo     AS modelo,
    v.placa      AS placa,
    v.tipo       AS tipo,
    v.marca      AS marca,
    v.ativo      AS ativo
-- FROM veiculos v
-- WHERE v.usuarios_idusuarios = ?
-- ORDER BY v.modelo
FROM veiculos v
WHERE v.usuarios_idusuarios = ? AND v.ativo = 1
ORDER BY v.modelo");



$stmt->bind_param("i", $idUsuario); // "i" indica que $idUsuario é um inteiro
$stmt->execute();
$result = $stmt->get_result();
$veiculos = [];

if ($result && $result->num_rows > 0) {
    if ($result && $result->num_rows > 0) {
//     while ($row = $result->fetch_assoc()) {
//         // Exibe o conteúdo real do que está vindo
//         echo "<pre>";
//         var_dump($row);
//         echo "</pre>";
//         exit;
//     }
// }

     while ($row = $result->fetch_assoc()) {
         $veiculos[] = [
             'id' => $row['idveiculos'],
             'modelo' => $row['modelo'],
             'placa' => $row['placa'],
             'tipo' => $row['tipo'],
            //  'ativo' => $row['ativo'],
             'marca'  => isset($row['marca']) ? $row['marca'] : 'default',
             //'marca' => array_key_exists('marca', $row) ? $row['marca'] : 'default',
         ];
     }
}
}

header('Content-Type: application/json');
echo json_encode($veiculos);
exit();
?>