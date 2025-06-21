<?php 
session_start();

// Gera um token CSRF se não existir
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificação de Identidade</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" href="../public/uploads/img/favicon.svg" type="image/svg+xml">
</head>

<body class="bg-white d-flex flex-column min-vh-100 position-relative">

    <header>

    </header>

    <main class="d-flex flex-grow-1 justify-content-center" style="margin-top: 200px;">
        <div style="width: 380px;">

            <h4 class="text-center fw-bold mb-5" style="color: #444;">Verificação de Identidade</h4>

            <p class="text-muted mb-2 small">Insiria seu e-mail para verificação</p>

            <!-- FORMULÁRIO 1: Enviar e-mail -->
            <form action="../controllers/send_token.php" method="POST" style="margin-bottom: 1rem;">
                <!-- Input oculto token Csrf -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="d-flex align-items-center flex-grow-1"
                            style="border: 2px solid #00a3c7; border-radius: 10px; padding: 0 1rem; height: 55px;">
                            <i class="bi bi-envelope-fill me-2" style="color: #00a3c7; font-size: 1.2rem;"></i>
                            <input type="email" name="email" class="form-control border-0 shadow-none placeholder-light"
                                placeholder="Insira seu e-mail"
                                style="font-size: 1rem; color: #444; height: 100%; line-height: 1.5; padding: 0;"
                                required>
                        </div>

                        <button type="submit" name="enviar_email"
                            class="btn d-flex align-items-center justify-content-center"
                            style="width: 55px; height: 55px; background-color: #0097B2; color: white; border-radius: 10px; border: none; box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.3);">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="./login.php" class="btn d-flex align-items-center justify-content-center w-100 border-0"
                            style="background-color: #0097B2; color: white; border-radius: 10px; height: 55px; box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.3); font-size: 18px;">
                            Voltar
                        </a>
                    </div>
            </form>
    </main>

    <!-- Rodapé -->
    <footer class="text-white text-center py-5 d-flex flex-column align-items-center justify-content-center fixed-bottom"
        style="background-color: #0097B2; border-top-left-radius: 20px; border-top-right-radius: 20px; height: 130px; font-size: 1.1rem; box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.3);">
        <small>
            &copy; <?= date('Y') ?> Embelezamento Automotivo
            <br>
            Todos os direitos reservados
        </small>
    </footer>

    <!-- lib SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <!-- Script para exibir mensagens de erro ou sucesso -->
    <script src="../public/js/recuperacao_alerts.js?v=<?= time() ?>" defer></script>
</body>

</html>