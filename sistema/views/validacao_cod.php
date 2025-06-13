<?php 
session_start();

// Gera um token CSRF se não existir
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Validar Código</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap Latest -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="../public/css/recuperar_senha/confirmar_identidade.css">
</head>
<body class="bg-white d-flex flex-column min-vh-100">

<section class="bg-white d-flex flex-column min-vh-100 position-relative">
    <main class="d-flex flex-grow-1 align-items-center justify-content-center px-3">
        <div style="width: 100%; max-width: 400px;">
            <h4 class="text-center fw-bold mb-5" style="color: #444;">Validação de Token</h4>
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
                         style="border: 2px solid #00a3c7; border-radius: 10px; padding: 0 1rem; height: 55px;">
                        <i class="bi bi-key" style="color: #00a3c7; font-size: 1.2rem;"></i>
                        <input type="text" name="codigo" class="form-control border-0 shadow-none placeholder-light"
                               placeholder="Código de validação"
                               style="margin-left: 0.75rem; font-size: 1rem; color: #444; height: 100%; line-height: 1.5; padding: 0;"
                               required>
                    </div>
                </div>



                <div class="d-flex justify-content-between">
                    <a href="./recovery.php" class="btn d-flex align-items-center justify-content-center w-50 me-2"
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

            <!-- Rodapé fixo no fundo -->
    <footer class="text-white text-center py-3 fixed-bottom"
        style="background-color: #0097B2; border-top-left-radius: 20px; border-top-right-radius: 20px;">
        <div class=" d-flex gap-3 p-3 align-items-center justify-content-center">
        
            <a href="https://wa.me/+555519998647044?text=Oi!%20Vim%20do%20WebSite%20e%20preciso%20de%20suporte,%20poderia%20me%20ajudar." class="text-white"
                target="_blank" rel="noopener noreferrer">
                <!-- Ícone do WhatsApp -->
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-whatsapp"
                viewBox="0 0 16 16">
                <path
                    d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232" />
            </svg>
            </a>

            <a href="https://www.instagram.com/embelezamentoautomotivos?igsh=MWlxOGw2YnliMGJvcg%3D%3D" class="text-white"
                target="_blank" rel="noopener noreferrer">
                <!-- Ícone do Instagram -->
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-instagram"
                viewBox="0 0 16 16">
                <path
                    d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334" />
            </svg>
        </a>
        </div>
        <small>© 2021 All Rights Reserved</small>
</section>

    <!-- lib SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
</body>
</html>
