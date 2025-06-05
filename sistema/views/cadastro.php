<?php
require_once '../init.php'; // <-- Inicia sessão
// Chama o componente de cabeçalho da página
require_once __DIR__ . '/components/header.php'; 

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<title>Cadastro</title>
<!-- CSS personalizado -->
<link rel="stylesheet" href="../public/css/cadastrar_se/cadastro.css">

<section class="bg-white d-flex flex-column min-vh-100">
    <main class="container my-auto px-3" style="max-width: 500px;">
        <h4 class="text-center fw-bold mb-5 mt-4" style="color: #444">Cadastro</h4>

        <form method="POST" action="../controllers/processa.php">
        <!-- Token CSRF -->
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <!-- Nome -->
            <div class="mb-2">
                <div class="d-flex align-items-center"
                    style="border: 2px solid #00a3c7; border-radius: 10px; padding: 0 1rem; height: 55px;">
                    <i class="bi bi-person" style="color: #00a3c7; font-size: 1.2rem;"></i>
                    <input  type="text" name="nome" class="form-control border-0 shadow-none placeholder-light"
                        placeholder="*Nome completo"
                        style="margin-left: 0.75rem; font-size: 1rem; color: #444; height: 100%; line-height: 1.5; padding: 0;"
                        required>
                </div>
            </div>

            <!-- Telefone -->
            <div class="mb-2">
                <!-- Mensagem ao usuário -->
                    <small class="text-muted d-block mb-1" style="font-size: 0.85rem; opacity: 0.7;">
                        Insira o DDD+número do celular, por exemplo: 11 para São Paulo.
                    </small>
                <div class="d-flex align-items-center"
                    style="border: 2px solid #00a3c7; border-radius: 10px; padding: 0 1rem; height: 55px;">
                    <i class="bi bi-telephone" style="color: #00a3c7; font-size: 1.2rem;"></i>
                    <input type="text" name="telefone" id="telefone" class="form-control border-0 shadow-none placeholder-light"
                        placeholder="*Celular (com DDD)"
                        style="margin-left: 0.75rem; font-size: 1rem; color: #444; height: 100%; line-height: 1.5; padding: 0;"
                        required>
                </div>
            </div>

            <!-- Email -->
            <div class="mb-2">
                <div class="d-flex align-items-center"
                    style="border: 2px solid #00a3c7; border-radius: 10px; padding: 0 1rem; height: 55px;">
                    <i class="bi bi-envelope" style="color: #00a3c7; font-size: 1.2rem;"></i>
                    <input type="email" name="email" class="form-control border-0 shadow-none placeholder-light"
                        placeholder="*Endereço de e-mail"
                        style="margin-left: 0.75rem; font-size: 1rem; color: #444; height: 100%; line-height: 1.5; padding: 0;"
                        required>
                </div>
            </div>

            <!-- CPF -->
            <div class="mb-2">
                <div class="d-flex align-items-center"
                    style="border: 2px solid #00a3c7; border-radius: 10px; padding: 0 1rem; height: 55px;">
                    <i class="bi bi-credit-card" style="color: #00a3c7; font-size: 1.2rem;"></i>
                    <input type="text" name="cpf" id="cpf" class="form-control border-0 shadow-none placeholder-light"
                        placeholder="*CPF"
                        style="margin-left: 0.75rem; font-size: 1rem; color: #444; height: 100%; line-height: 1.5; padding: 0;"
                        required>
                </div>
            </div>

            <!-- CEP -->
            <div class="mb-2">
                <div class="d-flex align-items-center"
                    style="border: 2px solid #00a3c7; border-radius: 10px; padding: 0 1rem; height: 55px;">
                    <i class="bi bi-geo-alt" style="color: #00a3c7; font-size: 1.2rem;"></i>
                    <input type="text"  name="cep" id="cep" class="form-control border-0 shadow-none placeholder-light"
                        placeholder="*CEP"
                        style="margin-left: 0.75rem; font-size: 1rem; color: #444; height: 100%; line-height: 1.5; padding: 0;"
                        required>
                </div>
            </div>

            <!-- Rua e Número -->
            <div class="row g-2">
                <div class="col-7">
                    <div class="d-flex align-items-center"
                        style="border: 2px solid #00a3c7; border-radius: 10px; padding: 0 1rem; height: 55px;">
                        <i class="bi bi-geo" style="color: #00a3c7; font-size: 1.2rem;"></i>
                        <input type="text" name="rua"  id="rua" class="form-control border-0 shadow-none placeholder-light"
                            placeholder="*Rua"
                            style="margin-left: 0.75rem; font-size: 1rem; color: #444; height: 100%; line-height: 1.5; padding: 0;"
                            required>
                    </div>
                </div>
                <div class="col-5">
                    <div class="d-flex align-items-center"
                        style="border: 2px solid #00a3c7; border-radius: 10px; padding: 0 1rem; height: 55px;">
                        <i class="bi bi-123" style="color: #00a3c7; font-size: 1.2rem;"></i>
                        <input type="number" name="numero"
                            class="form-control border-0 shadow-none text-center placeholder-light"
                            placeholder="*Número"
                            style="margin-left: 0.75rem; font-size: 1rem; color: #444; height: 100%; line-height: 1.5; padding: 0;"
                            required>
                    </div>
                </div>
            </div>

            <!-- Bairro -->
            <div class="mt-2 mb-2">
                <div class="d-flex align-items-center"
                    style="border: 2px solid #00a3c7; border-radius: 10px; padding: 0 1rem; height: 55px;">
                    <i class="bi bi-geo-alt-fill" style="color: #00a3c7; font-size: 1.2rem;"></i>
                    <input type="text" name="bairro"  id="bairro" class="form-control border-0 shadow-none placeholder-light"
                        placeholder="*Bairro"
                        style="margin-left: 0.75rem; font-size: 1rem; color: #444; height: 100%; line-height: 1.5; padding: 0;"
                        required>
                </div>
            </div>

            <!-- Senha -->
            <div class="mb-2">
                <div class="d-flex align-items-center"
                    style="border: 2px solid #00a3c7; border-radius: 10px; padding: 0 1rem; height: 55px;">
                    <i class="bi bi-lock" style="color: #00a3c7; font-size: 1.2rem;"></i>
                    <input type="password" name="senha" id="senha" class="form-control border-0 shadow-none placeholder-light"
                        placeholder="*Nova senha"
                        style="margin-left: 0.75rem; font-size: 1rem; color: #444; height: 100%; line-height: 1.5; padding: 0;"
                        required>
                </div>
            </div>

            <!-- Confirmar Senha -->
            <div class="mb-3">
                <div class="d-flex align-items-center"
                    style="border: 2px solid #00a3c7; border-radius: 10px; padding: 0 1rem; height: 55px;">
                    <i class="bi bi-lock" style="color: #00a3c7; font-size: 1.2rem;"></i>
                    <input type="password" name="confirma_senha" id="confirma_senha"
                        class="form-control border-0 shadow-none placeholder-light" placeholder="*Confirmar nova senha"
                        style="margin-left: 0.75rem; font-size: 1rem; color: #444; height: 100%; line-height: 1.5; padding: 0;"
                        required>
                </div>
                    <!-- Small de confirmar senha em raltime -->
                    <small id="senha-feedback" class="text-danger d-block mt-1" style="font-size: 0.9rem;"></small>
            </div>

            <!-- Checkboxes -->
            <div class="form-check mb-2">
                <input class="form-check-input" style="border: 2px solid #00a3c7;" type="checkbox" name="termos" id="termos" required>
                <label class="form-check-label" for="termos">Aceito os <a href="termos.html" target="_blank">Termos de Uso</a></label>
            </div>

            <!-- <div class="form-check mb-4">
                <input class="form-check-input" style="border: 2px solid #00a3c7;" type="checkbox" id="privacidade"
                    required>
                <label class="form-check-label" for="privacidade">Li e concordo com a Política de Privacidade</label>
            </div> -->

            <!-- Botões -->
            <div class="d-flex justify-content-between">
                <a href="../views/login.php" class="btn d-flex align-items-center justify-content-center w-50 me-2"
                    style="background-color:  #00a3c7; color: white; border-radius: 10px; height: 55px; box-shadow: 0 0 5px rgba(0, 0, 0, 0.50); font-weight: 500;">
                    Voltar
                </a>

                <button type="submit" class="btn d-flex align-items-center justify-content-center w-50 ms-2"
                    style="background-color:  #00a3c7; color: white; border-radius: 10px; height: 55px; box-shadow: 0 0 5px rgba(0, 0, 0, 0.50); font-weight: 500;">
                    Cadastrar
                </button>
            </div>

        </form>
    </main>
</section>
    <!-- lib sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Lib Inputmask -->
     <script src="https://unpkg.com/imask"></script>
<!-- // alerts de validação -->
<script src="../public/js/val_cads.js"></script>