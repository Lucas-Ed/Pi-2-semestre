<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/tabelas.css">
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

        <a href="../controllers/logout.php" class="text-white text-decoration-none small d-flex align-items-center">
            <i class="bi bi-power me-1"></i> Sair
        </a>
    </header>

    <!-- Main -->
    <main class="flex-grow-1 py-4">
        <h5 class="text-center fw-semibold mb-4" style="color: #444;">Clientes</h5>


        <!-- NESSA BARRA DE PESQUISA O LAVARAPIDO VAI BUSCAR O CLIENTE PELO NOME, NUMERO OU CPF, NADA ALEM DISSO! -->
        <!-- Barra de pesquisa -->
        <div class="container mb-4 d-flex justify-content-center">
            <div class="input-group w-50">
                <input type="text" class="form-control" placeholder="Pesquisar cliente..."
                    aria-label="Pesquisar cliente">
                <button class="btn" type="button" style="background-color: #00a3c7; color: white;">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>

        <div class="container px-3">
            <!-- Tabela para desktop -->
            <div class="table-responsive d-none d-md-block">
                <table class="table text-white align-middle">
                    <thead class="text-nowrap text-center">
                        <tr>
                            <th style="border-radius: 10px 0 0 10px;">Nome</th>
                            <th>Telefone</th>
                            <th>CPF</th>
                            <th  style="border-radius: 0 10px 10px 0;">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <?php for ($i = 0; $i < 4; $i++): ?>
                        <tr>
                            <td>Eliabe Leme</td>
                            <td>(19) 94036-5620</td>
                            <td>536.251.659-02</td>
                            <td>
                                <i class="bi bi-card-text" role="button" data-bs-toggle="modal"
                                data-bs-target="#modalDetalhes" style="color: #00a3c7;"></i>
                            </td>
                        </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>



            <!-- Cards para mobile -->
            <div class="d-md-none">
                <?php for ($i = 0; $i < 4; $i++): ?>
                <div class="card text-white mb-3" style="background-color: #00a3c7; border-radius: 12px;">
                    <div class="card-body position-relative">
                        <p><strong>Nome:</strong> Eliabe Leme</p>
                        <p><strong>Telefone:</strong> (19) 94036-5620</p>
                        <p><strong>CPF:</strong> 536.251.659-02 </p>
                        <div class="position-absolute top-0 end-0 m-2">
                            <i class="bi bi-pencil-square me-2" role="button"></i>
                            <i class="bi bi-card-text" role="button"></i>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>

            <!-- Botão Voltar -->
            <div class="d-flex justify-content-center my-4">
                <a href="../views/dashboard_admin.php"
                    class="btn d-flex align-items-center justify-content-center px-5 w-50 me-2"
                    style="background-color: #00a3c7; color: white; border-radius: 10px; height: 55px; box-shadow: 0 0 5px rgba(0, 0, 0, 0.50); font-weight: 500;">
                    Voltar
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center py-3 small" style="color: #bbb;">
        &copy; <?= date('Y') ?> Seu Sistema
    </footer>

    <!-- Modal Detalhes -->
    <div class="modal fade" id="modalDetalhes" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 15px; background-color: #009bbf;">
                <div class="modal-body px-4 py-4 text-white">

                    <h5 class="text-center fw-bold mb-4">Dados do Cliente</h5>

                    <div class="border p-2 mb-4 rounded">
                        <p><strong>Nome:</strong> Eliabe Leme</p>
                        <p><strong>Telefone:</strong> 19 94022-5623</p>
                        <p><strong>E-mail:</strong> Eliabeleme@gmail.com</p>
                        <p><strong>CPF:</strong> 536.251.659.02</p>
                        <p><strong>CEP:</strong> 13604-528</p>
                        <p><strong>Rua:</strong> João Cardoso</p>
                        <p><strong>Número:</strong> 187</p>
                        <p><strong>Bairro:</strong> Jardim Candida</p>
                    </div>

                    <div class="mb-3">
                        <h6>Caso queira buscar o veículo...</h6>

                        <div class="d-flex justify-content-between">
                            <a href="https://waze.com/ul?ll=LAT,LNG&navigate=yes" target="_blank"
                                class="btn d-flex align-items-center justify-content-center"
                                style="width: 45%; border-radius: 10px; background-color: white; color: #009bbf; font-weight: 500; padding: 10px;">
                                <img src="../public/uploads/img/waze.svg" alt="waze" class="me-2" width="20">
                                Waze
                            </a>

                            <a href="https://www.google.com/maps/search/?api=1&query=LAT,LNG" target="_blank"
                                class="btn d-flex align-items-center justify-content-center"
                                style="width: 45%; border-radius: 10px; background-color: white; color: #009bbf; font-weight: 500; padding: 10px;">
                                <i class="bi bi-geo-alt me-2 fs-5"></i>Maps
                            </a>
                        </div>
                    </div>


                    <button type="button" class="btn w-100 mt-5" style="background-color: white; color: #444; border-radius: 10px; height: 55px; 
                       box-shadow: 0 0 5px rgba(0,0,0,0.50); font-weight: 500;" data-bs-dismiss="modal">
                        Voltar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>