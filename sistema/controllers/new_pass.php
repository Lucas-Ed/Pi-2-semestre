<?php
// Controller para redefinição de senha.
require_once '../init.php'; // Inicializa o ambiente e a conexão com o banco de dados.
// require_once '../model/db.php';

// Inicia a sessão
ob_start();

// Impede requisições que não sejam POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/alter_pass.php?erro=acesso_nao_autorizado");
    exit;
}

// Proteção CSRF: valida token
$tempoExpiracaoToken = 7200; // 2 horas

$tokenSessao = $_SESSION['csrf_token']['value'] ?? null;
$criadoEm = $_SESSION['csrf_token']['created_at'] ?? 0;

if (
    !isset($_POST['csrf_token']) ||
    !$tokenSessao ||
    $_POST['csrf_token'] !== $tokenSessao ||
    (time() - $criadoEm > $tempoExpiracaoToken)
) {
    unset($_SESSION['csrf_token']); // sempre limpe o token
    header("Location: ../views/alter_pass.php?erro=csrf_invalido");
    exit;
}

// Token válido, pode prosseguir com o restante...
unset($_SESSION['csrf_token']); // Destroi o token após uso (evita reutilização)


// Verifica se o ID do usuário está na sessão
if (!isset($_SESSION['redefinir_usuario_id'])) {
    header("Location: ../views/recovery.php?erro=acesso_nao_autorizado");
    exit;
}

$userId = $_SESSION['redefinir_usuario_id'];

// Recebe os dados
$senha = $_POST['senha'] ?? '';
$nova_senha = $_POST['nova_senha'] ?? '';

// Valida senhas
if ($senha !== $nova_senha) {
    header("Location: ../views/alter_pass.php?erro=senhas_diferentes");
    exit;
}
// (Opcional) Validação extra de segurança de senha menor que 6 caracteres.
// if (strlen($senha) < 6) {
//     header("Location: ../views/alter_pass.php?erro=senha_muito_curta");
//     exit;
// }

// Validação de senha forte: pelo menos 8 caracteres, contendo letras e números
if (
    strlen($senha) < 8 ||                      // menos de 8 caracteres = inválido
    !preg_match('/[A-Za-z]/', $senha) ||       // deve conter pelo menos uma letra
    !preg_match('/\d/', $senha)                // deve conter pelo menos um número
) {
    header("Location: ../views/alter_pass.php?erro=senha_fraca");
    exit;
}

$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

// Atualiza no banco
$update = $conn->prepare("UPDATE usuarios 
    SET senha = ?, token_hash = NULL, criacao_token = NULL, expiracao_token = NULL 
    WHERE idusuarios = ?");
$update->bind_param('si', $senhaHash, $userId);

// if ($update->execute()) {
//     unset($_SESSION['redefinir_usuario_id']);
//     unset($_SESSION['csrf_token']); // Remove token após uso

//     // $_SESSION['status'] = 'senha_redefinida';// aqui
//     // header("Location: ../views/alter_pass.php");// aqui
//     header("Location: ../views/sucsses.php");
//     // echo "<script>window.location.href = '../views/alter_pass.php?status=senha_redefinida';</script>";
//     //echo "<script>location.replace('../views/alter_pass.php?status=senha_redefinida');</script>";
//     ob_end_flush(); // <-- libera o buffer se tudo ocorreu bem
//     exit;

// } 

// redireciona o usuário para o dashboard após redefinição
if ($update->execute()) {
    // Login automático após redefinição
    $_SESSION['loggedin'] = true;
    $_SESSION['idusuarios'] = $userId;
    $_SESSION['LAST_ACTIVITY'] = time();

    // Recupera o nome do usuário para exibir no dashboard
    $stmt = $conn->prepare("SELECT nome, telefone, email FROM usuarios WHERE idusuarios = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    $_SESSION['nome'] = $usuario['nome'] ?? 'Usuário';
    $_SESSION['telefone'] = $usuario['telefone'] ?? '';
    $_SESSION['email'] = $usuario['email'] ?? '';

    unset($_SESSION['redefinir_usuario_id']);
    unset($_SESSION['csrf_token']);

    $_SESSION['alert_success'] = 'senha_redefinida';

    header("Location: ../views/dashboard_user.php");
    ob_end_flush();
    exit;
} else {
    header("Location: ../views/alter_pass.php?erro=falha_redefinicao");
    exit;
}
?>