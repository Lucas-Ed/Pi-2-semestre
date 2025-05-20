<?php
require_once __DIR__ . '/../init.php';
// Chama o componente de cabeçalho da página
require_once __DIR__ . '/components/header.php'; 

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
<title>Home</title>

<!-- CSS personalizado -->
<link rel="stylesheet" href="../public/css/index.css">

<section class="d-flex flex-column min-vh-100 bg-light">

<!-- Header com botão de login -->
    <header  class="w-100 d-flex justify-content-end px-5 pt-5">
        <a href="./login.php" class="btn btn-primary px-5" style="background-color: #0097B2; border: none;">Login</a>
    </header>

    <!-- Conteúdo principal centralizado -->
    <main class="container text-center my-5 d-flex flex-column align-items-center justify-content-center flex-grow-1">
        <!-- Logo -->
        <img src="../public/uploads/img/logo_empresa.svg" alt="Logo Empresa" class="img-fluid mx-auto mb-4"
            style="max-width: 500px;">

        <!-- Texto -->
        <p class="text-secondary text-center mx-auto" style="max-width: 1000px;">
            Na unidade de Araras, oferecemos serviços de embelezamento automotivo, como polimento, cristalização
            de pintura, higienização e vitrificação de vidros. Usamos produtos premium e técnicas avançadas para
            garantir brilho e proteção. Agende seu atendimento facilmente pelo nosso sistema de agendamento online.
        </p>

        <!-- Carrossel de imagens -->
        <div class="carrossel-container">
  <div class="carrossel-track">
    <img src="../public/uploads/img/carrossel/car1.jpg" alt="Imagem 1">
    <img src="../public/uploads/img/carrossel/car2.jpg" alt="Imagem 2">
    <img src="../public/uploads/img/carrossel/car3.jpg" alt="Imagem 3">
    <img src="../public/uploads/img/carrossel/car4.jpg" alt="Imagem 4">
    <img src="../public/uploads/img/carrossel/car5.jpg" alt="Imagem 5">
    <img src="../public/uploads/img/carrossel/car6.jpg" alt="Imagem 6">
    <img src="../public/uploads/img/carrossel/car7.jpg" alt="Imagem 7">
    <img src="../public/uploads/img/carrossel/car8.jpg" alt="Imagem 8">
    <img src="../public/uploads/img/carrossel/car9.jpg" alt="Imagem 9">
    <!-- Repetir para efeito de loop -->
    <img src="../public/uploads/img/carrossel/car1.jpg" alt="Imagem 1">
    <img src="../public/uploads/img/carrossel/car2.jpg" alt="Imagem 2">
    <img src="../public/uploads/img/carrossel/car3.jpg" alt="Imagem 3">
  </div>
</div>
</section>


<?php require_once __DIR__ . '/components/footer.php'; ?> 