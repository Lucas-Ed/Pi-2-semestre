<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
// if (session_status() !== PHP_SESSION_ACTIVE) {
//     die("Sessão não iniciada!");
// }

// Certifique-se de que init.php foi incluído ANTES deste arquivo.
require_once '../init.php'; // Se este arquivo for o ponto de 
//require_once BASE_PATH . '/model/db.php';
// require_once "../config/config.php"; // Inclui a conexão com o banco de dados.

if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Verificação do token CSRF
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Token CSRF inválido.");
    }
    if (!empty($_POST['email']) && !empty($_POST['senha'])) {
        $username = trim($_POST['email']);
        $password = trim($_POST['senha']);

        // Prepara a consulta SQL para evitar SQL Injection
        $sql = "SELECT idusuarios, email, nome, senha, tipo, telefone FROM usuarios WHERE email = ?"; // tipo
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $db_email, $db_username, $db_password, $db_tipo, $db_telefone);  // , $db_tipo
            $stmt->fetch();
            //echo $db_password;
            // Verifica se a senha está correta
            if (password_verify($password, $db_password)) {
                session_regenerate_id(true); // Regerar a sessão ao logar,Proteção (regenerando session ID)
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['idusuarios'] = $id;
                $_SESSION['nome'] = $db_username;
                $_SESSION['email'] = $db_email; // Adiciona o e-mail à sessão
                $_SESSION["telefone"] = $db_telefone; // Adiciona o telefone à sessão
                $_SESSION['tipo'] = $db_tipo; // Salva o tipo na sessão também
                
                // Define controle para alerta e redirecionamento
                $redirectUrl = ($db_tipo === 'admin') ? '../views/dashboard_admin.php' : '../views/dashboard_user.php';
                $_SESSION['login_success'] = true;
                $_SESSION['redirect_url'] = $redirectUrl;

                // Redireciona para o mesmo arquivo (evita reenvio do POST)
                header("Location: " . $_SERVER['PHP_SELF']);
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
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Acessar</title>

    <!-- Icons bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" href="../public/uploads/img/favicon.svg" type="image/svg+xml">
</head>


<!-- Inicio do body -->
<body class="bg-white d-flex flex-column min-vh-100 position-relative">

    <!-- Botão Sair -->
    <a href="index.php" class="position-absolute top-0 end-0 m-5 text-decoration-none text-secondary small">
        <i class="bi bi-power"></i> Sair
    </a>

    <!-- Conteúdo central -->
    <main class="d-flex flex-grow-1 align-items-center justify-content-center px-3">
        <div style="width: 100%; max-width: 400px;">
            <h4 class="text-center fw-bold mb-5" style="color: #444;">Login</h4>


            <!-- Exibição de erro, apenas após submissão real do form -->
            <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login']) && !empty($error)): ?>
                <!-- <div class="alert alert-danger"><php echo $error; ?></div> -->
                 <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <p class="text-muted mb-2 small">Insira seus dados...</p>
                <!-- Campo CSRF. envia via post, input oculto -->
                 <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <!-- Campo Nome -->
                <!-- <div class="mb-3">
                    <div class="d-flex align-items-center"
                        style="border: 2px solid #00a3c7; border-radius: 10px; padding: 0 1rem; height: 55px;">
                        <i class="bi bi-person-fill" style="color: #00a3c7; font-size: 1.2rem;"></i>
                        <input type="text" name="nome" class="form-control border-0 shadow-none placeholder-light"
                            placeholder="Nome completo"
                            style="margin-left: 0.75rem; font-size: 1rem; color: #444; height: 100%; line-height: 1.5; padding: 0;" required>
                    </div>
                </div> -->

                <!-- Campo E-mail -->
                <div class="mb-3">
                    <div class="d-flex align-items-center"
                        style="border: 2px solid #00a3c7; border-radius: 10px; padding: 0 1rem; height: 55px;">
                        <i class="bi bi-envelope-fill" style="color: #00a3c7; font-size: 1.2rem;"></i>
                        <input type="email" name="email" class="form-control border-0 shadow-none placeholder-light"
                            placeholder="E-mail"
                            style="margin-left: 0.75rem; font-size: 1rem; color: #444; height: 100%; line-height: 1.5; padding: 0;" required>
                    </div>
                </div>


                <!-- Campo Senha -->
                <div class="mb-3">
                    <div class="d-flex align-items-center"
                        style="border: 2px solid #00a3c7; border-radius: 10px; padding: 0 1rem; height: 55px;">
                        <i class="bi bi-key" style="color: #00a3c7; font-size: 1.2rem;"></i>
                        <input type="password" name="senha" class="form-control border-0 shadow-none placeholder-light"
                            placeholder="Senha"
                            style="margin-left: 0.75rem; font-size: 1rem; color: #444; height: 100%; line-height: 1.5; padding: 0;" required>
                    </div>
                </div>

                <!-- Botão Entrar -->
    <div class="d-grid mb-3">
        <button type="submit" name="login" class="btn text-white d-flex align-items-center justify-content-center"
            style="background-color: #009bbf; border-radius: 10px; height: 55px;">
            Entrar
        </button>
    </div>

                <!-- Link Esqueci a senha -->
                <div class="text-start mb-4">
                    <small class="text-muted" style="font-size: 0.85rem;">
                        Esqueceu a senha?
                        <a href="../views/recovery.php"
                            class="text-decoration-none" style="color: #009bbf;">Recuperar senha</a>
                    </small>
                </div>
            </form>
        </div>
    </main>

    <!-- Rodapé -->
    <footer class="text-white text-center py-5"
        style="background-color: #009bbf; border-top-left-radius: 20px; border-top-right-radius: 20px;">
        <small>
            Não tem uma conta?
            <a href="../views/cadastro.php" class="text-white fw-bold text-decoration-underline">Cadastre-se</a>
        </small>
    </footer>
            

    <!-- lib sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if (!empty($_SESSION['login_success'])): ?>
    <script>
        Swal.fire({
            icon: "success",
            title: "Login feito com sucesso!",
            showConfirmButton: false,
            timer: 2000
        }).then(() => {
            window.location.href = "<?php echo $_SESSION['redirect_url']; ?>";
        });
    </script>
    <?php
        // Limpa as variáveis para não repetir na próxima vez
        unset($_SESSION['login_success']);
        unset($_SESSION['redirect_url']);
    ?>
<?php endif; ?>

</body>
</html>
