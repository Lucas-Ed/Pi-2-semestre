<?php
session_start();
require_once "config.php"; // Inclui a conexão com o banco de dados

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        // Prepara a consulta SQL para evitar SQL Injection
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $db_username, $db_password);
            $stmt->fetch();

            // Verifica se a senha está correta
            if (password_verify($password, $db_password)) {
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['id'] = $id;
                $_SESSION['username'] = $db_username;

                header("location: welcome.php");
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
?>


<!DOCTYPE html>
<html lang="pt_BR">
<head>
    <meta charset="UTF-8">
    <title>Acessar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Acessar</h2>
        <p>Favor inserir login e senha.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="Lucas eduardo rosolem">
                <span class="help-block"></span>
            </div> 
            <br>   
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="123456"> 
                <span class="help-block"></span>
            </div>
            <br>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Acessar">
            </div>
            <div class="form-group">
                <p>Não tem uma conta? <a href="cadastro.php" class="btn btn-primary">Cadastre-se aqui</a>.</p>
                <!-- <p>Esqueceu a senha? <a href="recuperar.php" class="btn btn-primary">Recuperar senha</a>.</p> -->
            </div >
        </form>
    </div>    
</body>
</html>