<?php
require_once __DIR__ . '/../init.php';

// Roteamento usando o caminho da URL
$caminho = $_SERVER['REQUEST_URI'];
$caminho = trim($caminho, '/'); // Remove barras iniciais e finais
$caminho = explode('/', $caminho);
$pagina = $caminho[0] ?? 'index';

 if ($pagina === 'logout') {
    require_once BASE_PATH . './controllers/logout.php';
} elseif ($pagina === 'welcome') {
     require_once BASE_PATH . '/views/welcome.php';
}else {
    require_once BASE_PATH . '/views/index.php'; // Página inicial padrão
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <title>Document</title>
</head>
<body>
    <h1>Home</h1>
    <br>
    <a href="./login.php" class="btn btn-primary">Login</a>
    <br><br>    
    <a href="./cadastro.php" class="btn btn-primary">Cadastre-se</a>
</body>
</html>