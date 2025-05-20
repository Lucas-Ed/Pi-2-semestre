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
unset($_SESSION['csrf_token']);

// Conectar ao banco de dados
$servername = $_ENV["DB_HOST"];
$dbUsername = $_ENV["DB_USER"];
$dbPassword = $_ENV["DB_PASS"];
$database = $_ENV["DB_NAME"];

$conn = new mysqli($servername, $dbUsername, $dbPassword, $database);

// Verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Captura os dados do formulário
$nome = $_POST["nome"];
$email = $_POST["email"];
$senha_cap = $_POST["senha"]; // Captura a senha
$senha_cap_conf = $_POST["confirma_senha"]; // Captura a confirmação da senha
$senhaHash = password_hash($_POST["senha"], PASSWORD_DEFAULT); // Criptografa a senha
$cpf = $_POST["cpf"];
$telefone = $_POST["telefone"];
$rua = $_POST["rua"];
$numero = $_POST["numero"];
$bairro = $_POST["bairro"];
$cep = $_POST["cep"];
$termos = isset($_POST["termos"]) ? 1 : 0;
$tipo = 'cliente';


// Verifica se o e-mail contém ".com" ou ".com.br"
if (!str_contains($email, '.com') && !str_contains($email, '.com.br')) {
    header("Location: ../views/cadastro.php?erro=email_invalido");
    exit();
}

// Verifica se a senha e a confirmação  de senha são iguais
if ($senha_cap !== $senha_cap_conf) {
    // echo "Erro: As senhas não coincidem!";

    header("Location: ../views/cadastro.php?erro=senha");
    $conn->close();
    exit();
} else{
        // Verificar se o e-mail ou cpf já existem
        $sqlCheck = "SELECT idusuarios FROM usuarios WHERE email = ? OR cpf = ?";
        $stmtCheck = $conn->prepare($sqlCheck);

        if ($stmtCheck === false) {
            die("Erro na preparação da verificação de usuário: " . $conn->error);
        }

        $stmtCheck->bind_param("ss", $email, $cpf);
        $stmtCheck->execute();
        $stmtCheck->store_result();

        if ($stmtCheck->num_rows > 0) {
            // E-mail ou CPF já existem
            // echo "Erro: E-mail ou CPF já cadastrado!";
            header("Location: ../views/cadastro.php?erro=email_ou_cpf");
            $stmtCheck->close();
            $conn->close();
            exit();
        }
        $stmtCheck->close();

        // 1. Inserir o usuário
        $sqlUsuario = "INSERT INTO usuarios (nome, email, senha, cpf, telefone, termos, tipo) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmtUsuario = $conn->prepare($sqlUsuario);

        if ($stmtUsuario === false) {
            die("Erro na preparação do cadastro de usuário: " . $conn->error);
        }

        $stmtUsuario->bind_param("sssssis", $nome, $email, $senhaHash, $cpf, $telefone, $termos, $tipo);

        if ($stmtUsuario->execute()) {
            $usuarios_idusuarios = $conn->insert_id;

            // 2. Inserir o endereço
            $sqlEndereco = "INSERT INTO enderecos (usuarios_idusuarios, rua, numero, bairro, cep) 
                            VALUES (?, ?, ?, ?, ?)";
            $stmtEndereco = $conn->prepare($sqlEndereco);

            if ($stmtEndereco === false) {
                die("Erro na preparação do cadastro de endereço: " . $conn->error);
            }

            $stmtEndereco->bind_param("issss", $usuarios_idusuarios, $rua, $numero, $bairro, $cep);

            if ($stmtEndereco->execute()) {
                // header("Location: cadastrado.php?status=sucesso");
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
} // fechando o else
?>