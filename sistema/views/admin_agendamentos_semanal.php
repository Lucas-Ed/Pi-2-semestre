<?php
// Pagina para listar agendamentos da semana do adm.
session_start(); //  Inicia a sessão
require_once __DIR__ . '/../init.php'; // e inclui o arquivo de inicialização

// Verifica se o usuário está logado
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../views/index.php");
    exit;
}

// Consulta os agendamentos e exibie somente os agendamentos da semana atual (segunda a domingo).
$sql = "
SELECT 
    a.idagendamentos,
    u.nome,
    u.telefone,
    u.email,
    u.cpf,
    e.cep,
    e.rua,
    e.numero,
    e.bairro,
    v.modelo,
    v.placa,
    a.servico,
    a.preco,  
    a.data_agendamento,
    a.hora_agendamento,
    a.leva_e_tras,
    s.executado
FROM agendamentos a
INNER JOIN usuarios u ON a.usuarios_idusuarios = u.idusuarios
LEFT JOIN enderecos e ON e.usuarios_idusuarios = u.idusuarios
LEFT JOIN veiculos v ON a.veiculos_idveiculos = v.idveiculos
LEFT JOIN pagamentos p ON a.idagendamentos = p.agendamentos_idagendamentos
LEFT JOIN status_ag s ON a.idagendamentos = s.agendamentos_idagendamentos
WHERE a.data_agendamento BETWEEN DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)
                            AND DATE_ADD(CURDATE(), INTERVAL (6 - WEEKDAY(CURDATE())) DAY)
ORDER BY a.data_agendamento ASC, a.hora_agendamento ASC
";

// Conecta ao banco de dados
$result = $conn->query($sql);
if (!$result) {
    die("Erro na consulta: " . $conn->error);
}


$key = base64_decode($_ENV['CHAVE_CPF'] ?? '');
// Verifica se a chave de criptografia foi definida
function descriptografarCPF($cpf_criptografado, $chave) {
    $cipher = "AES-128-ECB";
    return openssl_decrypt($cpf_criptografado, $cipher, $chave);
}

// Função auxiliar opcional (se ainda quiser manter para outros usos)
function tipoVeiculo($modelo) {
    $modeloLower = strtolower($modelo);
    if (str_contains($modeloLower, 'moto')) return 'moto';
    if (str_contains($modeloLower, 'caminhao')) return 'caminhao';
    if (str_contains($modeloLower, 'van')) return 'van';
    return 'carro'; // padrão
}

// Calcula a soma dos valores dos agendamentos
$total_valor_dia = 0;
$agendamentos = [];

// Array para mapear os nomes dos serviços.
while ($row = $result->fetch_assoc()) {
    $valor = $row['preco'] ?? 0;
    $row['valor'] = $valor;

    // Descriptografa o CPF antes de passar para o array
    $row['cpf'] = descriptografarCPF($row['cpf'], $key);

    $agendamentos[] = $row;
    $total_valor_dia += $valor;
}

// Conta o total de agendamentos da semana
$total_agend = count($agendamentos);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem Agendamentos</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" href="../public/uploads/img/favicon.svg" type="image/svg+xml">
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="../public/css/tabelas.css">
</head>

<body class="bg-white d-flex flex-column" style="min-height: 100vh;">

    <!-- Header -->
    <header class="d-flex justify-content-between align-items-center px-5 py-3 shadow-sm"
        style="background-color: #0097B2; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; height: 120px;">
        <div>
            <h4 class="text-white m-0">Olá, <span class="fw-bold">Administrador</span></h4>
            <small class="text-white" style="font-size: 0.9rem;">Seja bem-vindo!</small>
        </div>
        <a href="../controllers/logout.php" class="text-white text-decoration-none small d-flex align-items-center">
            <i class="bi bi-power me-1"></i> Sair
        </a>
    </header>

    <main class="flex-grow-1 py-4">
        <?php
        $inicioSemana = date('d/m/Y', strtotime('monday this week'));
        $fimSemana = date('d/m/Y', strtotime('sunday this week'));
        ?>
        <h5 class="text-center fw-semibold  mb-4" style="color: #444;">
            Agendamentos da Semana
        </h5>

        <div class="container px-3">
            <div class="d-flex justify-content-between align-items-center mb-2">

                <div class="d-flex flex-column align-items-start">
                    <small class="text-muted" style="font-size: 0.85rem; opacity: 0.7;">Período: (<?= $inicioSemana ?> -
                        <?= $fimSemana ?>)
                    </small>

                    <small class="text-muted" style="font-size: 0.85rem; opacity: 0.7;">
                        Total de agendamentos da semana: <?= $total_agend ?>
                    </small>
                </div>

                <!-- Botão Agendamentos da semana -->
                <a href="../views/admin_agendamentos.php"
                    class="btn d-flex align-items-center justify-content-center fw-semibold"
                    style=" border-radius: 8px; color: white; background-color: #0097B2">
                    Hoje
                </a>
            </div>

            <div class="table-responsive d-none d-md-block">
                <table class="table text-white align-middle table-agendamento">
                    <thead class="text-nowrap text-center">
                        <tr>
                            <th style="border-radius: 10px 0 0 10px; background-color: #0097B2">Nome</th>
                            <th style="background-color: #0097B2">Telefone</th>
                            <th style="background-color: #0097B2">Veículo</th>
                            <th style="background-color: #0097B2">Serviço</th>
                            <th style="background-color: #0097B2">Data</th>
                            <th style="background-color: #0097B2">Hora</th>
                            <th style="background-color: #0097B2">Leva e Traz</th>
                            <th style="background-color: #0097B2">Preço</th>
                            <th style="background-color: #0097B2">Status</th>
                            <th style="border-radius: 0 10px 10px 0; background-color: #0097B2">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <!-- ?<php while ($row = $result->fetch_assoc()): ?> -->
                        <?php foreach ($agendamentos as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nome']) ?></td>
                            <!-- <td>?<= htmlspecialchars($row['telefone']) ?></td> -->
                            <td>
                                <a href="https://wa.me/55<?= preg_replace('/\D/', '', $row['telefone']) ?>"
                                    target="_blank" style="color: #00a3c7; text-decoration: none;">
                                    <?= htmlspecialchars($row['telefone']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($row['modelo']) ?> [<?= htmlspecialchars($row['placa']) ?>]</td>
                            <!-- <td>?<= htmlspecialchars($row['servico']) ?></td> -->
                            <td><?= $nomesServicos[$row['servico']] ?? ucfirst($row['servico']) ?></td>
                            <td><?= date('d/m/Y', strtotime($row['data_agendamento'])) ?></td>
                            <td><?= htmlspecialchars(substr($row['hora_agendamento'], 0, 5)) ?></td>
                            <td><?= $row['leva_e_tras'] ? 'Sim' : 'Não' ?></td>
                            <!-- <td>?<= number_format($row['valor'], 2, ',', '.') ?> R$</td> -->
                            <td>R$ <?= number_format($row['valor'], 2, ',', '.') ?></td>
                            <td><?= htmlspecialchars($row['executado'] ?? 'Não definido') ?></td>
                            <td>
                                <i class="bi bi-pencil-square me-2 icon-action btn-editar" title="Editar"
                                    data-id="<?= $row['idagendamentos'] ?>" data-bs-toggle="modal"
                                    data-bs-target="#modalEditar" style="color: #00a3c7;">
                                </i>
                                <i class="bi bi-card-text icon-action btn-detalhes" title="Detalhes"
                                    data-bs-toggle="modal" data-bs-target="#modalDetalhes"
                                    data-nome="<?= htmlspecialchars($row['nome']) ?>"
                                    data-telefone="<?= htmlspecialchars($row['telefone']) ?>"
                                    data-email="<?= htmlspecialchars($row['email']) ?>"
                                    data-cpf="<?= htmlspecialchars($row['cpf']) ?>"
                                    data-cep="<?= htmlspecialchars($row['cep']) ?>"
                                    data-rua="<?= htmlspecialchars($row['rua']) ?>"
                                    data-numero="<?= htmlspecialchars($row['numero']) ?>"
                                    data-bairro="<?= htmlspecialchars($row['bairro']) ?>" style="color: #00a3c7;">
                                </i>
                                <!-- Botão de remover -->
                                <i class="bi bi-trash icon-action btn-remover" title="Remover"
                                    data-id="<?= $row['idagendamentos'] ?>" style="color: red; cursor: pointer;">
                                </i>

                            </td>
                        </tr>
                        <!-- ?<php endwhile; ?> -->
                        <?php endforeach; ?>
                        <!-- Exibe o total do dia -->
                        <tr>
                        <tr>
                            <td colspan="7" class="text-end fw-bold text-black">Total da Semana:</td>

                            <td class="fw-bold text-black"> R$ <?= number_format($total_valor_dia, 2, ',', '.') ?></td>
                            <td colspan="2"></td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <!-- VISÃO MOBILE -->
            <div class="d-md-none">
                <?php foreach ($agendamentos as $row): ?>

                <div class="card text-white mb-3 border-0"
                    style="box-shadow: 0 0 3px inset #009bbf; border-radius: 8px;">
                    <div class="card-body position-relative ">

                        <div>
                            <h4 class="card-title m-0 pb-3" style="color: #444;"><?= htmlspecialchars($row['nome']) ?>
                            </h4>

                            <!-- Inserir aqui o status igual o do usuario, pois o admin precisa ferificar em qual parte do processo o cliente está vendo -->

                        </div>

                        <p class="card-text m-0" style="color: #444;">
                            <strong>Telefone:</strong>
                            <a href="https://wa.me/55<?= preg_replace('/\D/', '', $row['telefone']) ?>" target="_blank"
                                style="color: #444; text-decoration: underline;">
                                <?= htmlspecialchars($row['telefone']) ?>
                            </a>
                        </p>

                        <p class="card-text m-0" style="color: #444;"><strong>Veículo:</strong>
                            <?= htmlspecialchars($row['modelo']) ?>
                            (<?= htmlspecialchars($row['placa']) ?>)</p>

                        <p class="card-text m-0" style="color: #444;"><strong>Serviço:</strong>
                            <?= htmlspecialchars($row['servico']) ?></p>

                        <p class="card-text m-0" style="color: #444;"><strong>Data:</strong>
                            <?= date('d/m/Y', strtotime($row['data_agendamento'])) ?></p>

                        <p class="card-text m-0" style="color: #444;"><strong>Hora:</strong>
                            <?= htmlspecialchars(substr($row['hora_agendamento'], 0, 5)) ?></p>

                        <p class="card-text m-0" style="color: #444;"><strong>Leva e Traz:</strong>
                            <?= $row['leva_e_tras'] ? 'Sim' : 'Não' ?></p>

                        <p class="card-text m-0" style="color: #444;"><strong>Preço:</strong> R$
                            <?= number_format($row['preco'], 2, ',', '.') ?></p>

                        <p class="card-text m-0" style="color: #444;"><strong>Status:</strong>
                            <?= $row['executado'] ? 'Confirmado' : 'Não Confirmado' ?></p>

                        <div class="position-absolute top-0 end-0 m-3">

                            <i class="bi bi-pencil-square icon-action btn-editar p-1 fs-3" style="color: #444;"
                                title="Editar" data-id="<?= $row['idagendamentos'] ?>" data-bs-toggle="modal"
                                data-bs-target="#modalEditar" style="color: white;">
                            </i>

                            <i class="bi bi-card-text btn-detalhes p-1 fs-3" style="color: #444;" data-bs-toggle="modal"
                                data-bs-target="#modalDetalhes" data-nome="<?= htmlspecialchars($row['nome']) ?>"
                                data-telefone="<?= htmlspecialchars($row['telefone']) ?>"
                                data-email="<?= htmlspecialchars($row['email']) ?>"
                                data-cpf="<?= htmlspecialchars($row['cpf']) ?>"
                                data-cep="<?= htmlspecialchars($row['cep']) ?>"
                                data-rua="<?= htmlspecialchars($row['rua']) ?>"
                                data-numero="<?= htmlspecialchars($row['numero']) ?>"
                                data-bairro="<?= htmlspecialchars($row['bairro']) ?>">
                            </i>

                            <i class="bi bi-trash icon-action btn-remover p-1 fs-3" title="Remover"
                                data-id="<?= $row['idagendamentos'] ?>" style="color: red; cursor: pointer;">
                            </i>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>


    <footer class="text-center py-3 small" style="color: #bbb;">

        <!-- Botão Voltar -->
        <div class="d-flex justify-content-center mb-5 fixed-bottom">
            <a href="../views/dashboard_admin.php"
                class="btn d-flex align-items-center justify-content-center px-5 w-50 "
                style="background-color: #0097B2; color: white; border-radius: 10px; height: 55px; box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.50); font-weight: 500;">
                Voltar
            </a>
        </div>

        &copy; <?= date('Y') ?> Embelezamento Automotivo. Todos os direitos reservados.
    </footer>


    <!-- Modal Detalhes -->
    <div class="modal fade" id="modalDetalhes" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 15px; background-color: #009bbf;">
                <div class="modal-body px-4 py-4 text-white">
                    <h5 class="text-center fw-bold mb-4">Dados do Cliente</h5>
                    <div class="border p-2 mb-4 rounded">
                        <p><strong>Nome:</strong> <span id="modal-nome"></span></p>
                        <p><strong>Telefone:</strong> <span id="modal-telefone"></span></p>
                        <p><strong>E-mail:</strong> <span id="modal-email"></span></p>
                        <p><strong>CPF:</strong> <span id="modal-cpf"></span></p>
                        <p><strong>CEP:</strong> <span id="modal-cep"></span></p>
                        <p><strong>Rua:</strong> <span id="modal-rua"></span></p>
                        <p><strong>Número:</strong> <span id="modal-numero"></span></p>
                        <p><strong>Bairro:</strong> <span id="modal-bairro"></span></p>
                    </div>
                    <!-- BOTÕES DE LOCALIZAÇÃO -->
                    <div class="mb-3">
                        <h6>Caso queira buscar o veículo...</h6>
                        <div class="d-flex justify-content-between">
                            <a id="btn-waze" href="#" target="_blank"
                                class="btn d-flex align-items-center justify-content-center"
                                style="width: 45%; border-radius: 10px; background-color: white; color: #009bbf;">
                                <img src="../public/uploads/img/waze.svg" alt="waze" class="me-2" width="20">Waze
                            </a>
                            <a id="btn-maps" href="#" target="_blank"
                                class="btn d-flex align-items-center justify-content-center"
                                style="width: 45%; border-radius: 10px; background-color: white; color: #009bbf;">
                                <i class="bi bi-geo-alt me-2 fs-5"></i>Maps
                            </a>
                        </div>
                    </div>


                    <button type="button" class="btn d-flex align-items-center justify-content-center px-5 w-100 me-2 border-0" style="background-color: white; border-radius: 10px; height: 55px; box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.50); font-weight: 500;"
                        data-bs-dismiss="modal"
                        onmouseover="this.style.color='black';"
                        onmouseout="this.style.color='black';">
                        Voltar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar -->
    <div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 15px; background-color: #009bbf;">
                <div class="modal-body px-4 py-4 text-white">
                    <h5 class="text-center fw-bold mb-4">Processo de lavagem</h5>

                    <button type="button" class="btn w-100 mb-3 btn-status" style="background-color: white; color: #444; border-radius: 10px; height: 55px; 
                   box-shadow: 0 0 5px rgba(0,0,0,0.50); font-weight: 500;">
                        Confirmado
                    </button>

                    <button type="button" class="btn w-100 mb-3 btn-status" style="background-color: white; color: red; border-radius: 10px; height: 55px; 
                   box-shadow: 0 0 5px rgba(0,0,0,0.50); font-weight: 500;">
                        Fila de espera
                    </button>

                    <button type="button" class="btn w-100 mb-3 btn-status" style="background-color: white; color: orange; border-radius: 10px; height: 55px; 
                   box-shadow: 0 0 5px rgba(0,0,0,0.50); font-weight: 500;">
                        Em andamento
                    </button>

                    <button type="button" class="btn w-100 mb-4 btn-status" style="background-color: white; color: #444; border-radius: 10px; height: 55px; 
                   box-shadow: 0 0 5px rgba(0,0,0,0.50); font-weight: 500;">
                        Concluída
                    </button>

                    <div class="pt-5">
                        <button type="button" class="btn w-100" style="background-color: white; color: #444; border-radius: 10px; height: 55px; 
                   box-shadow: 0 0 5px rgba(0,0,0,0.50); font-weight: 500;" data-bs-dismiss="modal">
                            Voltar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para preencher o modal dinamicamente -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const detalhesBtns = document.querySelectorAll('.btn-detalhes');
        detalhesBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('modal-nome').textContent = btn.dataset.nome;
                document.getElementById('modal-telefone').textContent = btn.dataset.telefone;
                document.getElementById('modal-email').textContent = btn.dataset.email;
                document.getElementById('modal-cpf').textContent = btn.dataset.cpf;
                document.getElementById('modal-cep').textContent = btn.dataset.cep;
                document.getElementById('modal-rua').textContent = btn.dataset.rua;
                document.getElementById('modal-numero').textContent = btn.dataset.numero;
                document.getElementById('modal-bairro').textContent = btn.dataset.bairro;
            });
        });
    });
    </script>

    <!-- Script do waze e google maps -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const detalhesBtns = document.querySelectorAll('.btn-detalhes');

        detalhesBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const nome = btn.dataset.nome;
                const telefone = btn.dataset.telefone;
                const email = btn.dataset.email;
                const cpf = btn.dataset.cpf;
                const cep = btn.dataset.cep;
                const rua = btn.dataset.rua;
                const numero = btn.dataset.numero;
                const bairro = btn.dataset.bairro;

                document.getElementById('modal-nome').textContent = nome;
                document.getElementById('modal-telefone').textContent = telefone;
                document.getElementById('modal-email').textContent = email;
                document.getElementById('modal-cpf').textContent = cpf;
                document.getElementById('modal-cep').textContent = cep;
                document.getElementById('modal-rua').textContent = rua;
                document.getElementById('modal-numero').textContent = numero;
                document.getElementById('modal-bairro').textContent = bairro;

                // Corrigido: template literals com crase
                const enderecoCompleto = `${rua}, ${numero}, ${bairro}, ${cep}`;
                const enderecoEncoded = encodeURIComponent(enderecoCompleto);

                // Atualiza os links do Waze e Maps
                document.getElementById('btn-waze').href =
                    `https://waze.com/ul?query=${enderecoEncoded}&navigate=yes`;
                document.getElementById('btn-maps').href =
                    `https://www.google.com/maps/search/?api=1&query=${enderecoEncoded}`;
            });
        });
    });
    </script>

    <!-- alertas  de editar-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    let agendamentoId = null;

    document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', () => {
            agendamentoId = btn.dataset.id;
        });
    });

    document.querySelectorAll('#modalEditar .btn-status').forEach(button => {
        button.addEventListener('click', () => {
            const status = button.innerText.trim();
            if (!agendamentoId) return;

            fetch('../controllers/update_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: agendamentoId,
                        status: status
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Status atualizado',
                            text: 'O status foi alterado com sucesso!',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: data.message || 'Erro ao atualizar o status.'
                        });
                    }
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Erro na requisição.'
                    });
                });
        });
    });
    </script>

    <!-- alertas de remover -->
    <script>
    document.querySelectorAll('.btn-remover').forEach(btn => {
        btn.addEventListener('click', () => {
            const agendamentoId = btn.dataset.id;

            Swal.fire({
                title: 'Tem certeza?',
                text: "Essa ação não pode ser desfeita!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, remover!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('../controllers/admin_delete_agendamento.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                id: agendamentoId
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Removido!',
                                    text: 'O agendamento foi removido com sucesso.',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => location.reload());
                            } else {
                                Swal.fire('Erro', data.message ||
                                    'Erro ao remover o agendamento.', 'error');
                            }
                        })
                        .catch(() => {
                            Swal.fire('Erro', 'Erro na requisição.', 'error');
                        });
                }
            });
        });
    });
    </script>

    <!-- Script para exibir a data atual -->
    <script>
    // Obter a data atual
    const hoje = new Date();

    // Formatar a data como dd/mm/yyyy
    const dataFormatada = hoje.toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });

    // Inserir a data no elemento com id="data-dia"
    document.getElementById('data-dia').textContent = dataFormatada;
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>