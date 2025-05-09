<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../views/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt_BR">
<head>
    <meta charset="UTF-8">
    <title>Lava Rápido - Agendamento</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- lib sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; };
      
    </style>
</head>
<body class="bg-light">
    <br> 
    <br>
    <!-- <script>
            sessionStorage.setItem("userName", "<?php echo addslashes($_SESSION["nome"]); ?>");
  </script> -->

    <div class="page-header">
        <h1>Olá, <b><?php
                        $nomeCompleto = htmlspecialchars($_SESSION["nome"]);
                        $nomes = explode(" ", $nomeCompleto);

                        if (count($nomes) >= 2) {
                            $primeiroSegundoNome = $nomes[0] . " " . $nomes[1];
                            echo $primeiroSegundoNome;
                        } else {
                            // Caso o nome tenha menos de dois termos, exibe o nome completo
                            echo $nomeCompleto;
                        }
                        //echo "ID: " . $_SESSION["idusuarios"]; // Deve imprimir algo como: ID: 1
?>
        <br>
        <!-- </b>Bem vindo ao site !!</h1> -->
    </div>

    <!-- <div class="page-header">
    <h1>
        Olá, 
        <b>
        php
            $nomeCompleto = isset($_SESSION["nome"]) ? trim($_SESSION["nome"]) : '';
            $nomes = explode(" ", $nomeCompleto);
            echo htmlspecialchars(implode(" ", array_slice($nomes, 0, 2)));
        ?>
        </b><br>
        Bem-vindo ao site!!
    </h1>
</div> -->

    <p>
        
        <!-- <a href="cadastro.php" class="btn btn-primary">Cadastre-se</a>
        <br><br> -->
        
        <a style="margin-left: -1500px; margin-top: -100px;" href="../controllers/logout.php" class="btn btn-danger">Sair da conta</a>
    </p>

    <!----página de agendamento---->
    <div class="container py-5">
      <header class="text-center mb-5">
        <h1 class="display-4">🚗 Embelezamento automotivo</h1>
        <p class="lead text-muted">Agende sua lavagem de forma rápida e fácil</p>
      </header>

      <div class="row g-4">
        <div class="col-md-6">
          <div class="card shadow-sm">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 mb-0">Meus Veículos</h2>
                <button id="addCarBtn" class="btn btn-primary"><!--addCarBtn--->
                  <i class="bi bi-plus-lg"></i> Adicionar Veículo
                </button>
              </div>
              <div id="carsList"></div>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card shadow-sm">
            <div class="card-body">
              <h2 class="h4 mb-4">Agendamentos</h2>
              <div id="appointmentsList"></div><!--- id="appointmentsList" -->
              <!-- <p class="card-text"><strong>Carro:</strong> ${selectedCar.model}</p>
              <p class="card-text"><strong>Placa:</strong> ${selectedCar.plate}</p>
              <p class="card-text"><strong>Nome:</strong> ${appointment.name}</p> -->

            </div>
          </div>
        </div>
      </div>
    </div>

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


</body>
</html>