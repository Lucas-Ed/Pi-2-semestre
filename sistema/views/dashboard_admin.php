<?php
session_start(); // Inicia a sessão
require_once __DIR__ . '/../init.php'; // Inclui o arquivo de inicialização

// Contar agendamentos ativos
//$queryAgendamentos = "SELECT COUNT(*) AS total FROM agendamentos"; // busca todos os agendamentos
$queryAgendamentos = "SELECT COUNT(*) AS total FROM agendamentos WHERE data_agendamento = CURDATE()"; // busca todos os agendamentos do dia atual
$resultAgendamentos = $conn->query($queryAgendamentos);
$agendamentosAtivos = $resultAgendamentos->fetch_assoc()['total'] ?? 0;

// Contar clientes cadastrados
$queryUsuarios = "SELECT COUNT(*) AS total FROM usuarios WHERE tipo != 'admin'";
$resultUsuarios = $conn->query($queryUsuarios);
$totalUsuarios = $resultUsuarios->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Dashboard</title>

    <!-- Icons bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" href="../public/uploads/img/favicon.svg" type="image/svg+xml">
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="../public/css/dashboard_admin.css">
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
        <h5 class="text-center fw-semibold mb-4" style="color: #444;">Dashboard</h5>

        <div class="container">
            <div class="d-flex flex-wrap gap-3 justify-content-start">

                <!-- Card: Agendamentos -->
                <div style="min-width: 380px; flex: 1 1 auto;">
                    <a href="../views/admin_agendamentos.php" style="text-decoration: none;">
                        <div class="d-flex justify-content-between align-items-center shadow-sm px-3 py-2"
                            style="height: 75px; border: 1px solid #e0e0e0; border-radius: 10px; background-color: #fff;">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar3 me-3" style="font-size: 2rem; color: #009bbf;"></i>
                                <div class="text-start">
                                    <h6 class="mb-0 fw-bold" style="color: #333;">Agendamentos</h6>
                                    <small class="text-muted" style="font-size: 13px;">
                                        <!-- mostra os agendamentos ativos do dia -->
                                        <?= $agendamentosAtivos ?> agendamento<?= $agendamentosAtivos != 1 ? 's' : '' ?> ativo<?= $agendamentosAtivos != 1 ? 's' : '' ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Card: Clientes -->
                <div style="min-width: 380px; flex: 1 1 auto;">
                    <a href="../views/admin_usuarios.php" style="text-decoration: none;">
                        <div class="d-flex justify-content-between align-items-center shadow-sm px-3 py-2"
                            style="height: 75px; border: 1px solid #e0e0e0; border-radius: 10px; background-color: #fff;">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-lines-fill me-3" style="font-size: 2rem; color: #009bbf;"></i>
                                <div class="text-start">
                                    <h6 class="mb-0 fw-bold" style="color: #333;">Clientes</h6>
                                    <small class="text-muted" style="font-size: 13px;">
                                        <?= $totalUsuarios ?> Cliente<?= $totalUsuarios != 1 ? 's' : '' ?> cadastrado<?= $totalUsuarios != 1 ? 's' : '' ?>
                                    </small>
                                </div>
                            </div>
                            <span style="width: 45px; height: 45px;"></span>
                        </div>
                    </a>
                </div>

            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="text-center py-3 small" style="color: #bbb;">
        &copy; <?= date('Y') ?>  Embelezamento Automotivo. Todos os direitos reservados.
    </footer>

    <!-- Bootstrap script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

