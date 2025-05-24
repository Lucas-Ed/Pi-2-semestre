document.addEventListener('DOMContentLoaded', function() {
    console.log("Script Cadastro Veículo carregado com sucesso!");

    const marcas = {
        carro: ['Chevrolet', 'Volkswagen', 'Fiat', 'Ford', 'Toyota', 'Honda', 'BMW', 'Mercedes', 'Audi', 'Hyundai'],
        moto: ['Honda', 'Yamaha', 'Suzuki', 'Kawasaki', 'Ducati', 'Harley Davidson', 'Triumph'],
        caminhao: ['Scania', 'Mercedes', 'Volvo', 'DAF', 'Iveco', 'MAN'],
        onibus: ['Mercedes', 'Volvo', 'Scania', 'Marcopolo'],
        van: ['Mercedes', 'Renault', 'Fiat', 'Ford']
    };

    const tipoSelect = document.getElementById('tipo_veiculo');
    const marcaSelect = document.getElementById('marca_veiculo');

    tipoSelect.addEventListener('change', function() {
        const tipoSelecionado = this.value;
        const marcasDisponiveis = marcas[tipoSelecionado] || [];

        // Limpa as opções anteriores
        marcaSelect.innerHTML = '<option value="" disabled selected>Selecione a marca</option>';

        // Preenche com as marcas correspondentes
        marcasDisponiveis.forEach(marca => {
            const option = document.createElement('option');
            option.value = marca.toLowerCase().replace(/\s+/g, '_');
            option.textContent = marca;
            marcaSelect.appendChild(option);
        });
    });
});
