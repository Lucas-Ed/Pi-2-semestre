<?php
session_start();
// if (session_status() !== PHP_SESSION_ACTIVE) {
//     die("Sessão não iniciada!");
// }

// Certifique-se de que init.php foi incluído ANTES deste arquivo.
require_once '../init.php'; // Se este arquivo for o ponto de entrada

require_once BASE_PATH . '/model/config.php';
// require_once "../config/config.php"; // Inclui a conexão com o banco de dados.

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['nome']) && !empty($_POST['senha'])) {
        $username = trim($_POST['nome']);
        $password = trim($_POST['senha']);

        // Prepara a consulta SQL para evitar SQL Injection
        $sql = "SELECT idusuarios, nome, senha, tipo FROM usuarios WHERE nome = ?"; // tipo
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $db_username, $db_password, $db_tipo);  // , $db_tipo
            $stmt->fetch();
            //echo $db_password;
            // Verifica se a senha está correta
            if (password_verify($password, $db_password)) {
                session_regenerate_id(true); // Regerar a sessão ao logar,Proteção (regenerando session ID)
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['idusuarios'] = $id;
                $_SESSION['nome'] = $db_username;
                $_SESSION['tipo'] = $db_tipo; // Salva o tipo na sessão também
                
                // Redireciona conforme o tipo.
                if ($db_tipo === 'admin') {
                    header("location: ../views/admin_agendamentos.php");
                } else {
                header("location: ../views/welcome.php");
                }
                exit();
            } else {
                $_SESSION['loggedin'] = FALSE;
                $error = "Usuário ou senha inválidos.";
            }
        } else {
            $_SESSION['loggedin'] = FALSE;
            $error = "Usuário ou senha inválidos.";
        }

        $stmt->close();
        $conn->close();
    } else {
        $error = "Por favor, preencha todos os campos.";
    }
}

// Gera o token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>


<!DOCTYPE html>
<html lang="pt_BR">
<head>
    <meta charset="UTF-8">
    <title>Acessar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
<div class="wrapper">
    <a href="../views/index.php" class="btn btn-danger">Voltar</a>
    <br><br>
    <h2>Acessar</h2>
    <p>Favor inserir login e senha.</p>

    <!-- Exibição de erro, apenas após submissão real do form -->
    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login']) && !empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label>Nome</label>
            <input type="text" name="nome" class="form-control" required>
            <span class="help-block"></span>
        </div> 
        <br>   
        <div class="form-group">
            <label>Senha</label>
            <input type="password" name="senha" class="form-control" autocomplete="off" required> <!-- autocomplete="off" para evitar preenchimento
             automático, e required para  impedir o envio do formulário se o campo estiver vazio.  -->
            <span class="help-block"></span>
            <br>
            <p> esqueceu a senha? <a href="recuperar_senha.php" class="btn btn-primary">Clique aqui</a>.</p>
        </div>
        <br>
        <div class="form-group">
            <input type="submit" name="login" class="btn btn-primary" value="Acessar">
        </div>
        <div class="form-group">
            <p>Não tem uma conta? <a href="cadastro.php" class="btn btn-primary">Cadastre-se aqui</a>.</p>
        </div>
    </form>
</div>

</body>
</html>