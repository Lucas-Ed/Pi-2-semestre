<?php
require_once __DIR__ . '/../init.php'; // Conecta via mysqli
// converte para em variável js.
// $status = $_SESSION['status'] ?? null; // aqui
// unset($_SESSION['status']);


// gerando token CSRF
$tempoExpiracaoToken = 7200; // 2 horas

if (
    empty($_SESSION['csrf_token']) || 
    time() - ($_SESSION['csrf_token']['created_at'] ?? 0) > $tempoExpiracaoToken
) {
    $_SESSION['csrf_token'] = [
        'value' => bin2hex(random_bytes(32)),
        'created_at' => time()
    ];
}
$csrf_token = $_SESSION['csrf_token']['value'];



// Verifica se o usuário já validou a identidade
    if (!isset($_SESSION['redefinir_usuario_id'])) {
    // Redireciona se não tiver permissão
    header('Location: ../views/recovery.php?erro=acesso_nao_autorizado');
    exit;
}

//destruir a variável para que não seja reutilizável
//unset($_SESSION['redefinir_usuario_id']);
//Consulta o nome do usuário
$userId = $_SESSION['redefinir_usuario_id'];

// Consulta o nome do usuário
$stmt = $conn->prepare("SELECT nome FROM usuarios WHERE idusuarios = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$nomeUsuario = $usuario['nome'] ?? 'Usuário';

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Alterar senha</title>
    <!-- Icons bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" href="../public/uploads/img/favicon.svg" type="image/svg+xml">
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="../public/css/recuperar_senha/alterar_senha.css">
</head>

<body class="bg-white d-flex flex-column min-vh-100">

    <script>
    const phpStatus = <?= json_encode($status) ?>;
    //console.log("phpStatus:", phpStatus);
    </script>

    <main class="d-flex flex-grow-1 justify-content-center" style="margin-top: 200px;">
        <div style="width: 380px;">

            <div class="caixa-texto" id="caixaTexto">
                <!-- Texto inicial -->
                <h4 class="text-center fw-bold mb-5 text-muted" style="color: #444;">Olá
                    <?= htmlspecialchars($nomeUsuario) ?></h4>
            </div>

            <script>
            const textos = [
                `<h4 class="text-center fw-bold mb-5 text-muted" style="color: #444;">Olá
                    <?= htmlspecialchars($nomeUsuario) ?></h4>`,
                `<h4 class="text-center fw-bold mb-5 text-muted" style="color: #444;">Alteração de senha</h4>`
            ];

            let index = 0;

            setInterval(() => {
                index = (index + 1) % textos.length;
                document.getElementById('caixaTexto').innerHTML = textos[index];
            }, 4000); // alterna a cada 4 segundos
            </script>


            <small class="text-muted" style="font-size: 13px; margin-bottom: 1rem; display: block;">Insira sua nova
                senha</small>

            <form action="../controllers/new_pass.php" method="POST">

                <div class="mb-3">
                    <div class="d-flex align-items-center"
                        style="border: 2px solid #0097B2; border-radius: 10px; padding: 0 1rem; height: 55px;">
                        <i class="bi bi-lock" style="color: #0097B2; font-size: 1.2rem;"></i>
                        <input type="password" name="senha" class="form-control border-0 shadow-none placeholder-light"
                            placeholder="*Nova senha" required style="margin-left: 0.75rem;">
                    </div>
                </div>

                <!-- Token CSRF oculto -->
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                <div class="mb-3">
                    <div class="d-flex align-items-center"
                        style="border: 2px solid #0097B2; border-radius: 10px; padding: 0 1rem; height: 55px;">
                        <i class="bi bi-lock" style="color: #0097B2; font-size: 1.2rem;"></i>
                        <input type="password" name="nova_senha"
                            class="form-control border-0 shadow-none placeholder-light"
                            placeholder="*Confirmar nova senha" required style="margin-left: 0.75rem;">
                    </div>
                    <!-- Small de confirmar senha em raltime -->
                    <small id="senha-feedback" class="text-danger d-block mt-1" style="font-size: 0.9rem;"></small>
                </div>


                <div class="d-flex justify-content-between">
                    <a href="./recovery.php" class="btn d-flex align-items-center justify-content-center w-50 me-2"
                        style="background-color: #0097B2; color: white; border-radius: 10px; height: 55px; box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.3); font-size: 18px;">
                        Voltar
                    </a>
                    <button type="submit" class="btn d-flex align-items-center justify-content-center w-50 ms-2"
                        style="background-color: #0097B2; color: white; border-radius: 10px; height: 55px; box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.3); font-size: 18px;">
                        Confirmar
                    </button>
                </div>
            </form>
        </div>
    </main>

    <!-- Rodapé -->
    <footer
        class="text-white text-center py-5 d-flex flex-column align-items-center justify-content-center fixed-bottom"
        style="background-color: #0097B2; border-top-left-radius: 20px; border-top-right-radius: 20px; height: 130px; font-size: 1.1rem; box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.3);">
        <small>
            &copy; <?= date('Y') ?> Embelezamento Automotivo
            <br>
            Todos os direitos reservados
        </small>
    </footer>

    <!--Script para validação de nova senha-->
    <script src="../public/js/new_pass_alerts.js?v=<?= time() ?>" defer></script>

    <!-- blibioteca de sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



</body>

</html>