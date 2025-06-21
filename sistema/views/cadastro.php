<?php
require_once '../init.php'; // <-- Inicia sessão
//  captura os dados do formulário e os armazena na sessão.
$form_data = $_SESSION['form_data'] ?? [];
$form_errors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['form_data'], $_SESSION['form_errors']);

// Exporta para JavaScript
echo "<script>window.formErrors = " . json_encode($form_errors) . ";</script>";

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>

<title>Cadastro</title>
<!-- CSS personalizado -->
<link rel="stylesheet" href="../public/css/cadastrar_se/cadastro.css">

<section class="bg-white d-flex flex-column min-vh-100">
    <!-- Mensagem de erro -->
    <!-- <?php if (!empty($form_errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($form_errors as $erro): ?>
                <li><?= htmlspecialchars($erro) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?> -->

    <!DOCTYPE html>
    <html lang="pt-br">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cadastro</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Icons bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
        <!-- Favicon -->
        <link rel="icon" href="../public/uploads/img/favicon.svg" type="image/svg+xml">
    </head>

    <body>
        <main class="container my-auto px-3" style="max-width: 400px;">
            <h4 class="text-center fw-bold mb-5 mt-4" style="color: #444">Cadastro</h4>

            <form method="POST" action="../controllers/processa.php">
                <!-- Token CSRF -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <!-- Nome -->
                <div class="mb-2">
                    <div class="d-flex align-items-center"
                        style="border: 2px solid #0097B2; border-radius: 10px; padding: 0 1rem; height: 55px;">
                        <i class="bi bi-person" style="color: #0097B2; font-size: 1.2rem;"></i>
                        <input type="text" name="nome" class="form-control border-0 shadow-none placeholder-light"
                            placeholder="*Nome completo" value="<?= htmlspecialchars($form_data['nome'] ?? '') ?>"
                            style="margin-left: 0.75rem; font-size: 1rem; color: #444; height: 100%; line-height: 1.5; padding: 0;"
                            required>
                    </div>
                </div>



                <!-- Email -->
                <div class="mb-2">
                    <div class="d-flex align-items-center"
                        style="border: 2px solid #0097B2; border-radius: 10px; padding: 0 1rem; height: 55px;">
                        <i class="bi bi-envelope" style="color: #0097B2; font-size: 1.2rem;"></i>
                        <input type="email" name="email" class="form-control border-0 shadow-none placeholder-light"
                            placeholder="*Endereço de e-mail" value="<?= htmlspecialchars($form_data['email'] ?? '') ?>"
                            style="margin-left: 0.75rem; font-size: 1rem; color: #444; height: 100%; line-height: 1.5; padding: 0;"
                            required>
                    </div>
                </div>


                <!-- Senha -->
                <div class="mb-2">
                    <div class="d-flex align-items-center"
                        style="border: 2px solid #0097B2; border-radius: 10px; padding: 0 1rem; height: 55px;">
                        <i class="bi bi-lock" style="color: #0097B2; font-size: 1.2rem;"></i>
                        <input type="password" name="senha" id="senha"
                            class="form-control border-0 shadow-none placeholder-light"
                            placeholder="*Nova senha-(8 caracteres, letras e números)"
                            style="margin-left: 0.75rem; font-size: 1rem; color: #444; height: 100%; line-height: 1.5; padding: 0;"
                            required>
                    </div>
                </div>

                <!-- Confirmar Senha -->
                <div class="mb-3">
                    <div class="d-flex align-items-center"
                        style="border: 2px solid #0097B2; border-radius: 10px; padding: 0 1rem; height: 55px;">
                        <i class="bi bi-lock" style="color: #0097B2; font-size: 1.2rem;"></i>
                        <input type="password" name="confirma_senha" id="confirma_senha"
                            class="form-control border-0 shadow-none placeholder-light"
                            placeholder="*Confirmar nova senha -(8 caracteres, letras e números)"
                            style="margin-left: 0.75rem; font-size: 1rem; color: #444; height: 100%; line-height: 1.5; padding: 0;"
                            required>
                    </div>
                    <!-- Small de confirmar senha em raltime -->
                    <small id="senha-feedback" class=" d-block mt-1" style="font-size: 0.9rem;"></small>
                </div>

                <!-- Checkboxes -->
                <div class="form-check mb-4">
                    <input class="form-check-input" style="border: 2px solid #0097B2;" type="checkbox" name="termos"
                        id="termos" <?= isset($form_data['termos']) ? 'checked' : '' ?> required>
                    <label class="form-check-label" for="termos">
                        Aceito os
                        <a href="termos.html" target="_blank" class="text-decoration-none" style=" color: #0097B2;">Termos de Uso</a>
                    </label>

                </div>

                <!-- <div class="form-check mb-4">
                <input class="form-check-input" style="border: 2px solid #00a3c7;" type="checkbox" id="privacidade"
                    required>
                <label class="form-check-label" for="privacidade">Li e concordo com a Política de Privacidade</label>
            </div> -->

                <!-- Botões -->
                <div class="d-flex justify-content-between">
                    <a href="../views/login.php" class="btn d-flex align-items-center justify-content-center w-50 me-2"
                        style="background-color: #0097B2; color: white; border-radius: 10px; height: 55px; box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.5);">
                        Voltar
                    </a>

                    <button type="submit" class="btn d-flex align-items-center justify-content-center w-50 ms-2"
                        style="background-color: #0097B2; color: white; border-radius: 10px; height: 55px; box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.5);">
                        Cadastrar
                    </button>
                </div>

            </form>
        </main>

            <!-- Rodapé -->
    <footer class="text-white text-center py-5 d-flex flex-column align-items-center justify-content-center fixed-bottom"
        style=" background-color: #0097B2; border-top-left-radius: 20px; border-top-right-radius: 20px; height: 130px; font-size: 1.1rem; box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.3);">
        <small>
            &copy; <?= date('Y') ?> Embelezamento Automotivo
            <br>
            Todos os direitos reservados
        </small>
    </footer>

        <!-- lib sweetalert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- Lib Inputmask -->
        <script src="https://unpkg.com/imask"></script>
        <!-- // alerts de validação -->
        <script src="../public/js/val_cads.js?v=<?= time() ?>" defer></script>

    </body>

    </html>