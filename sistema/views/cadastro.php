<?php
require_once '../init.php'; // <-- Inicia sessão

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.7-beta.29/inputmask.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container mt-5">
        <a href="../views/index.php" class="btn btn-danger">Voltar</a>
        <br><br>
        <h2>Cadastro de Usuário</h2>
        <p>Favor inserir os dados.</p>

        <form action="../controllers/processa.php" method="post">
        <!-- Token CSRF -->
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" required>
            </div>  

            <div class="mb-3">
                <label class="form-label">E-mail</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Senha</label>
                <input type="password" name="senha" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">CPF</label>
                <input type="text" name="cpf" id="cpf" class="form-control" required>
            </div>


            <div class="mb-3">
                <label class="form-label">Telefone</label>
                <input type="tel" name="telefone" id="telefone" class="form-control" required>
            </div>

            <h4 class="mt-4">Endereço</h4>
            <div class="mb-3">
                <label class="form-label">Rua</label>
                <input type="text" name="rua" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Número</label>
                <input type="text" name="numero" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Bairro</label>
                <input type="text" name="bairro" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">CEP</label>
                <input type="text" name="cep" id="cep" class="form-control" required>
            </div>
 
            <div class="mb-3 form-check">
                <input type="checkbox" name="termos" class="form-check-input" id="termos" required>
                <label class="form-check-label" for="termos">
                    Eu aceito os <a href="termos.html" target="_blank">Termos de Uso</a>
                </label>
            </div>

            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
        <br><br>
    </div>
<!-- // alert cadastro feito com sucesso -->
<?php if (isset($_GET['status']) && $_GET['status'] == 'sucesso'): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Cadastro realizado!',
                text: 'Seu cadastro foi concluído com sucesso.',
                confirmButtonText: 'OK'
            }).then(() => {
                // Remove o parâmetro da URL depois que o usuário fecha o alerta
                history.replaceState(null, '', window.location.pathname);
                window.location.href = '../views/index.php'; // Redireciona após o OK

            });
        </script>
<?php endif; ?>
<!-- // alert de erro de email ou cpf já cadastrado -->
<?php if (isset($_GET['erro']) && $_GET['erro'] == 'email_ou_cpf'): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'E-mail ou CPF já cadastrado!',
                confirmButtonText: 'OK'
            }).then(() => {
                // Remove o parâmetro da URL depois que o usuário fecha o alerta
                history.replaceState(null, '', window.location.pathname);
            });
        </script>
<?php endif; ?>
<!-- Mascaras para os campos de CPF, Telefone e CEP -->
    <script>
        $(document).ready(function(){
            Inputmask({"mask": "999.999.999-99"}).mask("#cpf");
            Inputmask({"mask": "(99) 99999-9999"}).mask("#telefone");
            Inputmask({"mask": "99999-999"}).mask("#cep");
        });
    </script>
</body>
</html>