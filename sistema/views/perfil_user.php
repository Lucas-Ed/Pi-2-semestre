<?php
require_once '../init.php'; // <-- Inicia sessão
// Chama o componente de cabeçalho da página
require_once __DIR__ . '/components/header.php'; 

// Verifica se o usuário está logado
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../views/index.php");
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<title>Perfil</title>
<!-- CSS personalizado -->
<link rel="stylesheet" href="../public/css/cadastrar_se/cadastro.css">

<section class="bg-white d-flex flex-column min-vh-100">
    <main class="container my-auto px-3" style="max-width: 500px;">
        <h4 class="text-center fw-bold mb-5 mt-4" style="color: #444">Perfil</h4>

        <form method="POST" action="../controllers/processa.php">
        <!-- Token CSRF -->
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        
            <!-- TODOS OS INPUTS QUE TEM NESSE ARQUIVO JÁ DEVEM ESTAR SETADOS ASSIM QUE O USUARIO ENTRAR! ASSIM ELE CONSEGUE EDITAR APENS O NESSESARIO AO INVES DE INSERIR TUDO NOVAMENTE! -->

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
                <div class="d-flex align-items-center"
                    style="border: 2px solid #00a3c7; border-radius: 10px; padding: 0 1rem; height: 55px;">
                    <i class="bi bi-telephone" style="color: #00a3c7; font-size: 1.2rem;"></i>
                    <input type="text" name="telefone" id="telefone" class="form-control border-0 shadow-none placeholder-light"
                        placeholder="*Telefone"
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
                        <input type="text" name="rua" class="form-control border-0 shadow-none placeholder-light"
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
                    <input type="text" name="bairro" class="form-control border-0 shadow-none placeholder-light"
                        placeholder="*Bairro"
                        style="margin-left: 0.75rem; font-size: 1rem; color: #444; height: 100%; line-height: 1.5; padding: 0;"
                        required>
                </div>
            </div>

            <!-- Botões -->
            <div class="d-flex justify-content-between">
                <a href="../views/dashboard_user.php" class="btn d-flex align-items-center justify-content-center w-50 me-2"
                    style="background-color:  #00a3c7; color: white; border-radius: 10px; height: 55px; box-shadow: 0 0 5px rgba(0, 0, 0, 0.50); font-weight: 500;">
                    Voltar
                </a>


                <!-- QUANDO APERTAMOS O BOTÃO DE SALVAR OS DADOS DO USUARIO LOGADOS DEVERÃO SER ALTERADOS NO BANCO DE DADOS -->
                <button type="submit" class="btn d-flex align-items-center justify-content-center w-50 ms-2"
                    style="background-color:  #00a3c7; color: white; border-radius: 10px; height: 55px; box-shadow: 0 0 5px rgba(0, 0, 0, 0.50); font-weight: 500;">
                    Salvar
                </button>
            </div>

        </form>
    </main>
</section>
    <!-- lib sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- // alerts de validação -->
<script src="../public/js/val_cads.js"></script>


</body>
</html>