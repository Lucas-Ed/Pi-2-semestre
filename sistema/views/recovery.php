<?php 
include_once '../controllers/send_token.php';
require_once __DIR__ . '/components/header.php'; 
?>
<title>Validação de Identidade</title>
<link rel="stylesheet" href="../public/css/recuperar_senha/confirmar_identidade.css">

<section class="bg-white d-flex flex-column min-vh-100 position-relative">
    <main class="d-flex flex-grow-1 align-items-center justify-content-center px-3">
        <div style="width: 100%; max-width: 400px;">
            <h4 class="text-center fw-bold mb-5" style="color: #444;">Validação de Identidade</h4>

            <!-- FORMULÁRIO 1: Enviar e-mail -->
            <form action="../controllers/send_token.php" method="POST" style="margin-bottom: 1rem;">
                <div class="mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="d-flex align-items-center flex-grow-1"
                            style="border: 2px solid #00a3c7; border-radius: 10px; padding: 0 1rem; height: 55px;">
                            <i class="bi bi-envelope-fill me-2" style="color: #00a3c7; font-size: 1.2rem;"></i>
                            <input type="email" name="email" class="form-control border-0 shadow-none placeholder-light"
                                placeholder="Endereço de e-mail"
                                style="font-size: 1rem; color: #444; height: 100%; line-height: 1.5; padding: 0;"
                                required>
                        </div>

                        <button type="submit" name="enviar_email"
                            class="btn d-flex align-items-center justify-content-center"
                            style="width: 55px; height: 55px; background-color: #00a3c7; color: white; border-radius: 10px; border: none;">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </div>
                </div>
            </form>


            <!-- FORMULÁRIO 2: Validar código -->
            <form action="../controllers/send_token.php" method="POST" id="formValidarCodigo">
                <div class="mb-3">
                    <div class="d-flex align-items-center"
                         style="border: 2px solid #00a3c7; border-radius: 10px; padding: 0 1rem; height: 55px;">
                        <i class="bi bi-key" style="color: #00a3c7; font-size: 1.2rem;"></i>
                        <input type="text" name="codigo" class="form-control border-0 shadow-none placeholder-light"
                               placeholder="Código de validação"
                               style="margin-left: 0.75rem; font-size: 1rem; color: #444; height: 100%; line-height: 1.5; padding: 0;"
                               required>
                    </div>
                </div>



                <div class="d-flex justify-content-between">
                    <a href="./login.php" class="btn d-flex align-items-center justify-content-center w-50 me-2"
                       style="background-color: #00a3c7; color: white; border-radius: 10px; height: 55px; box-shadow: 0 0 5px rgba(0, 0, 0, 0.50); font-weight: 500;">
                        Voltar
                    </a>

                    <button type="submit" name="validar_codigo" class="btn d-flex align-items-center justify-content-center w-50 ms-2"
                            style="background-color:  #00a3c7; color: white; border-radius: 10px; height: 55px; box-shadow: 0 0 5px rgba(0, 0, 0, 0.50); font-weight: 500;">
                        Validar
                    </button>
                </div>
            </form>
        </div>
    </main>
</section>

<!-- lib SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
<!-- Script para exibir mensagens de erro ou sucesso -->
<script src="../public/js/recuperacao_alerts.js" defer></script>

<?php require_once __DIR__ . '/components/footer.php'; ?>