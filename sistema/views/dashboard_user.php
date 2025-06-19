<?php
session_start();
require_once __DIR__ . '/../init.php'; // para garantir que a conexão esteja disponível 

// Gera um token CSRF, pra sessão para proteger contra ataques CSRF
// $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Verifica se o usuário está logado, caso contrário, redireciona para a página de login
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
$alerta = '';
$alerta = $_SESSION['alert_success'] ?? null;
$alertSuccess = $_SESSION['alert_success'] ?? '';
unset($_SESSION['alert_success']); // Limpa para não exibir novamente



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

// Consulta para buscar CPF e endereço do usuário logado
$sql = "SELECT 
            u.cpf, 
            e.cep, 
            e.rua, 
            e.numero, 
            e.bairro
        FROM 
            usuarios u
        INNER JOIN 
            enderecos e ON u.idusuarios = e.usuarios_idusuarios
        WHERE 
            u.idusuarios = ?
        LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // 🔑 Descriptografar o CPF
        $key = base64_decode($_ENV['CHAVE_CPF'] ?? '');
        if ($key === false) {
            die('Erro: chave CPF inválida.');
        }

        $cpfDescriptografado = openssl_decrypt($row['cpf'], 'AES-128-ECB', $key);
        if ($cpfDescriptografado === false) {
            error_log('Falha ao descriptografar CPF para usuário ID '.$userId);
            $cpfDescriptografado = '[Erro ao descriptografar]';
        }

        // Jogar na sessão
        $_SESSION['cpf'] = $cpfDescriptografado;
        $_SESSION['cep'] = $row['cep'];
        $_SESSION['rua'] = $row['rua'];
        $_SESSION['numero'] = $row['numero'];
        $_SESSION['bairro'] = $row['bairro'];

    } else {
        // Se não tiver endereço cadastrado, evita erro no formulário
        $_SESSION['cpf'] = '';
        $_SESSION['cep'] = '';
        $_SESSION['rua'] = '';
        $_SESSION['numero'] = '';
        $_SESSION['bairro'] = '';
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Erro na preparação da consulta: " . mysqli_error($conn);
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <header class="d-flex justify-content-between align-items-center px-5"
        style="background-color: #0097B2; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; height: 120px; box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.3);">

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
        <div class="d-flex flex-column align-items-end justify-content-end ms-auto">
            <!-- Abrir Modal -->
            <a href="#" class="text-white text-decoration-none small d-flex align-items-end mb-2" data-bs-toggle="modal"
                data-bs-target="#perfilModal">
                <i class="bi bi-person me-2"></i> Meu Perfil
            </a>

            <!-- Logout -->
            <a href="../controllers/logout.php" class="text-white text-decoration-none small d-flex align-items-center">
                <i class="bi bi-power me-2 fw-bold"></i> Sair
            </a>
        </div>

    </header>

    <!-- Main -->
    <main class="flex-grow-1 py-4">
        <h3 class="text-center fw-semibold mb-4 fw-bold" style="color: #444;">Dashboard</h3>

        <div class="container">
            <div class="d-flex flex-wrap gap-3 justify-content-start">

                <div class="container">
                    <div class="row g-4 ">
                        <!-- Card de Veículos -->
                        <div class="col-12 col-md-6 ">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-body d-flex flex-column gap-3"
                                    style="box-shadow: 0 0 3px  #0097B2; border-radius: 8px; height: 100%;">

                                    <div>
                                        <div class="d-flex justify-content-between align-items-center"
                                            style="width: 100%;">
                                            <!-- Div de 60% com ícone e título -->
                                            <div class="d-flex align-items-center" style="width: 60%;">
                                                <i class="bi bi-car-front me-2"
                                                    style="font-size: 2rem; color: #0097B2;"></i>
                                                <h4 class="mb-0">Meus Veículos</h4>
                                            </div>

                                            <!-- Div de 40% com o botão -->
                                            <div
                                                style="width: 40%; display: flex; justify-content: flex-end; height: 100%;">
                                                <button id="addCarBtn"
                                                    class="btn_add_vei border-0 d-flex justify-content-center align-items-center"
                                                    style="background-color: #0097B2; color: white; border-radius: 8px; box-shadow: 0 0 10px inset rgba(0,0,0,0.3); width: 100%; max-width: 150px;">
                                                    <p class="m-0" style="font-size: 14px">+ Veículo</p>
                                                </button>

                                            </div>
                                        </div>

                                        <!-- Descomentar  o código abaixo p/ exibir todos os veículos do usuário cadastrado, ativos e inativos. -->
                                        <!-- <small class="text-muted" style="font-size: 13px;">
                                        <?= $qtdVeiculos ?> veículo<?= $qtdVeiculos == 1 ? '' : 's' ?> cadastrado<?= $qtdVeiculos == 1 ? '' : 's' ?>
                                        </small> -->
                                        <small class="text-muted" style="font-size: 13px;">
                                            <?= $qtdVeiculos ?> veículo<?= $qtdVeiculos == 1 ? '' : 's' ?>
                                            ativo<?= $qtdVeiculos == 1 ? '' : 's' ?>
                                        </small>
                                    </div>

                                    <!-- Lista de veículos -->
                                    <div id="carsList" class="d-flex flex-wrap gap-3"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Card de Agendamentos -->
                        <div class="col-12 col-md-6">
                            <div class="card shadow-sm h-100 border-0">
                                <div class="card-body"
                                    style="box-shadow: 0 0 3px  #0097B2; border-radius: 8px; height: 100%;">

                                    <!-- Cabeçalho do card -->
                                    <div>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-calendar3 me-3"
                                                style="font-size: 2rem; color: #0097B2;"></i>
                                            <h4 class=" m-0">Agendamentos</h4>
                                        </div>
                                        <!-- Subtítulo sutil abaixo do título -->
                                        <small class="text-muted" style="font-size: 13px;">
                                            Limite de 4 agendamentos por usuário
                                        </small>
                                    </div>

                                    <!-- Lista de agendamentos -->
                                    <div id="appointmentsList" class=""></div>

                                </div>
                            </div>
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

    <!-- csrf-token para segurança -->
    <meta name="csrf-token" content="<?= $_SESSION['csrf_token']; ?>">

    <!-- Modal: Novo Veículo -->
    <section class="modal fade" id="addCarModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 15px; background-color: #0097B2;">
                <form id="carForm" method="POST">
                    <!-- CSRF Token para segurança, input oculto -->
                    <!-- <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>"> -->

                    <div class="modal-header px-4 py-4 text-white">
                        <h5 class="modal-title">Novo Veículo</h5>
                    </div>

                    <div class="modal-body px-4 py-4 text-white">

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
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn w-50 me-2 border-0" data-bs-dismiss="modal"
                                style="background-color: white; border-radius: 10px; height: 55px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);">
                                Voltar
                            </button>
                            <button type="submit" class="btn w-50 ms-2 border-0" id="submitCarBtn"
                                style="background-color: white; border-radius: 10px; height: 55px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);">
                                Salvar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Modal Agendamento -->
    <section class="modal fade" id="scheduleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 15px; background-color: #0097B2;">
                <div class="modal-header px-4 py-4 text-white">
                    <h5 class="modal-title">Agendamento</h5>
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
                                <option value="<?= $veiculo['idveiculos'] ?>"
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
                            <button type="button" class="btn w-50 me-2 border-0" data-bs-dismiss="modal"
                                style="background-color: white; border-radius: 10px; height: 55px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);">
                                Voltar
                            </button>
                            <button type="submit" class="btn w-50 ms-2 border-0"
                                style="background-color: white; border-radius: 10px; height: 55px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);">
                                Agendar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Meu Perfil -->
    <section class="modal fade" id="perfilModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 15px; background-color: #0097B2;">

                <div class="modal-header px-4 py-4 text-white">
                    <h5 class="modal-title">Meu Perfil</h5>
                </div>

                <div class="modal-body">
                    <form method="POST" action="../controllers/atualiza_perfil.php">
                        <!-- Token CSRF -->
                        <input type="hidden" name="csrf_token"
                            value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                        <div class="mb-2">
                            <div class="d-flex align-items-center"
                                style="border: 2px solid white; border-radius: 10px; padding: 0 1rem; height: 50px; background-color: white;">
                                <i class="bi bi-person" style="color: #0097B2; font-size: 1.2rem;"></i>
                                <input type="text" name="nome" class="form-control border-0 shadow-none"
                                    placeholder="Nome completo" value="<?= htmlspecialchars($_SESSION['nome'] ?? '') ?>"
                                    style="margin-left: 0.75rem; font-size: 1rem; color: #444;" required>
                            </div>
                        </div>


                        <!-- Telefone -->
                        <div class="mb-2">
                            <div class="d-flex align-items-center"
                                style="border: 2px solid white; border-radius: 10px; padding: 0 1rem; height: 50px; background-color: white;">
                                <i class="bi bi-telephone" style="color: #0097B2; font-size: 1.2rem;"></i>
                                <input type="text" name="telefone" class="form-control border-0 shadow-none"
                                    placeholder="Celular (com DDD)"
                                    value="<?= htmlspecialchars($_SESSION['telefone'] ?? '') ?>"
                                    style="margin-left: 0.75rem; font-size: 1rem; color: #444;" required>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="mb-2">
                            <div class="d-flex align-items-center"
                                style="border: 2px solid white; border-radius: 10px; padding: 0 1rem; height: 50px; background-color: white;">
                                <i class="bi bi-envelope" style="color: #0097B2; font-size: 1.2rem;"></i>
                                <input type="email" name="email" class="form-control border-0 shadow-none"
                                    placeholder="Email" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>"
                                    style="margin-left: 0.75rem; font-size: 1rem; color: #444;" required>
                            </div>
                        </div>

                        <!-- CPF -->
                        <div class="mb-2">
                            <div class="d-flex align-items-center"
                                style="border: 2px solid white; border-radius: 10px; padding: 0 1rem; height: 50px; background-color: white;">
                                <i class="bi bi-credit-card" style="color: #0097B2; font-size: 1.2rem;"></i>
                                <input type="text" name="cpf" class="form-control border-0 shadow-none"
                                    placeholder="CPF" value="<?= htmlspecialchars($_SESSION['cpf'] ?? '') ?>"
                                    style="margin-left: 0.75rem; font-size: 1rem; color: #444;" required>
                            </div>
                        </div>

                        <!-- CEP -->
                        <div class="mb-2">
                            <div class="d-flex align-items-center"
                                style="border: 2px solid white; border-radius: 10px; padding: 0 1rem; height: 50px; background-color: white;">
                                <i class="bi bi-geo-alt" style="color: #0097B2; font-size: 1.2rem;"></i>
                                <input type="text" name="cep" class="form-control border-0 shadow-none"
                                    placeholder="CEP" value="<?= htmlspecialchars($_SESSION['cep'] ?? '') ?>"
                                    style="margin-left: 0.75rem; font-size: 1rem; color: #444;" id="cepInput" required>
                            </div>
                        </div>

                        <!-- Rua e Número -->
                        <div class="row g-2">
                            <div class="col-7">
                                <div class="d-flex align-items-center"
                                    style="border: 2px solid white; border-radius: 10px; padding: 0 1rem; height: 50px; background-color: white;">
                                    <i class="bi bi-geo" style="color: #0097B2; font-size: 1.2rem;"></i>
                                    <input type="text" name="rua" class="form-control border-0 shadow-none"
                                        placeholder="Rua" value="<?= htmlspecialchars($_SESSION['rua'] ?? '') ?>"
                                        style="margin-left: 0.75rem; font-size: 1rem; color: #444;" id="ruaInput" required>
                                </div>
                            </div>

                            <div class="col-5">
                                <div class="d-flex align-items-center"
                                    style="border: 2px solid white; border-radius: 10px; padding: 0 1rem; height: 50px; background-color: white;">
                                    <i class="bi bi-123" style="color: #0097B2; font-size: 1.2rem;"></i>
                                    <input type="number" name="numero"
                                        class="form-control border-0 shadow-none text-center" placeholder="Número"
                                        value="<?= htmlspecialchars($_SESSION['numero'] ?? '') ?>"
                                        style="margin-left: 0.75rem; font-size: 1rem; color: #444;" required>
                                </div>
                            </div>
                        </div>

                        <!-- Bairro -->
                        <div class="mt-2 mb-2">
                            <div class="d-flex align-items-center"
                                style="border: 2px solid white; border-radius: 10px; padding: 0 1rem; height: 50px; background-color: white;">
                                <i class="bi bi-geo-alt-fill" style="color: #0097B2; font-size: 1.2rem;"></i>
                                <input type="text" name="bairro" class="form-control border-0 shadow-none"
                                    placeholder="Bairro" value="<?= htmlspecialchars($_SESSION['bairro'] ?? '') ?>"
                                    style="margin-left: 0.75rem; font-size: 1rem; color: #444;" id="bairroInput" required>
                            </div>
                        </div>


                        <!-- Botões -->
                        <div class="d-flex justify-content-between mt-5">
                            <button type="button" class="btn w-50 me-2 border-0" data-bs-dismiss="modal" style="background-color: white; color: #444; border-radius: 10px; height: 55px;
                           box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);">
                                Voltar
                            </button>

                            <button type="submit"
                                class="btn d-flex align-items-center justify-content-center w-50 me-2 border-0"
                                style="background-color: white; border-radius: 10px; height: 55px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);">
                                Salvar
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>

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
    document.addEventListener("DOMContentLoaded", function() {
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

    <!-- Script para exibir mensagem de sucesso ao atualizar perfil -->
    <?php if ($alertSuccess === 'atualizado'): ?>
    <script>
        Swal.fire({
            icon: "success",
            title: "Atualizado!",
            text: "Perfil atualizado com sucesso!",
            showConfirmButton: false,
            timer: 2000
        }).then(() => {
               window.location.href = '../views/dashboard_user.php';
         });
    </script>
    <?php endif; ?>

    <!-- // auto preencher cep do modal de perfil -->
    <script src="../public/js/preencher_cep_perfil.js"></script>

</body>

</html>