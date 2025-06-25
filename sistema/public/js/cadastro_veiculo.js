// Script para o cadastro de veículos do modal.
document.addEventListener('DOMContentLoaded', function () {
  //console.log("Script Cadastro Veículo carregado com sucesso!");

  const tipoSelect = document.getElementById('tipo');
  const marcaSelect = document.getElementById('marca');

  const marcasPorTipo = {
    carro: ['lexus','audi','alfa_romeo','rolls_royce','Fiat', 'volkswagen', 'chevrolet', 'toyota', 'honda', 'hyundai', 'ford', 'renault', 'nissan', 'citroen', 'peugeot', 'kia', 'mitsubishi', 'chery', 'lifan', 'jac', 'volvo'],
    moto: ['husqvarna', 'mv_augusta','honda', 'yamaha',"triumph", 'suzuki', 'kawasaki', 'ducati', 'triumph', 'shineray', 'dafra', 'sundown', 'bmw', 'royalenfield', 'harley', 'ktm', 'voltz', 'gws'],
    caminhao: ['man','foton','scania', 'mercedes-Benz', 'Volvo', 'Iveco', 'daf', 'ford', 'chevrolet', 'fiat', 'volkswagen'],
    van: ['fiat', 'renault', 'mercedes_benz', 'ford', 'volkswagen', 'chevrolet', 'citroen', 'peugeot', 'nissan', 'hyundai', 'kia'],
    onibus: ['mercedes_benz', 'volvo', 'scania', 'marcopolo', 'iveco', 'volkswagen', 'neobus', 'trolebus', 'volare', 'mascarello', 'comil', 'irizar', 'viação'],
    caminhonete: ['land_rover','fiat', 'volkswagen', 'chevrolet', 'toyota', 'honda', 'hyundai', 'ford', 'renault', 'nissan', 'jeep' , 'citroen', 'peugeot', 'kia', 'mitsubishi', 'chery', 'troller', 'lifan', 'jac', 'volvo']
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
