<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../views/index.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="pt_BR">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
    </style>
</head>
<body>
    <br> 
    <br>
    <div class="page-header">
        <h1>Olá, <b><?php
                        $nomeCompleto = htmlspecialchars($_SESSION["nome"]);
                        $nomes = explode(" ", $nomeCompleto);

                        if (count($nomes) >= 2) {
                            $primeiroSegundoNome = $nomes[0] . " " . $nomes[1];
                            echo $primeiroSegundoNome;
                        } else {
                            // Caso o nome tenha menos de dois termos, exibe o nome completo
                            echo $nomeCompleto;
                        }
?>
        <br>
        </b>Bem vindo ao site !!</h1>
    </div>
    <p>
        
        <!-- <a href="cadastro.php" class="btn btn-primary">Cadastre-se</a>
        <br><br> -->
        
        <a href="../controllers/logout.php" class="btn btn-danger">Sair da conta</a>
    </p>
</body>
</html>