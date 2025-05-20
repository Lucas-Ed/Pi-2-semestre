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

// Atualiza select
function updateCarSelect(cars) {
  selectedCarSelect.innerHTML = '<option value="">Selecione um carro</option>';
  if (Array.isArray(cars)) {
    cars.forEach((car) => {
      const option = document.createElement('option');
      option.value = car.id;
      option.textContent = `${car.modelo} - ${car.placa}`;
      selectedCarSelect.appendChild(option);
    });
  }
}

// Exibe lista de carros
function displayCars(cars) {
  carsList.innerHTML = '';
  if (Array.isArray(cars) && cars.length > 0) {
    cars.forEach((car, index) => {
      const card = document.createElement('div');
      card.className = 'card mb-3';
      card.innerHTML = `
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h5 class="card-title">${car.modelo}</h5>
              <p class="card-text text-muted mb-0">Placa: ${car.placa}</p>
            </div>
            <div class="btn-group">
              <button class="btn btn-primary btn-sm" onclick="openScheduleModal(${car.id})">
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

// Carrega veículos
async function loadUserCars() {
  try {
    showSpinner(carsList);
    const res = await fetch('http://localhost/sistema_2/controllers/api/get_veiculos.php', {
      credentials: 'include'
    });
    if (!res.ok) throw new Error(`Erro HTTP: ${res.status}`);
    const data = await res.json();
    displayCars(data);
  } catch (error) {
    Swal.fire('Erro ao carregar veículos', error.message, 'error');
  }
}

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
    const res = await fetch('http://localhost/sistema_2/controllers/removecar.php', {
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
      throw new Error('Placa inválida! Use o formato AAA-12343');
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

// Agendar
document.getElementById('appointmentForm').addEventListener('submit', async (e) => {
  e.preventDefault();

  const selectedCarId = document.getElementById('selectedCar').value;
  const service = document.getElementById('service').value;
  const date = document.getElementById('date').value;
  const time = document.getElementById('time').value;
  const levaETraz = document.getElementById('leva_e_tras').checked;// comentar essa linha caso leva e tras não tenha n form

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

async function addAppointment(appointmentData) {
  try {
    const res = await fetch('../controllers/add_agendamento.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify(appointmentData)
    });

    const text = await res.text();
    console.log('Resposta bruta do servidor:', text);

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
        timer: 3000,
        showConfirmButton: false
      });
      //await loadAppointments(); // Atualiza os agendamentos após sucesso
    } else {
      throw new Error(data.message || 'Falha ao agendar.');
    }
  } catch (error) {
    Swal.fire('Erro ao agendar', error.message, 'error');
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

// Exibir agendamentos
function displayAppointments(appointmentsData) {
  appointmentsList.innerHTML = '';
  if (Array.isArray(appointmentsData) && appointmentsData.length > 0) {
    appointmentsData.forEach((appointment) => {
      const card = document.createElement('div');
      card.className = 'card mb-3';
      card.innerHTML = `
        <div class="card-body">
          <h5 class="card-title">${appointment.nome}</h5>
          <div class="row">
            <div class="col-md-6">
              <p><strong>Telefone:</strong> ${appointment.telefone}</p>
              <p><strong>Carro:</strong> ${appointment.car_modelo}</p>
              <p><strong>Placa:</strong> ${appointment.car_placa}</p>
            </div>
            <div class="col-md-6">
              <p><strong>Data:</strong> ${formatDate(appointment.data)}</p>
              <p><strong>Horário:</strong> ${appointment.hora}</p>
              <p><strong>Serviço:</strong> ${getServiceName(appointment.servico)}</p>
              ${appointment.leva_e_traz ? '<p class="text-success"><strong>Leva e Traz:</strong> Sim</p>' : ''}
            </div>
          </div>
          <div class="mt-3">
            <span class="badge bg-warning text-dark me-2">Pendente</span>
            <button class="btn btn-danger btn-sm" onclick="removeAppointment(${appointment.idagendamentos})">
              <i class="bi bi-x-circle"></i> Cancelar
            </button>
          </div>
        </div>`;
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
    const res = await fetch('../controllers/remove_ag.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: appointmentId })
    });

    const data = await res.json();

    if (data.success) {
      Swal.fire('Cancelado!', 'Agendamento cancelado com sucesso.', 'success');
      await loadAppointments();
    } else {
      throw new Error(data.message || 'Falha ao cancelar o agendamento.');
    }
  } catch (error) {
    Swal.fire('Erro ao cancelar', error.message, 'error');
  }
}

// Submissão de formulários
carForm.addEventListener('submit', (e) => {
  e.preventDefault();
  const formData = {
    modelo: document.getElementById('modelo').value,
    placa: document.getElementById('placa').value.toUpperCase()
  };
  addCar(formData);
  carForm.reset();
  addCarModal.hide();
});

appointmentForm.addEventListener('submit', (e) => {
  e.preventDefault();
  const formData = {
    name: document.getElementById('name').value,
    phone: document.getElementById('phone').value,
    date: document.getElementById('date').value,
    time: document.getElementById('time').value,
    service: document.getElementById('service').value
  };
  addAppointment(formData);
  appointmentForm.reset();
  scheduleModal.hide();
});

// Abrir modal agendamento
function openScheduleModal(carId) {
  selectedCarSelect.value = carId;
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