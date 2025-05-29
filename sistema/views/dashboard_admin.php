<?php
// Chama o componente de cabeçalho da página
require_once __DIR__ . '/components/header.php'; 
?>

<title>Dashboard</title>
<!-- CSS personalizado -->
<link rel="stylesheet" href="../public/css/dashboard_admin.css">

<section class="bg-white d-flex flex-column" style="min-height: 100vh;">

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
                                    <small class="text-muted" style="font-size: 13px;">1 agendamento ativo</small>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Card: Clientes -->
                <div style="min-width: 380px; flex: 1 1 auto;">
                    <a href="../views/clientes.php" style="text-decoration: none;">
                        <div class="d-flex justify-content-between align-items-center shadow-sm px-3 py-2"
                            style="height: 75px; border: 1px solid #e0e0e0; border-radius: 10px; background-color: #fff;">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-lines-fill me-3" style="font-size: 2rem; color: #009bbf;"></i>
                                <div class="text-start">
                                    <h6 class="mb-0 fw-bold" style="color: #333;">Clientes</h6>
                                    <small class="text-muted" style="font-size: 13px;">187 Clientes cadastrados</small>
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
</section>


    <!-- Bootstrap script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

