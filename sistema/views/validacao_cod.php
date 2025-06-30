<?php 
// pagina de validação de código(token) de recuperação de senha , etapa 2.
session_start(); // Inicia a sessão para usar variáveis de sessão

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
    <title>Verificação de Código</title>
    <!-- Bootstrap Latest -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body class="bg-white d-flex flex-column min-vh-100">


    <main class="d-flex flex-grow-1 justify-content-center" style="margin-top: 170px;">
        <div style="width: 350px;">

            <h4 class="text-center fw-bold mb-5" style="color: #444;">Verificação de Token</h4>
            <!-- Mensagem -->
            <small class="text-muted" style="font-size: 13px; margin-bottom: 1rem; display: block;">
                Insira o token de 5 digítos que você recebeu no e-mail.
            </small>

            <!-- FORMULÁRIO 2: Validar código -->
            <form action="../controllers/send_token.php" method="POST" id="formValidarCodigo">
                <!-- Input oculto token Csrf -->
                 <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="mb-3">
                    <div class="d-flex align-items-center"
                         style="border: 2px solid #0097B2; border-radius: 10px; padding: 0 1rem; height: 55px;">
                        <i class="bi bi-key" style="color: #0097B2; font-size: 1.2rem;"></i>
                        <input type="text" name="codigo" class="form-control border-0 shadow-none placeholder-light"
                               placeholder="Código de validação"
                               style="margin-left: 0.75rem; font-size: 1rem; color: #444; height: 100%; line-height: 1.5; padding: 0;"
                               required>
                    </div>
                </div>

                <div class="d-flex justify-content-between gap-2">
                    <a href="./recovery.php" class="btn d-flex align-items-center justify-content-center w-50"
                       style="background-color: #0097B2; color: white; border-radius: 10px; height: 55px; box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.3); font-size: 18px;">
                        Voltar
                    </a>

                    <button type="submit" name="validar_codigo" class="btn d-flex align-items-center justify-content-center w-50"
                            style="background-color: #0097B2; color: white; border-radius: 10px; height: 55px; box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.3); font-size: 18px;">
                        Validar
                    </button>
                </div>
            </form>
        </div>
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
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
</body>
</html>
