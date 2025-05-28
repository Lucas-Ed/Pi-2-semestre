<?php
session_start();
require_once __DIR__ . '/../init.php'; // para garantir que a conexão esteja disponível
// Chama o componente de cabeçalho da página
require_once __DIR__ . '/components/header.php'; 

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../views/index.php");
    exit;
}

$userId = $_SESSION['idusuarios']; // ou o nome correto do ID na session

// Consulta para obter o número de veículos do usuário.
$query = "SELECT COUNT(*) AS total FROM veiculos WHERE usuarios_idusuarios = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$qtdVeiculos = $row['total'];

?>
    <!-- carrega o css da página -->
    <link rel="stylesheet" href="../public/css/dashboard_user.css">
    <!-- lib sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
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

                <div class="container">
                <div class="row g-4">
                    <!-- Card de Veículos -->
                    <div class="col-12 col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body d-flex flex-column gap-3">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-2">
                            <div class="d-flex align-items-center">
                            <i class="bi bi-car-front me-2" style="font-size: 2rem; color: #009bbf;"></i>
                            <h2 class="h4 mb-0">Meus Veículos</h2>
                            </div>
                            <button id="addCarBtn" class="btn_add_vei">
                            <i class="bi bi-plus-lg"></i> Adicionar Veículo
                            </button>
                        </div>
                        <small class="text-muted" style="font-size: 13px;">
                          <?= $qtdVeiculos ?> veículo<?= $qtdVeiculos == 1 ? '' : 's' ?> cadastrado<?= $qtdVeiculos == 1 ? '' : 's' ?>
                        </small>

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

    <!-- Modal Adicionar Carro -->
    <div class="modal fade" id="addCarModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Adicionar Novo Veículo</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form id="carForm" ><!--action="../public/api/salvar_veiculo.php" method="post"-->
              <div class="mb-3">
                <label for="carModel" class="form-label">Modelo do Veículo:</label>
                <input type="text" class="form-control" id="modelo" required>
              </div>
              <div class="mb-3">
                <label for="plate" class="form-label">Placa:</label>
                <input type="text" class="form-control" id="placa" required>
              </div>
              <div class="text-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar Veículo</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Agendamento -->
    <div class="modal fade" id="scheduleModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Novo Agendamento</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form id="appointmentForm">
              <div class="mb-3">
                <label for="name" class="form-label">Nome:</label>
                <!-- <input type="text" class="form-control" id="name" required> -->
                <p class="form-control-plaintext" id="name">
                  <?php echo htmlspecialchars($_SESSION["nome"]); ?>
                </p>
              </div>

              <div class="mb-3">
              <label for="phone" class="form-label">Telefone:</label>
                <!-- <input type="tel" class="form-control" id="phone" required> -->
                <!-- <input type="tel"  class="form-control" id="phone" pattern="\(\d{2}\)\s?\d{4,5}-\d{4}" title="Formato: (99) 99999-9999"> -->
              <p class="form-control-plaintext" id="phone">
                <?php echo htmlspecialchars($_SESSION["telefone"]); ?>
              </p>
            </div>


              <div class="mb-3">
                <label for="selectedCar" class="form-label">Selecione o Carro:</label>
                <select class="form-select" id="selectedCar" required>
                  <option value="">Selecione um carro</option>
                </select>
              </div>

              <div class="mb-3">
                <label for="service" class="form-label">Serviço:</label>
                <select class="form-select" id="service" required>
                  <option value="">Selecione o serviço</option>
                  <option value="simples">Lavagem Simples - R$ 40,00</option>
                  <option value="completa">Lavagem Completa - R$ 70,00</option>
                  <option value="premium">Lavagem Premium - R$ 100,00</option>
                </select>
              </div>

              <div class="mb-3">
                <label for="date" class="form-label">Data:</label>
                <input type="date" class="form-control" id="date" required>
              </div>

              <div class="mb-3">
                <label for="time" class="form-label">Horário:</label>
                <select class="form-select" id="time" required>
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

            <div class="mb-3 form-check">
                <input type="checkbox" name="leva_e_tras" class="form-check-input" id="leva_e_tras">
                <label class="form-check-label" for="levar_e_tras">
                    <p>Quer serviço Leva e Traz ?</p>
                </label>
            </div>


              <div class="text-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Agendar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!--Spinner-de-carregabdo-->
    <div id="loadingSpinner" class="text-center my-4" style="display: none;">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Carregando...</span>
      </div>
    </div>

<!---fim da página de agendamento--->
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- carregando o js das funcionalidades -->
    <script type="module" src="../public/js/welcome.js"></script>
    <!--- carrega a base url do projeto -->
    <!-- <script src="config/base_url.php"></script> -->
    <!-- validação de veiculos -->
    <script src="../public/js/validacao_veiculo.js"></script>


</section>