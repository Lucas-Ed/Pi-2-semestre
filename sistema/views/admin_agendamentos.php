<?php
session_start(); //  Inicia a sessão
require_once __DIR__ . '/../init.php'; // e inclui o arquivo de inicialização
require_once __DIR__ . '/components/header.php'; // Inclui o cabeçalho

// Verifica se o usuário está logado
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../views/index.php");
    exit;
}

// Consulta os agendamentos e os dados dos usuários sem filtros
// $sql = "
// SELECT 
//     a.idagendamentos,
//     u.nome,
//     u.telefone,
//     u.email,
//     u.cpf,
//     e.cep,
//     e.rua,
//     e.numero,
//     e.bairro,
//     v.modelo,
//     v.placa,
//     a.servico,
//     p.valor,
//     a.data_agendamento,
//     a.hora_agendamento,
//     a.leva_e_tras,
//     s.executado
// FROM agendamentos a
// INNER JOIN usuarios u ON a.usuarios_idusuarios = u.idusuarios
// LEFT JOIN enderecos e ON e.usuarios_idusuarios = u.idusuarios
// LEFT JOIN veiculos v ON a.veiculos_idveiculos = v.idveiculos
// LEFT JOIN pagamentos p ON a.idagendamentos = p.agendamentos_idagendamentos
// LEFT JOIN status_ag s ON a.idagendamentos = s.agendamentos_idagendamentos
// ORDER BY a.data_agendamento DESC, a.hora_agendamento DESC
// ";

// Consulta os agendamentos e exibie somente os agendamentos do dia atual
// e ordená-los do mais próximo para o mais distante no tempo
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
    p.valor,
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
WHERE a.data_agendamento = CURDATE()
ORDER BY a.hora_agendamento ASC
";
// Conecta ao banco de dados
$result = $conn->query($sql);
if (!$result) {
    die("Erro na consulta: " . $conn->error);
}

// Tabela de preços baseada no welcome.js
$servicos = [
    "enceramento" => ["carro" => 80, "moto" => 40, "caminhao" => 250],
    "polimento" => ["carro" => 180, "moto" => 40, "caminhao" => 350, "van" => 1200],
    "cristalizacao" => ["carro" => 280, "van" => 1200],
    "vitrificacao" => ["carro" => 730],
    "lavagem_motor" => ["carro" => 60, "van" => 80],
    "hidratacao_couro" => ["carro" => 180, "caminhao" => 250],
    "higienizacao" => ["carro" => 230, "caminhao" => 330, "van" => 800],
    "lavagem_externa" => ["carro" => 70, "moto" => 40, "caminhao" => 250, "van" => 50],
    "lavagem_interna" => ["carro" => 35, "caminhao" => 125, "van" => 50]
];

$nomesServicos = [
    "enceramento" => "Enceramento",
    "polimento" => "Polimento",
    "cristalizacao" => "Cristalização",
    "vitrificacao" => "Vitrificação",
    "lavagem_motor" => "Lavagem de motor",
    "hidratacao_couro" => "Hidratação em couro",
    "higienizacao" => "Higienização",
    "lavagem_externa" => "Lavagem externa",
    "lavagem_interna" => "Lavagem interna"
];

// Função auxiliar para obter tipo de veículo
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

while ($row = $result->fetch_assoc()) {
    $tipo = tipoVeiculo($row['modelo']);
    $servico = $row['servico'];
    $valor = $servicos[$servico][$tipo] ?? 0;

    $row['valor'] = $valor;
    $agendamentos[] = $row;
    $total_valor_dia += $valor;
}
?>

<title>Listagem Agendamentos</title>
<link rel="stylesheet" href="../public/css/tabelas.css">

<section class="bg-white d-flex flex-column" style="min-height: 100vh;">

    <!-- Header -->
    <header class="d-flex justify-content-between align-items-center px-5 py-3 shadow-sm"
        style="background-color: #009bbf; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; height: 120px;">
        <div>
            <h4 class="text-white m-0">Olá, <span class="fw-bold">Administrador</span></h4>
            <small class="text-white" style="font-size: 0.9rem;">Seja bem-vindo!</small>
        </div>
        <a href="../controllers/logout.php" class="text-white text-decoration-none small d-flex align-items-center">
            <i class="bi bi-power me-1"></i> Sair
        </a>
    </header>

            <!-- Botão Voltar -->
            <div class="d-flex justify-content-center my-4">
                <a href="../views/dashboard_admin.php"
                    class="btn d-flex align-items-center justify-content-center px-5 w-50 me-2"
                    style="background-color: #00a3c7; color: white; border-radius: 10px; height: 55px; box-shadow: 0 0 5px rgba(0, 0, 0, 0.50); font-weight: 500;">
                    Voltar
                </a>
            </div>

    <main class="flex-grow-1 py-4">
        <h5 class="text-center fw-semibold mb-4" style="color: #444;">Agendamentos</h5>
        <div class="container px-3">

            <div class="table-responsive d-none d-md-block">
                <table class="table text-white align-middle table-agendamento">
                    <thead class="text-nowrap text-center">
                        <tr>
                            <th style="border-radius: 10px 0 0 10px;">Nome</th>
                            <th>Telefone</th>
                            <th>Veículo</th>
                            <th>Serviço</th>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Leva e Traz</th>
                            <th>Preço</th>
                            <th>Status</th>
                            <th style="border-radius: 0 10px 10px 0;">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <!-- ?<php while ($row = $result->fetch_assoc()): ?> -->
                            <?php foreach ($agendamentos as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nome']) ?></td>
                                <!-- <td>?<= htmlspecialchars($row['telefone']) ?></td> -->
                                 <td>
                                    <a href="https://wa.me/55<?= preg_replace('/\D/', '', $row['telefone']) ?>" target="_blank" style="color: #00a3c7; text-decoration: none;">
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
                                 <?php
                                        $tipo = tipoVeiculo($row['modelo']);
                                        $servico = $row['servico'];
                                        $valor = $servicos[$servico][$tipo] ?? 0;
                                    ?>
                                    <td><?= number_format($valor, 2, ',', '.') ?> R$</td>

                                <td><?= htmlspecialchars($row['executado'] ?? 'Não definido') ?></td>
                                <td>
                                    <i class="bi bi-pencil-square me-2 icon-action btn-editar"
                                        title="Editar"
                                        data-id="<?= $row['idagendamentos'] ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditar"
                                        style="color: #00a3c7;">
                                    </i>
                                    <i class="bi bi-card-text icon-action btn-detalhes" 
                                        title="Detalhes"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalDetalhes"
                                        data-nome="<?= htmlspecialchars($row['nome']) ?>"
                                        data-telefone="<?= htmlspecialchars($row['telefone']) ?>"
                                        data-email="<?= htmlspecialchars($row['email']) ?>"
                                        data-cpf="<?= htmlspecialchars($row['cpf']) ?>"
                                        data-cep="<?= htmlspecialchars($row['cep']) ?>"
                                        data-rua="<?= htmlspecialchars($row['rua']) ?>"
                                        data-numero="<?= htmlspecialchars($row['numero']) ?>"
                                        data-bairro="<?= htmlspecialchars($row['bairro']) ?>"
                                        style="color: #00a3c7;">
                                    </i>
                                    <!-- Botão de remover -->
                                     <i class="bi bi-trash icon-action btn-remover"
                                        title="Remover"
                                        data-id="<?= $row['idagendamentos'] ?>"
                                        style="color: red; cursor: pointer;">
                                    </i>

                                </td>
                            </tr>
                        <!-- ?<php endwhile; ?> -->
                        <?php endforeach; ?>
                            <!-- Exibe o total do dia -->
                            <tr>
                                <tr>
                                    <td colspan="7" class="text-end fw-bold text-black">Total do Dia:</td>
                                    <td class="fw-bold text-black"><?= number_format($total_valor_dia, 2, ',', '.') ?> R$</td>
                                    <td colspan="2"></td>
                                </tr>

                    </tbody>
                </table>
            </div>

            <!-- VISÃO MOBILE -->
            <div class="d-md-none">
            <?php
                $result->data_seek(0);
                while ($row = $result->fetch_assoc()):
            ?>
                <div class="card text-white mb-3" style="background-color: #00a3c7; border-radius: 12px;">
                    <div class="card-body position-relative">
                        <p><strong>Nome:</strong> <?= htmlspecialchars($row['nome']) ?></p>
                        <!-- <p><strong>Telefone:</strong> ?<= htmlspecialchars($row['telefone']) ?></p> -->
                         <p>
                            <strong>Telefone:</strong>
                            <a href="https://wa.me/55<?= preg_replace('/\D/', '', $row['telefone']) ?>" target="_blank" style="color: #00a3c7; text-decoration: underline;">
                                <?= htmlspecialchars($row['telefone']) ?>
                            </a>
                        </p>
                        <p><strong>Veículo:</strong> <?= htmlspecialchars($row['modelo']) ?> (<?= htmlspecialchars($row['placa']) ?>)</p>
                        <p><strong>Serviço:</strong> <?= htmlspecialchars($row['servico']) ?></p>
                        <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($row['data_agendamento'])) ?></p>
                        <p><strong>Hora:</strong> <?= htmlspecialchars(substr($row['hora_agendamento'], 0, 5)) ?></p>
                        <p><strong>Leva e Traz:</strong> <?= $row['leva_e_tras'] ? 'Sim' : 'Não' ?></p>
                        <p><strong>Preço:</strong> <?= number_format($row['valor'], 2, ',', '.') ?> R$</p>
                        <p><strong>Status:</strong> <?= $row['executado'] ? 'Confirmado' : 'Não Confirmado' ?></p>

                        <div class="position-absolute top-0 end-0 m-3">
                            <!-- Botão de editar -->
                            <i class="bi bi-pencil-square me-2 icon-action btn-editar"
                                title="Editar"
                                data-id="<?= $row['idagendamentos'] ?>"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditar"
                                style="color: #00a3c7;">
                            </i>

                            <!-- Botão de detalhes -->
                            <i class="bi bi-card-text btn-detalhes" 
                                data-bs-toggle="modal"
                                data-bs-target="#modalDetalhes"
                                data-nome="<?= htmlspecialchars($row['nome']) ?>"
                                data-telefone="<?= htmlspecialchars($row['telefone']) ?>"
                                data-email="<?= htmlspecialchars($row['email']) ?>"
                                data-cpf="<?= htmlspecialchars($row['cpf']) ?>"
                                data-cep="<?= htmlspecialchars($row['cep']) ?>"
                                data-rua="<?= htmlspecialchars($row['rua']) ?>"
                                data-numero="<?= htmlspecialchars($row['numero']) ?>"
                                data-bairro="<?= htmlspecialchars($row['bairro']) ?>">
                            </i>
                            <!-- Botão de remover -->
                             <i class="bi bi-trash icon-action btn-remover"
                                title="Remover"
                                data-id="<?= $row['idagendamentos'] ?>"
                                style="color: red; cursor: pointer;">
                            </i>

                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
            </div>
        </div>
        <!-- Exibe o total do dia (apenas no mobile) -->
        <div class="text-center mt-4 d-md-none">
            <h6 class="fw-bold" style="color: black;">Total do Dia</h6>
            <p class="fw-bold" style="color: black; font-size: 1.2rem;">
                <?= number_format($total_valor_dia, 2, ',', '.') ?> R$
            </p>
        </div>
    </main>

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


                    <button type="button" class="btn w-100 mt-5" style="background-color: white; color: #444;" data-bs-dismiss="modal">
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
</section>

<!-- Script para preencher o modal dinamicamente -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const detalhesBtns = document.querySelectorAll('.btn-detalhes');
    detalhesBtns.forEach(btn => {
        btn.addEventListener('click', function () {
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
document.addEventListener('DOMContentLoaded', function () {
    const detalhesBtns = document.querySelectorAll('.btn-detalhes');

    detalhesBtns.forEach(btn => {
        btn.addEventListener('click', function () {
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

            // Monta endereço completo
            const enderecoCompleto = `${rua}, ${numero}, ${bairro}, ${cep}`;
            const enderecoEncoded = encodeURIComponent(enderecoCompleto);

            // Atualiza os links do Waze e Maps
            document.getElementById('btn-waze').href = `https://waze.com/ul?query=${enderecoEncoded}&navigate=yes`;
            document.getElementById('btn-maps').href = `https://www.google.com/maps/search/?api=1&query=${enderecoEncoded}`;
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
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: agendamentoId, status: status })
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
                Swal.fire({ icon: 'error', title: 'Erro', text: data.message || 'Erro ao atualizar o status.' });
            }
        })
        .catch(() => {
            Swal.fire({ icon: 'error', title: 'Erro', text: 'Erro na requisição.' });
        });
    });
});
</script>>

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
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: agendamentoId })
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
                        Swal.fire('Erro', data.message || 'Erro ao remover o agendamento.', 'error');
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


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
