<!DOCTYPE html>
<html lang="pt_BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <title>Document</title>
</head>
<body>
    <h1>Listagem de agendamentos</h1>
        <br>
        <br>
        <a href="../controllers/logout.php" class="btn btn-danger">Sair da conta</a>
        <br>
        <br>
        <a href="../views/admin_usuarios.php" class="btn btn-danger">Ver clientes</a>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Data</th>
                <th>Hora</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($agendamentos as $agendamento): ?>
                <tr>
                    <td><?php echo $agendamento['id']; ?></td>
                    <td><?php echo $agendamento['nome']; ?></td>
                    <td><?php echo $agendamento['data']; ?></td>
                    <td><?php echo $agendamento['hora']; ?></td>
                    <td><a href="editar.php?id=<?php echo $agendamento['id']; ?>">Editar</a> | 
                        <a href="deletar.php?id=<?php echo $agendamento['id']; ?>">Deletar</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>