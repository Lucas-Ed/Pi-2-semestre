
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Cadastro de Usuário</h2>
        <p>Favor inserir os dados.</p>
        
        <form action="processa.php" method="post">
            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="name" class="form-control" required>
            </div>  

            <div class="mb-3">
                <label class="form-label">Senha</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
    </div>
</body>
</html>
