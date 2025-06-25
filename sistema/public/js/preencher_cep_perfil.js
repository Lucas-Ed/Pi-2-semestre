// Script para preencher os campos de endereço com base no CEP, para modal de perfil.
document.addEventListener('DOMContentLoaded', () => {
    const cepInput = document.getElementById('cepInput');
    const ruaInput = document.getElementById('ruaInput');
    const bairroInput = document.getElementById('bairroInput');

    // Captura o valor inicial do CEP que veio da sessão PHP
    const cepInicial = cepInput ? cepInput.value.replace(/\D/g, '') : '';

    if (!cepInput || !ruaInput || !bairroInput) return;

    cepInput.addEventListener('blur', async () => {
        const cepAtual = cepInput.value.replace(/\D/g, '');

        if (cepAtual === cepInicial) {
            return; // Não faz nada se o CEP não foi alterado
        }

        if (cepAtual.length !== 8) {
            Swal.fire('CEP inválido', 'Digite um CEP com 8 dígitos.', 'warning');
            return;
        }

        try {
            const response = await fetch(`https://viacep.com.br/ws/${cepAtual}/json/`);
            const data = await response.json();

            if (data.erro) {
                Swal.fire('CEP não encontrado', 'Verifique se o CEP está correto.', 'error');
                return;
            }

            ruaInput.value = data.logradouro || '';
            bairroInput.value = data.bairro || '';

        } catch (error) {
            Swal.fire('Erro', 'Não foi possível buscar o endereço.', 'error');
        }
    });
});