<?php
session_start();
// Gera um token CSRF, pra sessão para proteger contra ataques CSRF
// $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/../init.php'; // para garantir que a conexão esteja disponível
// Chama o componente de cabeçalho da página
require_once __DIR__ . '/components/header.php'; 

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../views/index.php");
    exit;
}

// Verifica se a sessão está ativa, caso contrário, redireciona para a página de login
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] >1800)) {
    session_unset();
    session_destroy();
    header("location: ../controllers/logout.php");
    exit;
}
// Alerta de sucesso.
$alerta = $_SESSION['alert_success'] ?? null;
unset($_SESSION['alert_success']); // limpa após usar


$_SESSION['LAST_ACTIVITY'] = time();

$userId = $_SESSION['idusuarios']; // ou o nome correto do ID na session

//-- Descomentar  o Select abaixo p/ exibir todos os veículos do usuário cadastrado, ativos e inativos.
// // Consulta para obter o número de veículos do usuário.
// $query = "SELECT COUNT(*) AS total FROM veiculos WHERE usuarios_idusuarios = ?";
// $stmt = $conn->prepare($query);
// $stmt->bind_param("i", $userId);
// $stmt->execute();
// $result = $stmt->get_result();
// $row = $result->fetch_assoc();
// $qtdVeiculos = $row['total'];

// Consulta para obter o número de veículos ATIVOS do usuário.
$query = "SELECT COUNT(*) AS total FROM veiculos WHERE usuarios_idusuarios = ? AND ativo = 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$qtdVeiculos = $row['total'];



// Consulta para obter o tipo de veículos do usuário.
$stmt = $conn->prepare("SELECT idveiculos, modelo, tipo FROM veiculos WHERE usuarios_idusuarios = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$veiculos = $result->fetch_all(MYSQLI_ASSOC);
//print_r($veiculos); // debug


?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Home</title>
    <!-- lib sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Icons bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" href="../public/uploads/img/favicon.svg" type="image/svg+xml">
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="../public/css/dashboard_user.css">  
</head>
     
<body class="bg-white d-flex flex-column" style="min-height: 100vh;">
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
                        <!-- Descomentar  o código abaixo p/ exibir todos os veículos do usuário cadastrado, ativos e inativos. -->
                        <!-- <small class="text-muted" style="font-size: 13px;">
                          <?= $qtdVeiculos ?> veículo<?= $qtdVeiculos == 1 ? '' : 's' ?> cadastrado<?= $qtdVeiculos == 1 ? '' : 's' ?>
                        </small> -->
                        <small class="text-muted" style="font-size: 13px;">
                          <?= $qtdVeiculos ?> veículo<?= $qtdVeiculos == 1 ? '' : 's' ?> ativo<?= $qtdVeiculos == 1 ? '' : 's' ?>
                        </small>

                        <div id="carsList"></div>
                        </div>
                    </div>
                    </div>

                    <!-- Card de Agendamentos -->
                    <div class="col-12 col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <!-- Cabeçalho do card -->
                                <div class="mb-2">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-calendar3 me-3" style="font-size: 2rem; color: #009bbf;"></i>
                                        <h2 class="h4 m-0">Agendamentos</h2>
                                    </div>
                                    <!-- Subtítulo sutil abaixo do título -->
                                    <small class="text-muted" style="font-size: 13px;">
                                        4 agendamentos mais recentes.
                                    </small>
                                </div>

                                <!-- Lista de agendamentos -->
                                <div id="appointmentsList"></div>
                            </div>
                        </div>
                    </div>


    </main>

    <!-- Footer -->
    <footer class="text-center py-3 small" style="color: #bbb;">
        &copy; <?= date('Y') ?> Embelezamento Automotivo. Todos os direitos reservados.
    </footer>

<!-- csrf-token para segurança -->
<meta name="csrf-token" content="<?= $_SESSION['csrf_token']; ?>">

<!-- Modal: Novo Veículo -->
<div class="modal fade" id="addCarModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0" style="border-radius: 15px; background-color: #009bbf;">
      <form id="carForm" method="POST">
        <!-- CSRF Token para segurança, input oculto -->
        <!-- <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>"> -->
        <div class="modal-body px-4 py-4 text-white">
          <h5 class="text-center fw-bold mb-4">Novo Veículo</h5>

          <!-- Tipo de Veículo -->
          <div class="mb-3">
            <label class="form-label small">Tipo de Veículo</label>
            <select class="form-select" id="tipo" name="tipo" required
              style="border-radius: 10px; height: 45px; font-size: 0.95rem; border: none;">
              <option value="" disabled selected>Carregando tipos...</option>
            </select>
          </div>

          <!-- Marca do Veículo -->
          <div class="mb-3">
            <label class="form-label small">Marca do Veículo</label>
            <select class="form-select" id="marca" name="marca" required
              style="border-radius: 10px; height: 45px; font-size: 0.95rem; border: none;">
              <option value="" disabled selected>Selecione o tipo primeiro</option>
            </select>
          </div>

          <!-- Modelo do Veículo (input) -->
          <div class="mb-3">
            <label class="form-label small">Modelo do Veículo</label>
            <input type="text" class="form-control" id="modelo" name="modelo" required
              style="border-radius: 10px; height: 45px; font-size: 0.95rem; border: none;"
              placeholder="Ex: Civic, Strada, Fazer 250">
          </div>

          <!-- Placa -->
          <div class="mb-3">
            <label class="form-label small">Placa</label>
            <input type="text" name="placa" id="placa" class="form-control" required
              style="border-radius: 10px; height: 45px; font-size: 0.95rem; border: none;"
              placeholder="Ex: AAA-1234">
          </div>

          <!-- Botões -->
          <div class="d-flex justify-content-between">
            <button type="button" class="btn w-50 me-2" data-bs-dismiss="modal"
              style="background-color: white; color: #444; border-radius: 10px; height: 55px; box-shadow: 0 0 5px rgba(0,0,0,0.50); font-weight: 500;">
              Voltar
            </button>
            <button type="submit" class="btn w-50 ms-2" id="submitCarBtn"
              style="background-color: white; color: #444; border-radius: 10px; height: 55px; box-shadow: 0 0 5px rgba(0,0,0,0.50); font-weight: 500;">
              Salvar
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Agendamento -->
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0" style="border-radius: 15px; background-color: #009bbf;">
      <div class="modal-header px-4 py-4 text-white">
        <h5 class="modal-title">Agendamento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="appointmentForm">
          <div class="mb-3">
            <i class="bi bi-person text-white m-0"></i>
            <label class="form-label text-white m-0">
              Nome: <span id="name"><?php echo htmlspecialchars($_SESSION["nome"]); ?></span>
            </label>
          </div>

          <div class="mb-3">
            <i class="bi bi-telephone text-white m-0"></i>
            <label class="form-label text-white m-0">
              Telefone: <span id="phone"><?php echo htmlspecialchars($_SESSION["telefone"]); ?></span>
            </label>
          </div>

          <div class="mb-3">
            <label for="selectedCar" class="form-label small text-white m-0">Veículo:</label>
            <select class="form-select" id="selectedCar" required
                        style="border-radius: 10px; height: 45px; font-size: 0.95rem; border: none;">
            <option value="" disabled selected>Selecione um veículo</option>
            <?php foreach ($veiculos as $veiculo): ?>
              <option 
                value="<?= $veiculo['idveiculos'] ?>" 
                data-tipo="<?= strtolower($veiculo['tipo']) ?>">
                <?= htmlspecialchars($veiculo['modelo']) ?> (<?= ucfirst($veiculo['tipo']) ?>)
              </option>
            <?php endforeach; ?>
          </select>

          </div>

          <div class="mb-3">
            <label for="service" class="form-label small text-white m-0">Serviço:</label>
            <select class="form-select" id="serviceSelect" required
                    style="border-radius: 10px; height: 45px; font-size: 0.95rem; border: none;">
              <option value="" disabled selected>Selecione o serviço</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="date" class="form-label small text-white m-0">Data:</label>
            <input type="date" class="form-control" id="date" required
                   style="border-radius: 10px; height: 45px; font-size: 0.95rem; border: none;">
          </div>

          <div class="mb-3">
            <label for="time" class="form-label small text-white m-0">Horário:</label>
            <select class="form-select" id="time" required
                    style="border-radius: 10px; height: 45px; font-size: 0.95rem; border: none;">
               <option value="">Selecione um horário</option>
            </select>

          </div>

          <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="leva_tras" id="leva_tras"
                   style="border-radius: 50%; background-color: transparent; border: 2px solid white;">
            <label class="form-check-label text-white small" for="leva_tras">
              Deseja o serviço leva e trás?
            </label>
          </div>

          <div class="alert alert-light text-dark small mb-4" style="border-radius: 10px;">
            <strong>Horários de funcionamento:</strong><br>
            Segunda a Sexta: 07:00 às 18:00<br>
            Sábados: 07:00 às 15:00<br>
            Domingos: 07:00 às 12:00
          </div>

          <div class="d-flex justify-content-between">
            <button type="button" class="btn w-50 me-2" data-bs-dismiss="modal"
                    style="background-color: white; color: #444; border-radius: 10px; height: 55px;
                           box-shadow: 0 0 5px rgba(0,0,0,0.50); font-weight: 500;">
              Voltar
            </button>
            <button type="submit" class="btn w-50 ms-2"
                    style="background-color: white; color: #444; border-radius: 10px; height: 55px;
                           box-shadow: 0 0 5px rgba(0,0,0,0.50); font-weight: 500;">
              Agendar
            </button>
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
    <!-- cadastro de veiculos -->
     <script src="../public/js/cadastro_veiculo.js"></script>
    <!-- lib sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Script para exibir mensagens de alerta -->
<?php if ($alerta === 'senha_redefinida'): ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        Swal.fire({
              icon: "success",
              title: "Senha redefinida!",
              text: "Login automático autorizado",
              showConfirmButton: false,
              timer: 3000
        }); //.then(() => {
        //       window.location.href = '../views/dashboard_user.php';
        // });
    });
</script>
<?php endif; ?>
</body>
</html>