<?php
require '../Vendor/autoload.php';
require_once '../init.php';

// Ativar exceptions em erros do mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// validar CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Falha na validação CSRF');
}
// Token válido, então invalida o atual
unset($_SESSION['csrf_token']); // Invalida o token

// Conexão ao banco
$conn = new mysqli(
    $_ENV["DB_HOST"],
    $_ENV["DB_USER"],
    $_ENV["DB_PASS"],
    $_ENV["DB_NAME"]
);
// Verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

//  Captura e sanitiza os dados do formulário.
$nome = trim($_POST["nome"]);
$email = trim($_POST["email"]);
$cpf = preg_replace('/[^0-9]/', '', $_POST["cpf"]);
$telefone = preg_replace('/[^0-9]/', '', $_POST["telefone"]);
$rua = trim($_POST["rua"]);
$numero = intval($_POST["numero"]);
$bairro = trim($_POST["bairro"]);
$cep = preg_replace('/[^0-9]/', '', $_POST["cep"]);
$senha = $_POST["senha"];
$senha_confirma = $_POST["confirma_senha"];
$termos = isset($_POST["termos"]) ? 1 : 0;
$tipo = 'cliente';

//  Validações

// E-mail
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../views/cadastro.php?erro=email_invalido");
    exit();
}

// Senha (mínimo 8 caracteres, ao menos 1 letra e 1 número)
if (strlen($senha) < 8 || !preg_match('/[A-Za-z]/', $senha) || !preg_match('/[0-9]/', $senha)) {
    header("Location: ../views/cadastro.php?erro=senha_fraca");
    exit();
}

// Confirmação de senha
if ($senha !== $senha_confirma) {
    header("Location: ../views/cadastro.php?erro=senha");
    exit();
}

// CPF: 11 dígitos
if (!preg_match('/^\d{11}$/', $cpf)) {
    header("Location: ../views/cadastro.php?erro=cpf_invalido");
    exit();
}

// Telefone: 10 ou 11 dígitos
if (!preg_match('/^\d{10,11}$/', $telefone)) {
    header("Location: ../views/cadastro.php?erro=telefone_invalido");
    exit();
}

// CEP: 8 dígitos
if (!preg_match('/^\d{8}$/', $cep)) {
    header("Location: ../views/cadastro.php?erro=cep_invalido");
    exit();
}

// Criptografa a senha
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

//  Verifica se e-mail ou CPF já estão cadastrados
$sqlCheck = "SELECT idusuarios FROM usuarios WHERE email = ? OR cpf = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("ss", $email, $cpf);
$stmtCheck->execute();
$stmtCheck->store_result();

if ($stmtCheck->num_rows > 0) {
    header("Location: ../views/cadastro.php?erro=email_ou_cpf");
    $stmtCheck->close();
    $conn->close();
    exit();
}
$stmtCheck->close();

//  Insere usuário
$sqlUsuario = "INSERT INTO usuarios (nome, email, senha, cpf, telefone, termos, tipo) 
               VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmtUsuario = $conn->prepare($sqlUsuario);
$stmtUsuario->bind_param("sssssis", $nome, $email, $senhaHash, $cpf, $telefone, $termos, $tipo);

if ($stmtUsuario->execute()) {
    $usuarios_idusuarios = $conn->insert_id;

    // Insere endereço
    $sqlEndereco = "INSERT INTO enderecos (usuarios_idusuarios, rua, numero, bairro, cep) 
                    VALUES (?, ?, ?, ?, ?)";
    $stmtEndereco = $conn->prepare($sqlEndereco);
    $stmtEndereco->bind_param("issss", $usuarios_idusuarios, $rua, $numero, $bairro, $cep);

    if ($stmtEndereco->execute()) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['usuario_id'] = $usuarios_idusuarios;
        $_SESSION['nome'] = $nome;
        $_SESSION['usuario_email'] = $email;
        $_SESSION['telefone'] = $telefone;
        $_SESSION['usuario_tipo'] = $tipo;
        $_SESSION['idusuarios'] = $usuarios_idusuarios;
        $_SESSION['loggedin'] = true;

        header("Location: ../views/cadastro.php?status=sucesso");
        exit();
    } else {
        echo "Erro ao cadastrar endereço: " . $stmtEndereco->error;
    }

    $stmtEndereco->close();

} else {
    echo "Erro ao cadastrar usuário: " . $stmtUsuario->error;
}

$stmtUsuario->close();
$conn->close();
?>