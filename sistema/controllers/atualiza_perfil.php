<?php
// Controlador para atualizar o perfil do usuário.
session_start(); // Inicia a sessão para acessar as variáveis de sessão
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

// Salva os dados preenchidos na sessão (exceto senha)
// Essa sessão será usada para manter os dados no formulário em caso de erro
$_SESSION['form_data'] = [
    'nome' => $nome,
    'telefone' => $telefone,
    'email' => $email,
    'cpf' => $cpf,
    'cep' => $cep,
    'rua' => $rua,
    'numero' => $numero,
    'bairro' => $bairro
];

// Validações com retorno de erros via sessão
$erros = [];

//  Validações


// TELEFONE
$telefoneLimpo = preg_replace('/\D/', '', $telefone);
if (strlen($telefoneLimpo) > 0 && strlen($telefoneLimpo) !== 11) {
    $erros[] = "Telefone inválido. Deve conter 11 dígitos com DDD.";
}

// CPF
$cpfLimpo = preg_replace('/\D/', '', $cpf);
if (strlen($cpfLimpo) > 0 && strlen($cpfLimpo) !== 11) {
    $erros[] = "CPF inválido. Deve conter 11 dígitos numéricos.";
}

// CEP
$cepLimpo = preg_replace('/\D/', '', $cep);
if (strlen($cepLimpo) > 0 && strlen($cepLimpo) !== 8) {
    $erros[] = "CEP inválido. Deve conter 8 dígitos.";
}

// Verifica se há erros
if (!empty($erros)) {
    $_SESSION['form_errors'] = $erros;
    header("Location: ../views/dashboard_user.php");
    exit();
}

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
// Verifica se já existe endereço para o usuário
$sqlCheckEndereco = "SELECT 1 FROM enderecos WHERE usuarios_idusuarios = ?";
$stmtCheck = $conn->prepare($sqlCheckEndereco);
$stmtCheck->bind_param('i', $userId);
$stmtCheck->execute();
$stmtCheck->store_result();

if ($stmtCheck->num_rows > 0) {
    // Já existe, faz UPDATE
    $sqlEndereco = "UPDATE enderecos SET cep = ?, rua = ?, numero = ?, bairro = ? WHERE usuarios_idusuarios = ?";
    $stmtEndereco = $conn->prepare($sqlEndereco);
    $stmtEndereco->bind_param('ssssi', $cep, $rua, $numero, $bairro, $userId);
    $stmtEndereco->execute();
} else {
    // Não existe, faz INSERT
    $sqlEndereco = "INSERT INTO enderecos (cep, rua, numero, bairro, usuarios_idusuarios) VALUES (?, ?, ?, ?, ?)";
    $stmtEndereco = $conn->prepare($sqlEndereco);
    $stmtEndereco->bind_param('ssssi', $cep, $rua, $numero, $bairro, $userId);
    $stmtEndereco->execute();
}


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
