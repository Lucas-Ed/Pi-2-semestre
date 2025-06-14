<?php
require_once __DIR__ . '/components/header.php';
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Alterar senha</title>
    <!-- Icons bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" href="../public/uploads/img/favicon.svg" type="image/svg+xml">
  <!-- CSS personalizado -->
<link rel="stylesheet" href="../public/css/recuperar_senha/alterar_senha.css">
</head>

<body class="bg-white d-flex flex-column min-vh-100 position-relative">
<script>
    const phpStatus = <?= json_encode($status) ?>;
    //console.log("phpStatus:", phpStatus);
</script>
  <main class="d-flex flex-grow-1 align-items-center justify-content-center px-3">
    <div style="width: 100%; max-width: 400px;">
      <h4 class="text-center fw-bold mb-1" style="color: #444;">Olá <?= htmlspecialchars($nomeUsuario) ?>,</h4>
      <p class="text-center text-muted mb-4">Redefina sua senha</p>

      <form action="../controllers/new_pass.php" method="POST">

        <div class="mb-3">
          <div class="d-flex align-items-center"
            style="border: 2px solid #00a3c7; border-radius: 10px; padding: 0 1rem; height: 55px;">
            <i class="bi bi-lock" style="color: #00a3c7; font-size: 1.2rem;"></i>
            <input type="password" name="senha" class="form-control border-0 shadow-none placeholder-light"
              placeholder="*Nova senha" required style="margin-left: 0.75rem;">
          </div>
        </div>

        <!-- Token CSRF oculto -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

        <div class="mb-3">
          <div class="d-flex align-items-center"
            style="border: 2px solid #00a3c7; border-radius: 10px; padding: 0 1rem; height: 55px;">
            <i class="bi bi-lock" style="color: #00a3c7; font-size: 1.2rem;"></i>
            <input type="password" name="nova_senha" class="form-control border-0 shadow-none placeholder-light"
              placeholder="*Confirmar nova senha" required style="margin-left: 0.75rem;">
          </div>
        </div>

        <div class="d-flex justify-content-between">
          <a href="./recovery.php"
            class="btn d-flex align-items-center justify-content-center w-50 me-2"
            style="background-color:  #00a3c7; color: white; border-radius: 10px; height: 55px;">
            Voltar
          </a>
          <button type="submit"
            class="btn d-flex align-items-center justify-content-center w-50 ms-2"
            style="background-color:  #00a3c7; color: white; border-radius: 10px; height: 55px;">
            Confirmar
          </button>
        </div>
      </form>
    </div>
  </main>

  <!--Script para validação de nova senha-->
  <script src="../public/js/new_pass_alerts.js" defer></script>

  <!-- blibioteca de sweetalert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Rodapé -->
   <?php include_once '../views/components/footer.php'; ?>
</body>
</html>