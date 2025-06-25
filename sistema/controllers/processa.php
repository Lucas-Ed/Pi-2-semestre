<?php
// Controlador para cadastro de usuário.
require '../Vendor/autoload.php'; //Carrega o autoload do Composer.
require_once '../init.php'; // Inicializa o ambiente

// Ativar exceptions em erros do mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Iniciar sessão, caso ainda não esteja iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
$senha = $_POST["senha"];
$senha_confirma = $_POST["confirma_senha"];
$termos = isset($_POST["termos"]) ? 1 : 0;
$tipo = 'cliente';

// Salva os dados preenchidos na sessão (exceto senha)
// Essa sessão será usada para manter os dados no formulário em caso de erro
$_SESSION['form_data'] = [
    'nome' => $nome,
    'email' => $email,
    'termos' => $termos ? 'on' : ''
];

// Validações com retorno de erros via sessão
$erros = [];

//  Validações

// E-mail
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    //header("Location: ../views/cadastro.php?erro=email_invalido");
    $erros[] = "E-mail inválido.";
    //exit();
}

// Senha (mínimo 8 caracteres, ao menos 1 letra e 1 número)
if (strlen($senha) < 8 || !preg_match('/[A-Za-z]/', $senha) || !preg_match('/[0-9]/', $senha)) {
    //header("Location: ../views/cadastro.php?erro=senha_fraca");
    $erros[] = "A senha deve ter pelo menos 8 caracteres, contendo letras e números.";
    //exit();
}

// Confirmação de senha
if ($senha !== $senha_confirma) {
    //header("Location: ../views/cadastro.php?erro=senha");
    $erros[] = "As senhas não coincidem.";
    //exit();
}

// Verifica se há erros
if (!empty($erros)) {
    $_SESSION['form_errors'] = $erros;
    header("Location: ../views/cadastro.php");
    exit();
}

// Criptografa a senha 
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

//  Verifica se e-mail já esta sendo usado 
$sqlCheck = "SELECT idusuarios FROM usuarios WHERE email = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("s", $email); //"ss" , $cpfCriptografado
$stmtCheck->execute();
$stmtCheck->store_result();

if ($stmtCheck->num_rows > 0) {
    $_SESSION['form_errors'] = ["E-mail já cadastrado."];
    //$_SESSION['form_data'] = $_POST; // opcional aqui, pois já fizemos antes
    header("Location: ../views/cadastro.php");
    $stmtCheck->close();
    $conn->close();
    exit();
}

//  Insere usuário
$sqlUsuario = "INSERT INTO usuarios (nome, email, senha, termos, tipo) 
               VALUES (?, ?, ?, ?, ?)"; // , ?, ?
$stmtUsuario = $conn->prepare($sqlUsuario);
$stmtUsuario->bind_param("sssss", $nome, $email, $senhaHash, $termos, $tipo); //"sssssis" $cpfCriptografado, $telefone
// Verifica se a inserção foi bem-sucedida, e se sim, inicia a sessão do usuário.
if ($stmtUsuario->execute()) {
    $usuarios_idusuarios = $conn->insert_id;

        // Login automático
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['usuario_id'] = $usuarios_idusuarios;
        $_SESSION['nome'] = $nome;
        $_SESSION['email'] = $email;
        $_SESSION['tipo'] = $tipo;
        $_SESSION['idusuarios'] = $usuarios_idusuarios;
        $_SESSION['loggedin'] = true;

        //  Limpa os dados do formulário da sessão após sucesso
        unset($_SESSION['form_data']);
        unset($_SESSION['form_errors']);

        header("Location: ../views/cadastro.php?status=sucesso");
        exit();

} else {
    echo "Erro ao cadastrar usuário: " . $stmtUsuario->error;
}

$stmtUsuario->close();
$conn->close();
?>