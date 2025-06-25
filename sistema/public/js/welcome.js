// script com todas as funcionalidades da página de dashboard do usuário.
const addCarModal = new bootstrap.Modal(document.getElementById('addCarModal'));
const scheduleModal = new bootstrap.Modal(document.getElementById('scheduleModal'));
const carForm = document.getElementById('carForm');
const appointmentForm = document.getElementById('appointmentForm');
const carsList = document.getElementById('carsList');
const appointmentsList = document.getElementById('appointmentsList');
const selectedCarSelect = document.getElementById('selectedCar');

// Nome do usuário
document.getElementById('name').value = sessionStorage.getItem('userName') || '';

let appointments = [];


function formatDate(dateString) {
  // Divide manualmente a string no formato 'YYYY-MM-DD'
  const parts = dateString.split('-');
  const year = parseInt(parts[0], 10);
  const month = parseInt(parts[1], 10) - 1; // mês começa de 0
  const day = parseInt(parts[2], 10);

  // Cria a data no fuso local, sem risco de conversão UTC
  const date = new Date(year, month, day);

  // Formata no padrão brasileiro
  return date.toLocaleDateString('pt-BR');
}

// Spinner
function showSpinner(container) {
  container.innerHTML = `
    <div class="text-center my-4">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Carregando...</span>
      </div>
    </div>`;
}
// =====================================================================================================
// Atualiza select
const servicos = {
  enceramento: { carro:150, moto: 80, caminhao: 500, van: 1250 },
  polimento: { carro: 250, moto: 120, caminhao: 600, van: 1250 },
  cristalizacao: { carro: 350, van: 1250 },
  vitrificacao: { carro: 800 },
  lavagem_motor: { carro: 60, van: 80 },
  hidratacao_couro: { carro: 180, caminhao: 250 },
  higienizacao: { carro: 300, caminhao: 455, van: 850 },
  lavagem_externa: { carro: 70, moto: 40, caminhao: 250, van: 50 },
  lavagem_completa: { van: 100 },
  lavagem_polimento_cristalizacao: { van: 1250 },
  lavagem_interna: { carro: 105, caminhao: 375, van: 100 }
};

const nomesServicos = {
  enceramento: "Lavagem  externa + enceramento",
  polimento: "Lavagem  externa + polimento",
  cristalizacao: "Lavagem  externa + cristalização",
  vitrificacao: "Lavagem  externa + vitrificação",
  lavagem_motor: "Lavagem de motor",//
  hidratacao_couro: "Hidratação em couro",//
  higienizacao: " Lavagem  externa + higienização",
  lavagem_externa: "Lavagem externa",
  lavagem_completa: "Lavagem completa",
  lavagem_polimento_cristalizacao: "Lavagem externa + polimento + cristalização",
  lavagem_interna: "Lavagem  externa + lavagem interna"
};

function normalizarTipo(tipo) {
  if (tipo === "caminhonete") return "carro";
  if (tipo === "onibus") return "caminhao";
  
  return tipo;
}

// Atualiza o select com os carros
function updateCarSelect(cars) {
  selectedCarSelect.innerHTML = '<option value="">Selecione um Veículo</option>';

  if (Array.isArray(cars)) {
    cars.forEach((car) => {
      const option = document.createElement('option');
      option.value = car.id;
      option.textContent = `${car.modelo} - ${car.placa}`;
      option.dataset.tipo = `${car.tipo}`.toLowerCase();
      selectedCarSelect.appendChild(option);
    });
  }
}

// Evento para atualizar serviços ao trocar o carro
selectedCarSelect.addEventListener("change", function () {
  const serviceSelect = document.getElementById("serviceSelect");
  const selectedOption = selectedCarSelect.options[selectedCarSelect.selectedIndex];
  const tipoRaw = selectedOption?.dataset?.tipo;

  if (!tipoRaw) {
    serviceSelect.innerHTML = '<option value="">Selecione o serviço</option>';
    return;
  }

  const tipoVeiculo = normalizarTipo(tipoRaw.toLowerCase());
  serviceSelect.innerHTML = '<option value="">Selecione o serviço</option>';

  Object.entries(servicos).forEach(([chave, precos]) => {
    if (precos[tipoVeiculo] !== undefined) {
      const option = document.createElement("option");
      option.value = chave;
      option.textContent = `${nomesServicos[chave]} - R$ ${precos[tipoVeiculo].toFixed(2)}`;
      serviceSelect.appendChild(option);
    }
  });
});

// =====================================================================================================
// Select de data e horário.
// Evento para mostrar o modal de agendamento quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function () {
  const dateInput = document.getElementById('date');
  const modal = document.getElementById('scheduleModal');

  modal.addEventListener('shown.bs.modal', () => {
    if (dateInput && dateInput.value) {
      dateInput.dispatchEvent(new Event('change'));
    }
  });
});

// evento de mudança no input de data
document.getElementById('date').addEventListener('change', async function () {
  const select = document.getElementById('time');
  select.innerHTML = '<option value="">Carregando horários...</option>';

  const selectedDate = new Date(this.value + 'T00:00:00');
  if (isNaN(selectedDate.getTime())) return;

  const day = selectedDate.getDay(); // 0 Domingo, 6 Sábado
  // console.log(`Data: ${this.value}, day=${day}`);

  let startHour, endHour;
  if (day === 0) {
    startHour = 7; endHour = 12;
  } else if (day === 6) {
    startHour = 7; endHour = 15;
  } else {
    startHour = 7; endHour = 18;
  }

  // console.log(`Início=${startHour}, Fim=${endHour}`);
  const horariosDisponiveis = gerarHorariosDisponiveis(startHour, endHour, 40);
  // console.log("Disponíveis:", horariosDisponiveis);

  const agendados = await buscarHorariosAgendados(this.value);
  // console.log("Já agendados:", agendados);

  const horariosFiltrados = horariosDisponiveis.filter(h => !agendados.includes(h));
  // console.log("Para preencher:", horariosFiltrados);

  preencherSelectHorarios(horariosFiltrados);
});

// Gera horários disponíveis
function gerarHorariosDisponiveis(inicio, fim, intervaloMinutos) {
  const horarios = [];
  const base = new Date();
  base.setHours(inicio, 0, 0, 0);
  const limite = new Date();
  limite.setHours(fim, 0, 0, 0);

  while (base <= limite) {
    const horas = base.getHours().toString().padStart(2, '0');
    const minutos = base.getMinutes().toString().padStart(2, '0');
    horarios.push(`${horas}:${minutos}`);
    base.setMinutes(base.getMinutes() + intervaloMinutos);
  }

  return horarios;
}

// busca horários agendados
async function buscarHorariosAgendados(data) {
  try {
    const res = await fetch(`../controllers/api/get_agendamentos.php?data=${data}`, {
      credentials: 'include'
    });
    const dataJson = await res.json();
    return dataJson.map(item => item.hora); // deve retornar algo como ["08:00", "08:40"]
  } catch (error) {
    console.error("Erro ao buscar agendamentos:", error);
    return [];
  }
}
// Testando sábado (07:00 às 15:00)
// console.log("Horários de sábado:");
// console.log(gerarHorariosDisponiveis(7, 15, 40));

// // Testando domingo (07:00 às 12:00)
// console.log("Horários de domingo:");
// console.log(gerarHorariosDisponiveis(7, 12, 40));

// Preenche o select de horários
function preencherSelectHorarios(horarios) {
  const select = document.getElementById('time');
  select.innerHTML = '<option value="">Selecione um horário</option>';

  horarios.forEach(hora => {
    const option = document.createElement('option');
    option.value = hora;
    option.textContent = hora;
    select.appendChild(option);
  });
}


// =====================================================================================================
// Carrega veículos

// função comentada pois não inclui veiculos ativo
// async function loadUserCars() {
//   try {
//     showSpinner(carsList);
//     const res = await fetch('../controllers/api/get_veiculos.php', {
//       credentials: 'include'
//     });
//     if (!res.ok) throw new Error(`Erro HTTP: ${res.status}`);
//     const data = await res.json();
//     // console.log('Dados recebidos:', data); // Para depuração
//     console.log('Veículos carregados:', data);
//     displayCars(data);
//   } catch (error) {
//     Swal.fire('Erro ao carregar veículos', error.message, 'error');
//   }
// }

// Carrega veículos do usuário que estão ativos.
async function loadUserCars() {
  try {
    showSpinner(carsList); // Mostra carregando

    const res = await fetch('../controllers/api/get_veiculos.php', {
      credentials: 'include'
    });
    if (!res.ok) throw new Error(`Erro HTTP: ${res.status}`);
    const data = await res.json();
    // console.log('Dados recebidos:', data); // Para depuração
    //     console.log('Veículos carregados:', data);
    if (!Array.isArray(data)) throw new Error('Resposta inválida do servidor');

    //console.log('Veículos carregados:', data); // Remova este log em produção

    displayCars(data);
  } catch (error) {
    Swal.fire('Erro ao carregar veículos', error.message, 'error');
    carsList.innerHTML = '<p class="text-danger">Falha ao carregar veículos.</p>';
  }
}


// sanitiza e normaliza a marca
function normalizarMarca(marca) {
  return (marca ?? 'default')
    .toLowerCase()
    .normalize('NFD') // remove acentos
    .replace(/[\u0300-\u036f]/g, '') // continua removendo acentos
    .replace(/\s+/g, '') // remove espaços
    .replace(/[^a-z0-9]/g, ''); // remove caracteres especiais
}

// Mapeia o logo do veículo por tipo de veículo.
function pastaDaMarcaPorTipo(tipo) {
  switch ((tipo ?? '').toLowerCase()) {
    case 'carro':
      return 'carro';
    case 'moto':
      return 'moto';
    case 'caminhao':
      return 'caminhao';
    case 'van':
      return 'van';
    case 'onibus':
      return 'onibus';
    case 'caminhonete':
      return 'caminhonete';
    default:
      return 'carro';
  }
}

// Exibe os veículos
function displayCars(cars) {
  carsList.innerHTML = '';
  if (Array.isArray(cars) && cars.length > 0) {
    cars.forEach((car) => {
      const card = document.createElement('div');
      card.className = 'card border-0';
      card.style.flex = '1 1 40%';
      card.style.minWidth = '200px';
      card.style.maxWidth = '600px';

      card.innerHTML = `
        <div class="card-body d-flex align-items-center" 
          style="box-shadow: 0 0 3px inset #009bbf; border-radius: 8px;">

          <div class="d-flex flex-column justify-content-between align-items-center w-100">

            <img src="../public/uploads/img/marcas/${pastaDaMarcaPorTipo(car.tipo)}/${normalizarMarca(car.marca)}.svg"
              alt="${car.marca ?? 'Marca desconhecida'}"
              style="width: 60px; height: auto; max-height: 100px;">

            <div class="text-center pt-2 pb-4">
              <p class="card-text text-muted mb-0 fs-5">${car.modelo}</p>
              <h5 class="card-text text-muted mb-0">${car.placa}</h5>
            </div>

            <div class="btn-group d-flex w-100 gap-2">
              <button 
                class="btn btn-sm border-0 p-3 w-50" 
                style="
                  background-color: #009BBF; 
                  color: white;
                  border-radius: 8px; 
                  box-shadow: 0 0 10px inset rgba(0,0,0,0.3);
                "
                data-bs-toggle="modal" 
                data-bs-target="#scheduleModal"
                onclick="openScheduleModal(${car.id})">
                <i class="bi bi-calendar-plus"></i> Agendar
              </button>

              <button 
                class="btn btn-sm border-0 p-3 w-50" 
                style="
                  background-color: #D9534F; 
                  color: white;
                  border-radius: 8px; 
                  box-shadow: 0 0 10px inset rgba(0,0,0,0.3);
                "
                onclick="removeCar(${car.id})">
                <i class="bi bi-trash"></i> Remover
              </button>
            </div>

          </div>
        </div>`;
      carsList.appendChild(card);
    });
  } else {
    carsList.innerHTML = '<p>Nenhum veículo cadastrado.</p>';
  }
  updateCarSelect(cars);
}
// =====================================================================================================

// Efetuar Agendamento
document.getElementById('appointmentForm').addEventListener('submit', async (e) => {
  e.preventDefault();

  const selectedCar = document.getElementById('selectedCar');
  const selectedCarId = selectedCar.value;
  const vehicleType = selectedCar.options[selectedCar.selectedIndex].getAttribute('data-tipo');

  const service = document.getElementById('serviceSelect').value;
  const date = document.getElementById('date').value;
  const time = document.getElementById('time').value;
  const levaETraz = document.getElementById('leva_tras').checked;

  if (!selectedCarId || !service || !date || !time) {
    Swal.fire('Campos obrigatórios!', 'Preencha todos os campos.', 'warning');
    return;
  }

  const tipoNormalizado = normalizarTipo(vehicleType);
  const preco = servicos[service]?.[tipoNormalizado] ?? 0;
  // debugar 
  //console.log({ service, tipoNormalizado, precoCalculado: servicos[service]?.[tipoNormalizado] });

  const appointmentData = {
    veiculos_idveiculos: selectedCarId,
    data_agendamento: date,
    hora_agendamento: time,
    leva_e_tras: levaETraz,
    servico: service,
    preco: preco
  };

  await addAppointment(appointmentData);
});

async function addAppointment(appointmentData) {
  // log para preço
  //console.log(appointmentData); // Verifique se "preco" aparece corretamente
  try {
    const agendamentoDataHora = new Date(`${appointmentData.data_agendamento}T${appointmentData.hora_agendamento}`);
    const agora = new Date();

    if (agendamentoDataHora < agora) {
      Swal.fire('Data inválida', 'Você não pode agendar para o passado.', 'error');
      return;
    }

    const res = await fetch('../controllers/add_agendamento.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify(appointmentData)
    });

    const text = await res.text();

    let data;
    try {
      data = JSON.parse(text);
    } catch (e) {
      Swal.fire('Erro de resposta', 'Resposta inesperada do servidor.', 'error');
      return;
    }

    if (data.success) {
      Swal.fire({
        icon: 'success',
        title: 'Agendamento realizado!',
        text: `Para ${appointmentData.data_agendamento} às ${appointmentData.hora_agendamento}`,
        timer: 2000,
        showConfirmButton: false
      }).then(() => {
        window.location.href = '../views/dashboard_user.php';
      });
    } else {
      throw new Error(data.message || 'Falha ao agendar.');
    }
  } catch (error) {
    Swal.fire('Erro ao agendar', error.message, 'error').then(() => {
      window.location.href = '../views/dashboard_user.php';
    });
  }
}
// ======================================================================================================================
// Carregar agendamentos
async function loadAppointments() {
  try {
    showSpinner(appointmentsList);
    const res = await fetch('../controllers/api/get_agendamentos.php', {
      credentials: 'include'
    });
    if (!res.ok) throw new Error(`Erro HTTP: ${res.status}`);
    const data = await res.json();
    displayAppointments(data);
  } catch (error) {
    Swal.fire('Erro ao carregar agendamentos', error.message, 'error');
  }
}

// Função para sanitizar strings
function sanitize(str) {
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

// =====================================================================================================
// Atualização de status dos agendamentos a cada 10 segundos.
setInterval(() => {
  fetch('../controllers/api/get_agendamentos.php')
    .then(response => response.json())
    .then(data => {
      data.forEach(agendamento => {
        const statusElement = document.querySelector(`#status_${agendamento.idagendamentos}`);
        const novoStatus = (agendamento.executado || 'Pendente').trim();
        const novoTexto = novoStatus.charAt(0).toUpperCase() + novoStatus.slice(1).toLowerCase();

        if (statusElement && statusElement.textContent !== novoTexto) {
          statusElement.textContent = novoTexto;
          statusElement.classList.add("status-updated");

          setTimeout(() => {
            statusElement.classList.remove("status-updated");
          }, 1500);
        }
      });
    })
    .catch(error => console.error("Erro ao atualizar status:", error));
}, 4000); // a cada 10 segundos


// Exibir agendamentos
function displayAppointments(appointmentsData) {
  appointmentsList.innerHTML = '';

  if (Array.isArray(appointmentsData) && appointmentsData.length > 0) {
    appointmentsData.forEach((appointment) => {
      const card = document.createElement('div');
      card.className = 'card mb-3 border-0';

      const servicoChave = appointment.servico;
      const servicoFormatado = nomesServicos[servicoChave] || servicoChave.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

      const levaETrazHTML = appointment.leva_e_traz
        ? `<p class="text-success"><strong>Leva e Traz:</strong> Sim</p>`
        : '';

      // Status
      const rawStatus = appointment.executado || 'pendente';
      const status = rawStatus.trim().toLowerCase();

      let badgeClass = 'badge bg-secondary me-2';
      let badgeText = 'Desconhecido';

      switch (status) {
        case 'pendente':
        case '':
          badgeClass = 'badge bg-warning text-dark me-2';
          badgeText = 'Pendente';
          break;
        case 'confirmado':
          badgeClass = 'badge bg-secondary me-2';
          badgeText = 'Confirmado';
          break;
        case 'fila de espera':
          badgeClass = 'badge bg-warning text-dark me-2';
          badgeText = 'Fila de espera';
          break;
        case 'em andamento':
          badgeClass = 'badge bg-info text-dark me-2';
          badgeText = 'Em andamento';
          break;
        case 'concluida':
        case 'concluído':
        case 'concluída':
          badgeClass = 'badge bg-success me-2';
          badgeText = 'Concluída';
          break;
        case 'cancelado':
          badgeClass = 'badge bg-danger me-2';
          badgeText = 'Cancelado';
          break;
        case 'expirado':
        case 'Expirado':
          badgeClass = 'badge bg-danger me-2';
          badgeText = 'Expirado';
          break;
      }

      const statusBadge = `<span id="status_${appointment.idagendamentos}" class="badge ${badgeClass} me-2">${badgeText}</span>`;

      // Preço
      const preco = parseFloat(appointment.preco_servico);
      const precoFormatado = !isNaN(preco) ? preco.toFixed(2) : '0,00';

      // HTML do card
      card.innerHTML = 
        `<div class="card-body" style="box-shadow: 0 0 3px inset #009bbf; border-radius: 8px;">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h5 class="card-title">${sanitize(appointment.nome)}</h5>
              ${statusBadge}
            </div>
            <div>
              <button class="btn" onclick="removeAppointment(${appointment.idagendamentos})">
                <i class="bi bi-trash fs-3"></i> 
              </button>
            </div>
          </div>
          <div class="">
            <div class="d-flex flex-column gap-2">
              <p class="m-0"><strong>Veículo:</strong> ${sanitize(appointment.car_modelo)}</p>
              <p class="m-0"><strong>Placa:</strong> ${sanitize(appointment.car_placa)}</p>
              <p class="m-0"><strong>Telefone:</strong> ${sanitize(appointment.telefone)}</p>
              <p class="m-0"><strong>Data:</strong> ${formatDate(appointment.data)}</p>
              <p class="m-0"><strong>Horário:</strong> ${sanitize(appointment.hora)}</p>
              <p class="m-0"><strong>Serviço:</strong> ${sanitize(servicoFormatado)}</p>
              <p class="m-0"><strong>Preço:</strong> R$ ${precoFormatado}</p>
              ${levaETrazHTML}
            </div>
          </div>
        </div>`;

      appointmentsList.appendChild(card);
    });
  } else {
    appointmentsList.innerHTML = '<p>Nenhum agendamento encontrado.</p>';
  }
}
// ======================================================================================================================
// Cancelar agendamento
async function removeAppointment(appointmentId) {
  const result = await Swal.fire({
    title: 'Cancelar agendamento?',
    text: "Você não poderá reverter esta ação!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sim, cancelar!',
    cancelButtonText: 'Voltar'
  });

  if (!result.isConfirmed) return;

  try {
  const res = await fetch('../controllers/remover_agendamento.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id: appointmentId })
  });

  const isJson = res.headers.get('content-type')?.includes('application/json');

  if (!res.ok || !isJson) {
    throw new Error('Resposta inválida do servidor.');
  }

  const data = await res.json();

  if (data.success) {
    Swal.fire({
      icon: "success",
      title: "Cancelado!",
      text: "Agendamento cancelado com sucesso.",
      showConfirmButton: false,
      timer: 2000
    }).then(() => {
      window.location.href = '../views/dashboard_user.php';
    });
  } else {
    // Tratamento de erros específicos
    const msg = data.message.toLowerCase();
    let icon = 'error';
    let title = 'Erro ao cancelar';
    let text = data.message;

    if (msg.includes('1 hora')) {
      title = 'Cancelamento não permitido';
      text = 'Você só pode cancelar agendamentos com no mínimo 1 hora de antecedência.';
    } else if (msg.includes('fila de espera')) {
      title = 'Cancelamento não permitido';
      text = 'Agendamentos com status na fila de espera não podem ser cancelados.';
    } else if (msg.includes('em andamento')) {
      title = 'Cancelamento não permitido';
      text = 'Agendamentos com status em andamento não podem ser cancelados.';
    }

    Swal.fire({ icon, title, text }).then(() => {
      window.location.href = '../views/dashboard_user.php';
    });
  }
} catch (error) {
  Swal.fire({
    icon: 'error',
    title: 'Erro inesperado',
    text: error.message || 'Erro ao processar a requisição.'
  }).then(() => {
    window.location.href = '../views/dashboard_user.php';
  });
}
}

// ======================================================================================================================
// Funcionalidade de adicionar veículo.
carForm.addEventListener('submit', (e) => {
  e.preventDefault();

  const formData = {
  tipo: document.getElementById('tipo').value.trim(),
  marca: document.getElementById('marca').value.trim(),
  modelo: document.getElementById('modelo').value.trim(),
  placa: document.getElementById('placa').value.trim().toUpperCase()
}

  // testando
//   console.log({
//   tipo: document.getElementById('tipo').value,
//   marca: document.getElementById('marca').value,
//   modelo: document.getElementById('modelo').value,
//   placa: document.getElementById('placa').value.toUpperCase()
// });

if (!formData.tipo || !formData.marca || !formData.modelo || !formData.placa) {
  Swal.fire({
    icon: 'warning',
    title: 'Campos obrigatórios',
    text: 'Por favor, preencha todos os campos antes de salvar.'
  });
  return;
}
  addCar(formData);
  // carForm.reset(); //  Resetaro form.
  // addCarModal.hide(); // Fechar modal
});


// Função para validar placas de veículos
function isValidPlate(placa) {
  if (typeof placa !== 'string') return false;

  const normalized = placa.trim().toUpperCase();

  const oldFormatWithHyphen = /^[A-Z]{3}-\d{4}$/;
  const oldFormatNoHyphen   = /^[A-Z]{3}\d{4}$/;
  const newMercosulFormat   = /^[A-Z]{3}\d[A-Z]\d{2}$/;

  return (
    oldFormatWithHyphen.test(normalized) ||
    oldFormatNoHyphen.test(normalized) ||
    newMercosulFormat.test(normalized)
  );
}


// Adicionar carro
async function addCar(car) {
  try {
    if (!isValidPlate(car.placa)) {
      throw new Error('Placa inválida! Use o formato ABC-1234, ABC1234 ou ABC1D23');
    }
    //Desabilitar o botão de envio para evitar múltiplos cliques
    const submitBtn = document.getElementById('submitCarBtn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Salvando...'; // opcional
    // Adicionar token CSRF se necessário
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const res = await fetch('../controllers/salvar_veiculo.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json',
                  'X-CSRF-Token': csrfToken // Adiciona o token CSRF
       },
      credentials: 'include',
      body: JSON.stringify(car)
    });

    const data = await res.json();

    if (data.success) {
      Swal.fire({
        icon: 'success',
        title: 'Veículo salvo!',
        text: `${car.modelo} - ${car.placa}`,
        timer: 2000,
        showConfirmButton: false
      }).then(() => {
        carForm.reset();         //  Resetar só após sucesso
        addCarModal.hide();      // Fechar modal só após sucesso
        window.location.href = '../views/dashboard_user.php';
      });
    } else {
      throw new Error(data.message || 'Erro ao salvar o veículo.');
    }
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Erro',
      text: error.message,
    });
  } finally {
    // Reativar botão sempre (sucesso ou erro)
    submitBtn.disabled = false;
    submitBtn.textContent = 'Salvar';
  }
}

// ======================================================================================================================
// Remove veículo
async function removeCar(carId) {
  const result = await Swal.fire({
    title: 'Tem certeza?',
    text: "Você não poderá reverter esta ação!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sim, remover!',
    cancelButtonText: 'Cancelar'
  });

  if (!result.isConfirmed) return;

  try {
    const res = await fetch('../controllers/removecar.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({ id: carId })
    });
     const data = await res.json();
    //const text = await res.text(); // <-- NÃO usa res.json() ainda
    //console.log('Resposta bruta do PHP:', text); // <-- Ver o conteúdo no console
    if (data.success) {
      Swal.fire({
        icon: 'success',
        title: 'Veículo removido!',
        text: 'O veículo foi removido com sucesso.',
        timer: 2000,
        showConfirmButton: false
      }).then(() => {
        window.location.href = '../views/dashboard_user.php';
      });
      // Atualizar a lista de veículos e agendamentos
      await loadUserCars();
      await loadAppointments();
    } else {
      throw new Error(data.message || 'Erro ao remover veículo.');
    }
  } catch (error) {
    Swal.fire('Erro', error.message, 'error');
  }
}

// =====================================================================================================


// Abrir modal agendamento
// function openScheduleModal(carId) {
//   selectedCarSelect.value = carId;
//   scheduleModal.show();
// }
// Abrir modal agendamento sem um veículo pré selecionado.
function openScheduleModal() {
  // Resetar para a opção padrão
  selectedCarSelect.selectedIndex = 0;

  // Limpar o select de serviços
  const serviceSelect = document.getElementById("serviceSelect");
  serviceSelect.innerHTML = '<option value="">Selecione o serviço</option>';

  // Limpar os outros campos do formulário
  document.getElementById('date').value = '';
  document.getElementById('time').selectedIndex = 0;
  document.getElementById('leva_traz').checked = false;

  // Mostrar o modal
  scheduleModal.show();
}

// =====================================================================================================
// Definir data mínima
const dateInput = document.getElementById('date');
// dateInput.min = new Date().toISOString().split('T')[0];
const today = new Date();
const localDate = new Date(today.getTime() - today.getTimezoneOffset() * 60000)
  .toISOString()
  .split('T')[0];
dateInput.min = localDate;


// Inicialização
window.addEventListener('DOMContentLoaded', () => {
  loadAppointments();
  loadUserCars();
  // document.getElementById('addCarBtn').addEventListener('click', () => addCarModal.show());
  document.getElementById('addCarBtn').addEventListener('click', async () => {
  try {
    const res = await fetch('../controllers/api/verifica_perfil.php', {
      credentials: 'include'
    });
    const data = await res.json();

    if (data.status === 'completo') {
      addCarModal.show();
    } else if (data.status === 'nao_logado') {
      Swal.fire({
        icon: 'error',
        title: 'Sessão expirada',
        text: 'Por favor, faça login novamente.',
      }).then(() => {
        window.location.href = '../views/index.php';
      });
    } else {
      Swal.fire({
        icon: 'warning',
        title: 'Complete seu perfil',
        html: 'Antes de adicionar um veículo, complete seu perfil com:<br><strong>CPF, telefone e endereço</strong>.',
        confirmButtonText: 'Ir para perfil'
      }).then(() => {
          const perfilModal = new bootstrap.Modal(document.getElementById('perfilModal')); // abre o modal de meu perfil
          perfilModal.show();
      });
    }
  } catch (error) {
    console.error('Erro ao verificar perfil:', error);
    Swal.fire({
      icon: 'error',
      title: 'Erro',
      text: 'Não foi possível verificar os dados do perfil.',
    });
  }
});

});

// Tornar funções globais
window.removeCar = removeCar;
window.removeAppointment = removeAppointment;
window.openScheduleModal = openScheduleModal;