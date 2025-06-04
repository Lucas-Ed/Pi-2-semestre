<?php
require_once '../init.php';
require_once '../model/db.php';
require_once '../Vendor/autoload.php'; // Autoload do Composer para PHPMailer e outros

// Carrega as variáveis do .env
// var_dump($_ENV); die();

// log de erros
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Função para gerar token de 5 dígitos
function gerarToken() {
    return str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
}

// Função para carregar variáveis do .env
function env($key, $default = null) {
    return $_ENV[$key] ?? getenv($key) ?: $default;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_email'])) {
    // echo "<script>alert('PHP foi acionado');</script>";
    // var_dump($_POST); die();
    session_start(); // [MOD] Garantir sessão iniciada
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("Token CSRF inválido.");
}
    $email = $_POST['email'];

    // Verifica se e-mail está cadastrado
    $stmt = $conn->prepare('SELECT idusuarios FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header('Location: ../views/recovery.php?erro=email_invalido');
        exit;
    }

    $usuario = $result->fetch_assoc();
    $userId = $usuario['idusuarios'];

    // Gerar token e dados
    $token = gerarToken();
    $token_hash = password_hash($token, PASSWORD_DEFAULT);
    $criacao = date('Y-m-d H:i:s');
    $expiracao = date('Y-m-d H:i:s', strtotime('+2 hours'));

    // Verifica se o token já foi utilizado
    //var_dump($token_hash, $criacao, $expiracao); exit;
    
    // Atualizar token no banco
    $update = $conn->prepare("UPDATE usuarios 
        SET token_hash = ?, criacao_token = ?, expiracao_token = ? 
        WHERE idusuarios = ?");
    $update->bind_param('sssi', $token_hash, $criacao, $expiracao, $userId);
    // $update->execute();
    if ($update->execute()) {
    // Sucesso - continua com envio do e-mail
    } else {
        echo "Erro ao atualizar usuário: " . $conn->error;
        exit;
    }

    $_SESSION['email_validacao'] = $email; // [MOD] Armazena o e-mail na sessão

    // Enviar e-mail com PHPMailer
    try {
        $mail = new PHPMailer(true);
        // $mail->SMTPDebug = 2; // Ou use 3 para mais detalhe
        // $mail->Debugoutput = 'html';

        $mail->isSMTP();
        $mail->Host = env('SMTP_HOST');
        $mail->SMTPAuth = true;
        $mail->Username = env('SMTP_USER');
        $mail->Password = env('SMTP_PASS');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = env('SMTP_PORT');

        $mail->setFrom(env('SMTP_FROM'), env('SMTP_FROM_NAME'));
        $mail->addAddress($email);
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);
         // Assunto do e-mail
        $mail->Subject = 'Recuperação de Senha - Embelezamento automotivo';
         // Corpo do e-mail em HTML
        $mail->Body = "<p>Seu código de recuperação válido por 2 horas é: <strong>$token</strong></p>";
        // Versão em texto simples (fallback
        $mail->AltBody = "Seu código de recuperação é: $token";

        $mail->send();
        // exibir variáveis de ambiente
        // echo "SMTP_USER: " . env('SMTP_USER') . "<br>";
        // echo "SMTP_PASS: " . env('SMTP_PASS') . "<br>";
        // echo "SMTP_HOST: " . env('SMTP_HOST') . "<br>";
        // echo "FROM: " . env('SMTP_FROM') . "<br>";
        // die();


        header('Location: ../views/recovery.php?status=codigo_enviado');
        exit;
    } catch (Exception $e) {
        header('Location: ../views/recovery.php?erro=email_envio');
        exit;
    }
}// validação de código
else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validar_codigo'])) {
        session_start(); // [MOD] Garante que a sessão está ativa
       if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Token CSRF inválido.");
    }
    
    date_default_timezone_set('America/Sao_Paulo');

    $codigo = $_POST['codigo'];
    $email = $_SESSION['email_validacao'] ?? ''; // [MOD] Usa o e-mail salvo na sessão
    //$email = $_POST['email'];

    $stmt = $conn->prepare('SELECT idusuarios, token_hash, criacao_token, expiracao_token FROM usuarios WHERE email = ? AND token_hash IS NOT NULL LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header('Location: ../views/recovery.php?erro=codigo_incorreto');
        exit;
    }

    $usuario = $result->fetch_assoc();
    // Verifica se o token já foi utilizado
    //var_dump($usuario); exit;

    $expiracao = strtotime($usuario['expiracao_token']);
    $agora = time();

    // Adicione logs de depuração, se necessário
    //var_dump($usuario['expiracao_token'], $expiracao, $agora, date('Y-m-d H:i:s', $agora)); exit;

    // Primeiro: verifica se expirou
    if ($agora > $expiracao) { // [MOD] Verifica expiração primeiro
            header('Location: ../views/recovery.php?erro=token_expirado');
            exit;
        }

        if (!password_verify($codigo, $usuario['token_hash'])) {
            header('Location: ../views/recovery.php?erro=codigo_incorreto');
            exit;
        }

        $_SESSION['redefinir_usuario_id'] = $usuario['idusuarios'];
        header('Location: ../views/recovery.php?status=codigo_validado');
        exit;
    }


?>