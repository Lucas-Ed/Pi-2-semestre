document.addEventListener('DOMContentLoaded', function () {
  console.log("Script Cadastro Veículo carregado com sucesso!");

  const tipoSelect = document.getElementById('tipo');
  const marcaSelect = document.getElementById('marca');

  const marcasPorTipo = {
    carro: ['Fiat', 'Volkswagen', 'Chevrolet', 'Toyota', 'Honda', 'Hyundai', 'Ford', 'Renault', 'Nissan', 'Citroën', 'Peugeot', 'Kia', 'Mitsubishi', 'Chery', 'Lifan', 'JAC Motors', 'Volvo'],
    moto: ['Honda', 'Yamaha', 'Suzuki', 'Kawasaki', 'Ducati', 'Triumph', 'Shineray', 'Dafra', 'sundown', 'BMW', 'Royal Enfield', 'Harley Davidson', 'ktm', 'Voltz', 'GWS'],
    caminhao: ['Scania', 'Mercedes-Benz', 'Volvo', 'Iveco', 'MAN', 'DAF', 'Ford', 'Chevrolet', 'Fiat', 'Volkswagen'],
    van: ['Fiat', 'Renault', 'Mercedes-Benz', 'Ford', 'Volkswagen', 'Chevrolet', 'Citroën', 'Peugeot', 'Nissan', 'Hyundai', 'Kia'],
    onibus: ['Mercedes-Benz', 'Volvo', 'Scania', 'Marcopolo', 'Iveco', 'Volkswagen', 'Caio', 'Neobus', 'Trolebus', 'Volare', 'Mascarello', 'Comil', 'Irizar', 'Viação', 'G7'],
    caminhonete: ['Fiat', 'Volkswagen', 'Chevrolet', 'Toyota', 'Honda', 'Hyundai', 'Ford', 'Renault', 'Nissan', 'Jeep' , 'Citroën', 'Peugeot', 'Kia', 'Mitsubishi', 'Chery', 'Troller', 'Lifan', 'JAC Motors', 'Volvo']
  };

  // Popula o select de tipo
  const tipos = Object.keys(marcasPorTipo);
  tipoSelect.innerHTML = '<option value="" disabled selected>Selecione o tipo</option>';
  tipos.forEach(tipo => {
    const option = document.createElement('option');
    option.value = tipo;
    option.textContent = tipo.charAt(0).toUpperCase() + tipo.slice(1);
    tipoSelect.appendChild(option);
  });

  // Quando o tipo é selecionado
  tipoSelect.addEventListener('change', function () {
    const tipoSelecionado = this.value;
    const marcas = marcasPorTipo[tipoSelecionado] || [];

    marcaSelect.innerHTML = '<option value="" disabled selected>Selecione a marca</option>';

    marcas.forEach(marca => {
      const option = document.createElement('option');
      option.value = marca.toLowerCase().replace(/\s+/g, '_');
      option.textContent = marca;
      marcaSelect.appendChild(option);
    });
  });
});

// document.addEventListener('DOMContentLoaded', function () {
//     console.log("Script Cadastro Veículo carregado com sucesso!");

//     const marcas = {
//         carro: ['Fiat', 'Volkswagen', 'Chevrolet', 'Toyota', 'Hyundai', 'Jeep', 'Nissan', 'Renault', 'Honda', 'Ford'],
//         moto: ['Honda', 'Yamaha', 'Shineray', 'Haojue', 'Royal Enfield', 'Avelloz', 'BMW', 'Triumph', 'Suzuki', 'Kawasaki'],
//         caminhao: ['Scania', 'Mercedes-Benz', 'Volvo', 'DAF', 'Iveco', 'MAN'],
//         onibus: ['Mercedes-Benz', 'Volvo', 'Scania', 'Marcopolo'],
//         van: ['Mercedes-Benz', 'Renault', 'Fiat', 'Ford']
//     };

//     const modelos = {
//         // Carros
//         fiat: ['Strada', 'Argo', 'Mobi', 'Pulse', 'Cronos', 'Toro'],
//         volkswagen: ['Polo', 'T-Cross', 'Nivus', 'Virtus', 'Saveiro'],
//         chevrolet: ['Onix', 'Tracker', 'S10', 'Spin', 'Montana'],
//         toyota: ['Corolla', 'Yaris', 'Hilux', 'Corolla Cross'],
//         hyundai: ['HB20', 'Creta'],
//         jeep: ['Renegade', 'Compass'],
//         nissan: ['Kicks', 'Versa'],
//         renault: ['Kwid', 'Duster'],
//         honda: ['Civic', 'HR-V'],
//         ford: ['Ranger', 'Territory'],

//         // Motos
//         honda: ['CG 160', 'Biz', 'Pop 110i', 'NXR 160 Bros', 'CB 300F Twister', 'PCX 160'],
//         yamaha: ['YBR 150 Factor', 'XTZ 250 Lander', 'FZ25 Fazer', 'FZ15', 'Crosser 150'],
//         shineray: ['Jet 50', 'XY 50', 'Phoenix 50'],
//         haojue: ['DK 150', 'NK 150'],
//         'royal enfield': ['Hunter 350', 'Meteor 350'],
//         avelloz: ['AZ Moby', 'AZ Moby S'],
//         bmw: ['G 310 R', 'G 310 GS'],
//         triumph: ['Street Twin', 'Bonneville T100'],
//         suzuki: ['GSX-S750', 'V-Strom 650'],
//         kawasaki: ['Ninja 400', 'Z400'],

//         // Caminhões
//         scania: ['R440', 'R500'],
//         'mercedes-benz': ['Actros', 'Axor'],
//         volvo: ['FH 540', 'VM 270'],
//         daf: ['XF', 'CF'],
//         iveco: ['Hi-Way', 'Tector'],
//         man: ['TGX', 'TGS'],

//         // Ônibus
//         marcopolo: ['Paradiso 1200', 'Viaggio 1050'],

//         // Vans
//         'mercedes-benz': ['Sprinter'],
//         renault: ['Master'],
//         fiat: ['Ducato'],
//         ford: ['Transit']
//     };

//     const tipoSelect = document.getElementById('tipo');
//     const marcaSelect = document.getElementById('marca');
//     const modeloSelect = document.getElementById('modelo');

//     tipoSelect.addEventListener('change', function () {
//         const tipoSelecionado = this.value;
//         const marcasDisponiveis = marcas[tipoSelecionado] || [];

//         // Limpa as opções anteriores
//         marcaSelect.innerHTML = '<option value="" disabled selected>Selecione a marca</option>';
//         modeloSelect.innerHTML = '<option value="" disabled selected>Selecione o modelo</option>';

//         // Preenche com as marcas correspondentes
//         marcasDisponiveis.forEach(marca => {
//             const option = document.createElement('option');
//             option.value = marca.toLowerCase().replace(/\s+/g, '_');
//             option.textContent = marca;
//             marcaSelect.appendChild(option);
//         });
//     });

//     marcaSelect.addEventListener('change', function () {
//         const tipoSelecionado = tipoSelect.value;
//         const marcaSelecionada = this.value;
//         const modelosDisponiveis = modelos[marcaSelecionada] || [];

//         // Limpa as opções anteriores
//         modeloSelect.innerHTML = '<option value="" disabled selected>Selecione o modelo</option>';

//         // Preenche com os modelos correspondentes
//         modelosDisponiveis.forEach(modelo => {
//             const option = document.createElement('option');
//             option.value = modelo.toLowerCase().replace(/\s+/g, '_');
//             option.textContent = modelo;
//             modeloSelect.appendChild(option);
//         });
//     });
// });
