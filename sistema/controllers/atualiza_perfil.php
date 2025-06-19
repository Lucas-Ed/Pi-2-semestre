<?php
session_start();
require_once __DIR__ . '/../init.php'; // conexão com o banco
require_once __DIR__ . '/../vendor/autoload.php'; // se usa dotenv


$userId = $_SESSION['idusuarios'];

// Dados recebidos do formulário
$nome = trim($_POST['nome']);
$telefone = trim($_POST['telefone']);
$email = trim($_POST['email']);
$cpf = trim($_POST['cpf']);
$cep = trim($_POST['cep']);
$rua = trim($_POST['rua']);
$numero = trim($_POST['numero']);
$bairro = trim($_POST['bairro']);

// Criptografa o CPF antes de armazenar
$key = base64_decode($_ENV['CHAVE_CPF']);
if ($key === false) {
    die('Erro: chave CPF inválida.');
}
$cpfCriptografado = openssl_encrypt($cpf, 'AES-128-ECB', $key);

// Atualiza dados na tabela de usuários
$sqlUser = "UPDATE usuarios SET nome = ?, telefone = ?, email = ?, cpf = ? WHERE idusuarios = ?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param('ssssi', $nome, $telefone, $email, $cpfCriptografado, $userId);
$stmtUser->execute();

// Atualiza dados na tabela de endereços
$sqlEndereco = "UPDATE enderecos SET cep = ?, rua = ?, numero = ?, bairro = ? WHERE usuarios_idusuarios = ?";
$stmtEndereco = $conn->prepare($sqlEndereco);
$stmtEndereco->bind_param('ssssi', $cep, $rua, $numero, $bairro, $userId);
$stmtEndereco->execute();

// Atualiza os dados na sessão também
$_SESSION['nome'] = $nome;
$_SESSION['telefone'] = $telefone;
$_SESSION['email'] = $email;
$_SESSION['cpf'] = $cpf;
$_SESSION['cep'] = $cep;
$_SESSION['rua'] = $rua;
$_SESSION['numero'] = $numero;
$_SESSION['bairro'] = $bairro;

// Mensagem de sucesso
$_SESSION['alert_success'] = "atualizado";

// Redireciona de volta para a página de perfil ou dashboard
header('Location: ../views/dashboard_user.php');
exit(); // Encerra o script para evitar que mais código seja executado
?>
