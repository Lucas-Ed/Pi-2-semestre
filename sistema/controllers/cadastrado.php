<?php
$status = $_GET["status"] ?? "";

if ($status === "sucesso") {
    echo "<h2>Cadastro realizado com sucesso!</h2>";

    echo "<a href='../views/index.php'>Voltar</a>";
} else {
    echo "<h2>Erro ao cadastrar.</h2>";
}
?>
