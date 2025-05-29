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

// Formatadores
function formatDate(date) {
  return new Date(date).toLocaleDateString('pt-BR');
}

function getServiceName(serviceValue) {
  const services = {
    'simples': 'Lavagem Simples - R$ 40,00',
    'completa': 'Lavagem Completa - R$ 70,00',
    'premium': 'Lavagem Premium - R$ 100,00'
  };
  return services[serviceValue] || 'Serviço não especificado';
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
  enceramento: { carro: 80, moto: 40, caminhao: 250 },
  polimento: { carro: 180, moto: 40, caminhao: 350, van: 1200 },
  cristalizacao: { carro: 280, van: 1200 },
  vitrificacao: { carro: 730 },
  lavagem_motor: { carro: 60, van: 80 },
  hidratacao_couro: { carro: 180, caminhao: 250 },
  higienizacao: { carro: 230, caminhao: 330, van: 800 },
  lavagem_externa: { carro: 70, moto: 40, caminhao: 250, van: 50 },
  lavagem_interna: { carro: 35, caminhao: 125, van: 50 }
};

const nomesServicos = {
  enceramento: "Enceramento",
  polimento: "Polimento",
  cristalizacao: "Cristalização",
  vitrificacao: "Vitrificação",
  lavagem_motor: "Lavagem de motor",
  hidratacao_couro: "Hidratação em couro",
  higienizacao: "Higienização",
  lavagem_externa: "Lavagem externa",
  lavagem_interna: "Lavagem interna"
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
// evento de mudança no input de data
document.getElementById('date').addEventListener('change', async function () {
  const selectedDate = new Date(this.value);
  if (!selectedDate) return;

  const day = selectedDate.getDay(); // 0 = Domingo, 6 = Sábado
  let startHour, endHour;

  if (day === 0) {
    startHour = 7; endHour = 12;
  } else if (day === 6) {
    startHour = 7; endHour = 15;
  } else {
    startHour = 7; endHour = 18;
  }

  const horariosDisponiveis = gerarHorariosDisponiveis(startHour, endHour, 40);
  const agendados = await buscarHorariosAgendados(this.value);
  const horariosFiltrados = horariosDisponiveis.filter(horario => !agendados.includes(horario));

  preencherSelectHorarios(horariosFiltrados);
});
// Gera horários disponíveis
function gerarHorariosDisponiveis(inicio, fim, intervaloMinutos) {
  const horarios = [];
  const base = new Date();
  base.setHours(inicio, 0, 0, 0);
  const limite = new Date();
  limite.setHours(fim, 0, 0, 0);

  while (base < limite) {
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
async function loadUserCars() {
  try {
    showSpinner(carsList);
    const res = await fetch('http://localhost/sistema_41/controllers/api/get_veiculos.php', {
      credentials: 'include'
    });
    if (!res.ok) throw new Error(`Erro HTTP: ${res.status}`);
    const data = await res.json();
    // console.log('Dados recebidos:', data); // Para depuração
    console.log('Veículos carregados:', data);
    displayCars(data);
  } catch (error) {
    Swal.fire('Erro ao carregar veículos', error.message, 'error');
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
      return 'logo_carros';
    case 'moto':
      return 'logo_motos';
    case 'caminhao':
      return 'logo_caminhoes';
    default:
      return 'logo_carros'; 
  }
}

function displayCars(cars) {
  carsList.innerHTML = '';
  if (Array.isArray(cars) && cars.length > 0) {
    cars.forEach((car) => {
      const card = document.createElement('div');
      card.className = 'card mb-3';
      card.innerHTML = `
        <div class="card-body d-flex align-items-center gap-3 ">
          <div class="d-flex justify-content-between align-items-center w-100">
                    <img src="../public/uploads/img/marcas/${pastaDaMarcaPorTipo(car.tipo)}/${normalizarMarca(car.marca)}.svg"
                      alt="${car.marca ?? 'Marca desconhecida'}"
                      style="width: 60px; height: auto; max-height: 100px;">
            <div>
              <h5 class="card-title mb-1">${car.modelo}</h5>
              <p class="card-text text-muted mb-0">Placa: ${car.placa}</p>
            </div>
            <div class="btn-group">
              <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#scheduleModal"
                      style="background-color: #009BBF; color: white;"
                      onclick="openScheduleModal(${car.id})">
                <i class="bi bi-calendar-plus"></i> Agendar
              </button>
              <button class="btn btn-danger btn-sm" onclick="removeCar(${car.id})">
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
// appointmentForm.addEventListener('submit', (e) => {
//   e.preventDefault();
//   const formData = {
//     name: document.getElementById('name').value,
//     phone: document.getElementById('phone').value,
//     date: document.getElementById('date').value,
//     time: document.getElementById('time').value,
//     service: document.getElementById('service').value
//   };
//   addAppointment(formData);
//   appointmentForm.reset();
//   scheduleModal.hide();
// });

document.getElementById('appointmentForm').addEventListener('submit', async (e) => {
  e.preventDefault();

  const selectedCarId = document.getElementById('selectedCar').value;
  const service = document.getElementById('serviceSelect').value;
  const date = document.getElementById('date').value;
  const time = document.getElementById('time').value;
  const levaETraz = document.getElementById('leva_tras').checked;// comentar essa linha caso leva e tras não tenha n form

  if (!selectedCarId || !service || !date || !time) {
    Swal.fire('Campos obrigatórios!', 'Preencha todos os campos.', 'warning');
    return;
  }

  const appointmentData = {
    veiculos_idveiculos: selectedCarId,
    data_agendamento: date,
    hora_agendamento: time,
    leva_e_tras: levaETraz, // Para ativar essa opção no form colocar levaETraz ou false  para não pegar essa opção
    servico: service
  };

  await addAppointment(appointmentData);
});

// função para adicionar agendamento
async function addAppointment(appointmentData) {
  try {
    const res = await fetch('../controllers/add_agendamento.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify(appointmentData)
    });

    const text = await res.text();
    //console.log('Resposta bruta do servidor:', text);

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
      //await loadAppointments(); // Atualiza os agendamentos após sucesso
    } else {
      throw new Error(data.message || 'Falha ao agendar.');
    }
  } catch (error) {
    Swal.fire('Erro ao agendar', error.message, 'error').then(() => {
            window.location.href = '../views/dashboard_user.php';
        });
  }
}

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
// Exibir agendamentos
function displayAppointments(appointmentsData) {
  appointmentsList.innerHTML = '';

  if (Array.isArray(appointmentsData) && appointmentsData.length > 0) {
    appointmentsData.forEach((appointment) => {
      const card = document.createElement('div');
      card.className = 'card mb-3';

      const levaETrazHTML = appointment.leva_e_traz
        ? `<p class="text-success"><strong>Leva e Traz:</strong> Sim</p>`
        : '';

      // HTML 
      card.innerHTML = `
        <div class="card-body">
          <h5 class="card-title">${sanitize(appointment.nome)}</h5>
          <div class="row">
            <div class="col-md-6">
              <p><strong>Telefone:</strong> ${sanitize(appointment.telefone)}</p>
              <p><strong>Veículo:</strong> ${sanitize(appointment.car_modelo)}</p>
              <p><strong>Placa:</strong> ${sanitize(appointment.car_placa)}</p>
            </div>
            <div class="col-md-6">
              <p><strong>Data:</strong> ${formatDate(appointment.data)}</p>
              <p><strong>Horário:</strong> ${sanitize(appointment.hora)}</p>
              <p><strong>Serviço:</strong> ${sanitize(appointment.servico)}</p>
              ${levaETrazHTML}
            </div>
          </div>
          <div class="mt-3">
            <span class="badge bg-warning text-dark me-2">Pendente</span>
            <button class="btn btn-danger btn-sm" onclick="removeAppointment(${appointment.idagendamentos})">
              <i class="bi bi-x-circle"></i> Cancelar
            </button>
          </div>
        </div>
      `;

      appointmentsList.appendChild(card);
    });
  } else {
    appointmentsList.innerHTML = '<p>Nenhum agendamento encontrado.</p>';
  }
}




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
    const data = await res.json();

    if (data.success) {
      Swal.fire('Cancelado!', 'Agendamento cancelado com sucesso.', 'success');
      await loadAppointments();
    } else {
      // Mensagem de cancelamento caso passe menos de 1 hora do agendamento.
      if (data.message.includes('1 hora de antecedência')) {
        Swal.fire({
          icon: 'error',
          title: 'Cancelamento não permitido',
          text: 'Você só pode cancelar agendamentos com no mínimo 1 hora de antecedência do horário agendado.'
        });         // Mensagem de cancelamento caso passe menos de 1 dia do agendamento.
      } else if (data.message.includes('data futura')) {
        Swal.fire({
          icon: 'info',
          title: 'Cancelamento inválido',
          text: 'Você só pode cancelar agendamentos com data a partir de amanhã.'
        });
      } else {
        // Erros genéricos
        Swal.fire({
          icon: 'error',
          title: 'Erro ao cancelar',
          text: data.message || 'Falha ao cancelar o agendamento.'
        });
      }
    }
  } catch (error) {
    Swal.fire('Erro ao cancelar', error.message, 'error');
  }
}

// ======================================================================================================================
// Funcionalidade de adicionar carro
// Submissão do formulário de carro
carForm.addEventListener('submit', (e) => {
  e.preventDefault();
  const formData = {
  tipo: document.getElementById('tipo').value,
  marca: document.getElementById('marca').value,
  modelo: document.getElementById('modelo').value,
  placa: document.getElementById('placa').value.toUpperCase()
  };

  // testando
//   console.log({
//   tipo: document.getElementById('tipo').value,
//   marca: document.getElementById('marca').value,
//   modelo: document.getElementById('modelo').value,
//   placa: document.getElementById('placa').value.toUpperCase()
// });

  addCar(formData);
  carForm.reset();
  addCarModal.hide();
});

// Validar placa
function isValidPlate(placa) {
  const oldFormat = /^[A-Z]{3}-\d{4}$/;
  const newFormat = /^[A-Z]{3}\d[A-Z]\d{2}$/;
  return oldFormat.test(placa) || newFormat.test(placa);
}

// Adicionar carro
async function addCar(car) {
  try {
    if (!isValidPlate(car.placa)) {
      throw new Error('Placa inválida! Use o formato AAA-1234');
    }

    const res = await fetch('../controllers/salvar_veiculo.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify(car)
    });

    const data = await res.json();

    if (data.success) {
      Swal.fire('Veículo salvo!', `${car.modelo} - ${car.placa}`, 'success');
      await loadUserCars();
    } else {
      throw new Error(data.message || 'Erro ao salvar o veículo.');
    }
  } catch (error) {
    Swal.fire('Erro', error.message, 'error');
  }
}

// ======================================================================================================================
// Remove carro
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
    if (data.success) {
      Swal.fire('Removido!', 'O carro foi removido com sucesso.', 'success');
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


// Definir data mínima
const dateInput = document.getElementById('date');
dateInput.min = new Date().toISOString().split('T')[0];

// Inicialização
window.addEventListener('DOMContentLoaded', () => {
  loadAppointments();
  loadUserCars();
  document.getElementById('addCarBtn').addEventListener('click', () => addCarModal.show());
});

// Tornar funções globais
window.removeCar = removeCar;
window.removeAppointment = removeAppointment;
window.openScheduleModal = openScheduleModal;