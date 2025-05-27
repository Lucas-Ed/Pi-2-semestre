<?php
session_start();
// Chama o componente de cabeçalho da página
require_once __DIR__ . '/components/header.php'; 

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../views/login.php");
    exit;
}

// Verifica se a sessão está ativa, caso contrário, redireciona para a página de login
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    session_unset();
    session_destroy();
    header("location: ../views/logout.php");
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();
?>

<!-- carrega o css da página -->
<link rel="stylesheet" href="../public/css/dashboard_user.css">

<section class="bg-white d-flex flex-column" style="min-height: 100vh;">

    <!-- Header -->
    <section class="d-flex justify-content-between align-items-center px-5 py-3 shadow-sm"
        style="background-color: #009bbf; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; height: 120px;">

        <div>
            <h4 class="text-white m-0">
                Olá,
                <?php
                $nomeCompleto = htmlspecialchars($_SESSION["nome"]);
                $nomes = explode(" ", $nomeCompleto);

                if (count($nomes) >= 2) {
                    $primeiroSegundoNome = $nomes[0] . " " . $nomes[1];
                    echo $primeiroSegundoNome;
                } else {
                    echo $nomeCompleto;
                }
            ?>
            </h4>
            <small class="text-white" style="font-size: 0.9rem;">Seja bem-vindo!</small>
        </div>

        <!-- Perfil e Sair -->
        <div class="d-flex flex-column align-items-end ms-auto">
            <a href="../views/perfil_user.php" class="text-white text-decoration-none small d-flex align-items-center mb-2">
                <i class="bi bi-person me-2"></i> <span class="fw-bold">Meu Perfil</span>
            </a>
            <a href="../controllers/logout.php" class="text-white text-decoration-none small d-flex align-items-center">
                <i class="bi bi-power me-2"></i> Sair
            </a>
        </div>
    </section>


    <!-- Main -->
    <main class="flex-grow-1 py-4">
        <h5 class="text-center fw-semibold mb-4" style="color: #444;">Dashboard</h5>

        <div class="container">
            <div class="d-flex flex-wrap gap-3 justify-content-start">

                <!-- Card: Meus Agendamentos -->
                <!-- <div style="min-width: 380px; flex: 1 1 auto;">
                    <button type="button"
                        class="d-flex justify-content-between align-items-center shadow-sm px-3 py-2 text-decoration-none"
                        style="display: block; width: 100%; height: 75px; border: 1px solid #e0e0e0; border-radius: 10px; background-color: #fff; cursor: pointer;"
                        data-bs-toggle="modal" data-bs-target="#modalAgendamento">

                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar3 me-3" style="font-size: 2rem; color: #009bbf;"></i>
                            <div class="text-start">    
                            <h6 class="mb-0 fw-bold" style="color: #333;">Meus Agendamentos</h6>

                            DEVESSE REALIZAR UMA CONSULTA DE AGENDAMENTO PELO ID DO CLIENTE PARA VERIFICAR QUANTOS AGENDAMENTO ELE TEM! ISSO DEVE MOSTRAR DE FORMA AUTOMATICA AQUI!
                                <small class="text-muted" style="font-size: 13px;">1 agendamento ativo</small>
                            </div>
                        </div>

                        <span style="width: 45px; height: 45px;"></span>
                    </button>
                </div> -->


                <!-- Card: Meus Veículos -->
                <!-- <div style="min-width: 380px; flex: 1 1 auto;">
                    <button type="button"
                        class="d-flex justify-content-between align-items-center shadow-sm px-3 py-2 text-decoration-none"
                        style="display: block; width: 100%; height: 75px; border: 1px solid #e0e0e0; border-radius: 10px; background-color: #fff; cursor: pointer;"
                        data-bs-toggle="modal" data-bs-target="#modalVeiculo">

                        <div class="d-flex align-items-center">
                            <i class="bi bi-car-front me-3" style="font-size: 2rem; color: #009bbf;"></i>
                            <div class="text-start">
                                <h6 class="mb-0 fw-bold" style="color: #333;">Meus Veículos</h6>
                                
                                 DEVESSE REALIZAR UMA CONSULTA DE AGENDAMENTO PELO ID DO CLIENTE PARA VERIFICAR QUANTOS VEÍCULOS ELE TEM! E ISSO DEVE MOSTRAR DE FORMA AUTOMATICA AQUI!
                                <small class="text-muted" style="font-size: 13px;">2 veículos cadastrados</small>
                            </div>
                        </div>

                        <span style="width: 45px; height: 45px;"></span>
                    </button>
                </div> -->

                <div class="container">
                <div class="row g-4">
                    <!-- Card de Veículos -->
                    <div class="col-12 col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body d-flex flex-column justify-content-between">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-2">
                            <div class="d-flex align-items-center">
                            <i class="bi bi-car-front me-2" style="font-size: 2rem; color: #009bbf;"></i>
                            <h2 class="h4 mb-0">Meus Veículos</h2>
                            </div>
                            <button id="addCarBtn" class="btn_add_vei">
                            <i class="bi bi-plus-lg"></i> Adicionar Veículo
                            </button>
                        </div>
                        <small class="text-muted" style="font-size: 13px;">2 veículos cadastrados</small>
                        <div id="carsList"></div>
                        </div>
                    </div>
                    </div>

                    <!-- Card de Agendamentos -->
                    <div class="col-12 col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <i class="bi bi-calendar3 me-3" style="font-size: 2rem; color: #009bbf;"></i>
                            <h2 class="h4 m-0">Agendamentos</h2>
                        </div>
                        <div id="appointmentsList"></div>
                        </div>
                    </div>
                    </div>
                </div>
                </div>

    </main>

    <!-- Footer -->
    <footer class="text-center py-3 small" style="color: #bbb;">
        &copy; <?= date('Y') ?> Embelezamento Automotivo. Todos os direitos reservados.
    </footer>

    <!-- ====================================================== -->

    <!-- O CAMPO DE SERVIÇO DEVE MUDAR DE ACORDO COM OS TIPO DE VEICULO DO CLIENTE! -->

    <!-- Modal: Agendamento -->
    <div class="modal fade" id="modalAgendamento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 15px; background-color: #009bbf;">
                <form action="../controllers/agendamento.php" method="POST">
                    <div class="modal-body px-4 py-4 text-white">
                        <h5 class="text-center fw-bold mb-4">Agendamento</h5>

                        <!-- MOSTRAR O NOME DO USÚARIO -->
                        <div class="mb-1 d-flex align-items-center gap-2 small">
                            <i class="bi bi-person"></i>
                            <p class="text-white m-0">
                                <?php
                                $nomeCompleto = htmlspecialchars($_SESSION["nome"]);
                                $nomes = explode(" ", $nomeCompleto);

                                if (count($nomes) >= 2) {
                                    $primeiroSegundoNome = "Nome: " . $nomeCompleto;
                                    echo $primeiroSegundoNome;
                                }
                                ?>
                            </p>
                        </div>

                        <!-- MOSTRAR O TELEFONE O USÚARIO -->
                        <div class="mb-4 d-flex align-items-center gap-2 small">
                            <i class="bi bi-telephone"></i>
                            <p class="text-white m-0">
                                <?php
                                $telefone = htmlspecialchars($_SESSION["telefone"]);

                                if (!empty($telefone)) {
                                    echo "Telefone: " . $telefone;
                                } else {
                                    echo "Telefone não disponível.";
                                }
                                ?>
                            </p>
                        </div>


                        <div class="mb-3">
                            <label class="form-label small">Veículo</label>
                            <select class="form-select" id="veiculo" name="veiculo" required
                                style="border-radius: 10px; height: 45px; font-size: 0.95rem; border: none;">
                                <option value="" disabled selected>Selecione o veículo</option>
                            </select>

                        </div>

                        <div class="mb-3">
                            <label class="form-label small">Serviço</label>
                            <select class="form-select" name="servico" required
                                style="border-radius: 10px; height: 45px; font-size: 0.95rem; border: none;">
                                <option selected disabled>Selecione o serviço</option>
                                <option>Lavagem</option>
                                <option>Revisão</option>
                            </select>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label class="form-label small">Data</label>
                                <input type="date" name="data" class="form-control" required
                                    style="border-radius: 10px; height: 45px; font-size: 0.95rem; border: none;">
                            </div>
                            <!-- <div class="col">
                                <label class="form-label small">Horário</label>
                                <input type="time" name="horario" class="form-control" required
                                    style="border-radius: 10px; height: 45px; font-size: 0.95rem; border: none;">
                            </div>
                        </div> -->
                        <div class="mb-3">
                        <label for="time" class="form-label small">Horário:</label>
                                <select class="form-select " id="time" required>
                                    <option value="">Selecione um horário</option>
                                    <option value="08:00">08:00</option>
                                    <option value="09:00">09:00</option>
                                    <option value="10:00">10:00</option>
                                    <option value="11:00">11:00</option>
                                    <option value="14:00">14:00</option>
                                    <option value="15:00">15:00</option>
                                    <option value="16:00">16:00</option>
                                    <option value="17:00">17:00</option>
                                </select>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="leva_traz" id="leva-traz"
                                style="border-radius: 50%; background-color: transparent; border: 2px solid white;">
                            <label class="form-check-label text-white small" for="leva-traz">
                                Deseja o serviço leva e trás?
                            </label>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn w-50 me-2" data-bs-dismiss="modal"
                                style="background-color: white; color: #444; border-radius: 10px; height: 55px; box-shadow: 0 0 5px rgba(0,0,0,0.50); font-weight: 500;">
                                Voltar
                            </button>
                            <button type="submit" class="btn w-50 ms-2"
                                style="background-color: white; color: #444; border-radius: 10px; height: 55px; box-shadow: 0 0 5px rgba(0,0,0,0.50); font-weight: 500;">
                                Agendar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- ====================================================== -->

    <!-- Modal: Novo Veículo -->
    <div class="modal fade" id="addCarModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 15px; background-color: #009bbf;">
                <form id="carForm"  method="POST">
                    <div class="modal-body px-4 py-4 text-white">
                        <h5 class="text-center fw-bold mb-4">Novo Veículo</h5>


                        <!-- DEVEMOS SELECIONAR PRIMEIRAMENTE O VEÍCULO -->
                        <div class="mb-3">
                            <label class="form-label small">Tipo de Veículo</label>
                            <select class="form-select" id="tipo"
                                style="border-radius: 10px; height: 45px; font-size: 0.95rem; border: none;" required>
                                <option value="" disabled selected>Selecione o tipo</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small">Marca do Veículo</label>
                            <select class="form-select" id="marca"
                                style="border-radius: 10px; height: 45px; font-size: 0.95rem; border: none;" required>
                                <option value="" disabled selected>Selecione a marca</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small">Modelo do Veículo</label>
                            <select class="form-select" id="modelo"
                                style="border-radius: 10px; height: 45px; font-size: 0.95rem; border: none;" required>
                                <option value="" disabled selected>Selecione o modelo</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small">Placa</label>
                            <input type="text" name="placa" class="form-control" required
                                style="border-radius: 10px; height: 45px; font-size: 0.95rem; border: none;"
                                placeholder="Ex: TRG2E34">
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn w-50 me-2" data-bs-dismiss="modal"
                                style="background-color: white; color: #444; border-radius: 10px; height: 55px; box-shadow: 0 0 5px rgba(0,0,0,0.50); font-weight: 500;">
                                Voltar
                            </button>
                            <button type="submit" class="btn w-50 ms-2"
                                style="background-color: white; color: #444; border-radius: 10px; height: 55px; box-shadow: 0 0 5px rgba(0,0,0,0.50); font-weight: 500;">
                                Salvar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- carregando o js das funcionalidades -->
        <script type="module" src="../public/js/welcome.js"></script>
        <!-- validação de veiculos -->
        <script src="../public/js/validacao_veiculo.js"></script>

        <!-- Custom JS -->
        <script>
        src = "../public/js/cadastro_veiculo"
        </script>
        <!-- <script>
        src = "../public/js/dashboard_user.js"
        </script> -->
</section>

