<?php
session_start(); // Inicia a sessão
require_once __DIR__ . '/../init.php'; // Inclui o arquivo de inicialização

// Verifica se o usuário está logado
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../views/index.php");
    exit;
}

// Consulta a tabela de usuários do tipo cliente e ordena por ordem alfabética com ORDER BY nome ASC.
$sql = "SELECT * FROM usuarios WHERE tipo = 'cliente' ORDER BY nome ASC";
$result = mysqli_query($conn, $sql);

$clientes = [];
if ($result && mysqli_num_rows($result) > 0) {
    $key = base64_decode($_ENV['CHAVE_CPF'] ?? '');
    if ($key === false) die('Erro: chave CPF inválida.');

    while ($row = mysqli_fetch_assoc($result)) {
        $dec = openssl_decrypt($row['cpf'], 'AES-128-ECB', $key);
        if ($dec === false) {
            error_log('Falha ao descriptografar CPF para ID '.$row['idusuarios']);
            $dec = '[Erro ao descriptografar]';
        }
        $row['cpf'] = $dec;
        $clientes[] = $row;
    }

    // while ($row = mysqli_fetch_assoc($result)) {
    //     $clientes[] = $row;
    // }
}

$sqlTotalClientes = "SELECT COUNT(*) AS total FROM usuarios WHERE tipo = 'cliente'";
$resultTotalClientes = mysqli_query($conn, $sqlTotalClientes);
$totalClientes = 0;

if ($resultTotalClientes && $rowTotalClientes = mysqli_fetch_assoc($resultTotalClientes)) {
    $totalClientes = $rowTotalClientes['total'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Clientes</title>
    <link rel="stylesheet" href="../public/css/tabelas.css">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-white d-flex flex-column" style="min-height: 100vh;">

    <!-- Header -->
    <header class="d-flex justify-content-between align-items-center px-5 py-3 shadow-sm"
        style="background-color: #009bbf; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; height: 120px;">
        <div>
            <h4 class="text-white m-0">
                Olá,
                <span class="fw-bold">Administrador</span>
            </h4>
            <small class="text-white" style="font-size: 0.9rem;">Seja bem-vindo!</small>
        </div>
    </header>

    <!-- Main -->
    <main class="flex-grow-1 py-4">
        <h4 class="text-center fw-semibold mb-4" style="color: #444;">Clientes</h4>

        <!-- Barra de pesquisa (sem funcionalidade por enquanto) -->
        <div class="container mb-4 d-flex justify-content-center">
            <div class="input-group w-50">
                <input type="text" id="campoBusca" class="form-control" placeholder="Pesquisar cliente..." aria-label="Pesquisar cliente">
                <button id="botaoBusca" class="btn" type="button" style="background-color: #00a3c7; color: white;">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
        
        <div class="container px-3">
            <!-- Exibe o total de clientes cadastrados -->
            <div class="d-flex justify-content-end pe-3 mb-2">
                <small class="text-muted" style="font-size: 0.85rem; opacity: 0.7;">
                    Total de clientes cadastrados: <?= $totalClientes ?>
                </small>
            </div>
            <!-- Tabela para desktop -->
            <div class="table-responsive d-none d-md-block">
                <table class="table text-white align-middle">
                    <thead class="text-nowrap text-center" style="background-color: #00a3c7; border-radius: 10px;">
                        <tr>
                            <th style="border-radius: 10px 0 0 10px;">Nome</th>
                            <th>E-mail</th>
                            <th>Telefone</th>
                            <th>CPF</th>
                            <th style="border-radius: 0 10px 10px 0;">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td><?= htmlspecialchars($cliente['nome']) ?></td>
                            <td><?= htmlspecialchars($cliente['email']) ?></td> <!-- ✅ Aqui o e-mail -->
                            <td>
                                <a href="https://wa.me/55<?= preg_replace('/\D/', '', $cliente['telefone']) ?>"
                                    target="_blank" style="color: #00a3c7; text-decoration: none;">
                                    <?= htmlspecialchars($cliente['telefone']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($cliente['cpf']) ?></td>
                            <td>
                                <i class="bi bi-card-text" role="button" style="color: #00a3c7;" data-bs-toggle="modal"
                                    data-bs-target="#modalDetalhes"
                                    data-cliente='<?= json_encode($cliente, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'></i>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Cards para mobile -->
            <div class="d-md-none">
                <?php foreach ($clientes as $cliente): ?>
                <div class="card text-white mb-3" style="background-color: #00a3c7; border-radius: 12px;">
                    <div class="card-body position-relative">
                        <p><strong>Nome:</strong> <?= htmlspecialchars($cliente['nome']) ?></p>

                        <p><strong>E-mail:</strong> <?= htmlspecialchars($cliente['email']) ?></p>

                        <p><strong>Telefone:</strong> 
                            <a href="https://wa.me/55<?= preg_replace('/\D/', '', $cliente['telefone']) ?>" target="_blank" style="color: white; text-decoration: underline;">
                                <?= htmlspecialchars($cliente['telefone']) ?>
                            </a>
                        </p>
                        <p><strong>CPF:</strong> <?= htmlspecialchars($cliente['cpf']) ?></p>

                        <div class="position-absolute top-0 end-0 m-2">
                            <!-- <i class="bi bi-pencil-square me-2" role="button"></i> -->
                            <i class="bi bi-card-text" role="button"
                               data-bs-toggle="modal" data-bs-target="#modalDetalhes"
                               data-cliente='<?= json_encode($cliente, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'></i>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Botão Voltar -->
            <div class="d-flex justify-content-center my-4 fixed-bottom">
                <a href="../views/dashboard_admin.php"
                   class="btn d-flex align-items-center justify-content-center px-5 w-50 me-2"
                   style="background-color: #00a3c7; color: white; border-radius: 10px; height: 55px;
                   box-shadow: 0 0 5px rgba(0, 0, 0, 0.50); font-weight: 500;">
                    Voltar
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center py-3 small" style="color: #bbb;">
        &copy; <?= date('Y') ?> Embelezamento Automotivo. Todos os direitos reservados.
    </footer>

    <!-- Modal Detalhes -->
    <div class="modal fade" id="modalDetalhes" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 15px; background-color: #009bbf;">
                <div class="modal-body px-4 py-4 text-white">
                    <h5 class="text-center fw-bold mb-4">Dados do Cliente</h5>
                    <div class="border p-2 mb-4 rounded">
                        <p><strong>Nome:</strong> <span id="modalNome"></span></p>
                        <!-- <p><strong>Telefone:</strong> <span id="modalTelefone"></span></p> -->
                        <p><strong>Telefone:</strong> 
                            <a id="modalTelefone" href="#" target="_blank" style="color: #fff; text-decoration: underline;"></a>
                        </p>
                        <p><strong>E-mail:</strong> <span id="modalEmail"></span></p>
                        <p><strong>CPF:</strong> <span id="modalCpf"></span></p>
                    </div>
                    <button type="button" class="btn w-100 mt-5"
                            style="background-color: white; color: #444; border-radius: 10px; height: 55px;
                                   box-shadow: 0 0 5px rgba(0,0,0,0.50); font-weight: 500;"
                            data-bs-dismiss="modal">
                        Voltar
                    </button>
                </div>
            </div>
        </div>
    </div>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- script para buscar clientes -->
 <script src="../public/js/buscar_clientes.js"></script>

<!-- Modal Script -->
<script>
    const modal = document.getElementById('modalDetalhes');
    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const data = JSON.parse(button.getAttribute('data-cliente'));

        document.getElementById('modalNome').textContent = data.nome;
        // document.getElementById('modalTelefone').textContent = data.telefone;
        const telefoneEl = document.getElementById('modalTelefone');
        const numeroLimpo = data.telefone.replace(/\D/g, ''); // remove espaços, traços etc
        telefoneEl.href = `https://wa.me/55${numeroLimpo}`;
        telefoneEl.textContent = data.telefone;

        document.getElementById('modalEmail').textContent = data.email;
        document.getElementById('modalCpf').textContent = data.cpf;
    });
</script>

</body>
</html>