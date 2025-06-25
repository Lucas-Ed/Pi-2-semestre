// Script para validação de cadastro de usuário.
document.addEventListener("DOMContentLoaded", function () {
    const params = new URLSearchParams(window.location.search);
    //const erro = params.get('erro');
    const status = params.get('status');

    //  Alertas de erro
    // const errorMap = {
    //     'senha': 'As senhas não coincidem.',
    //     'email_ou_cpf': 'E-mail ou CPF já cadastrado!',
    //     'email_invalido': 'O e-mail informado é inválido.',
    //     'senha_fraca': 'A senha deve ter no mínimo 8 caracteres, contendo letras e números.',
    //     'cpf_invalido': 'O CPF informado é inválido. Informe apenas números (11 dígitos).',
    //     'telefone_invalido': 'Telefone inválido. Use o formato com DDD e número (10 a 11 dígitos).',
    //     'cep_invalido': 'CEP inválido. Informe 8 dígitos numéricos.'
    // };

    // if (erro && errorMap[erro]) {
    //     Swal.fire({
    //         icon: 'error',
    //         title: 'Oops...',
    //         text: errorMap[erro],
    //         confirmButtonText: 'OK'
    //     }).then(() => {
    //         history.replaceState(null, '', window.location.pathname);
    //     });
    // Exibe erros vindos do PHP via window.formErrors (injetado no HTML via PHP)
    if (window.formErrors && Array.isArray(window.formErrors) && window.formErrors.length > 0) {
        const htmlList = window.formErrors.map(erro => `<li>${erro}</li>`).join('');
        Swal.fire({
            icon: 'error',
            title: 'Erro ao cadastrar',
            html: `<ul style="text-align: left;">${htmlList}</ul>`,
            confirmButtonText: 'OK'
        });
    } else if (status === 'sucesso') {
        Swal.fire({
            icon: "success",
            title: "Cadastro realizado!",
            text: "Seu cadastro foi concluído com sucesso.",
            showConfirmButton: false,
            timer: 2000
        }).then(() => {
            history.replaceState(null, '', window.location.pathname);
            window.location.href = '../views/dashboard_user.php';
        });
    }

    // Seletores de campos
    // const cepInput = document.getElementById('cep');
    // const ruaInput = document.getElementById('rua');
    // const bairroInput = document.getElementById('bairro');
    // const telefoneInput = document.getElementById('telefone');
    // const cpfInput = document.getElementById('cpf');
    const senhaInput = document.getElementById('senha');
    const confirmaSenhaInput = document.getElementById('confirma_senha');
    const feedback = document.getElementById('senha-feedback');
    // console.log({
    //     senhaInput,
    //     confirmaSenhaInput,
    //     feedback
    // });

    // Máscaras com IMask
    // if (IMask) {
    //     IMask(cepInput, { mask: '00000-000' });
    //     IMask(cpfInput, { mask: '000.000.000-00' });
    //     IMask(telefoneInput, { mask: '(00) 00000-0000' });
    //}

    // ViaCEP: Autopreenchimento de endereço
    // cepInput.addEventListener('blur', async () => {
    //     const cep = cepInput.value.replace(/\D/g, '');
    //     if (cep.length !== 8) {
    //         Swal.fire('CEP inválido', 'Digite um CEP com 8 dígitos.', 'warning');
    //         return;
    //     }

    //     try {
    //         const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
    //         const data = await response.json();

    //         if (data.erro) {
    //             Swal.fire('CEP não encontrado', 'Verifique se o CEP está correto.', 'error');
    //             return;
    //         }

    //         ruaInput.value = data.logradouro;
    //         bairroInput.value = data.bairro;

    //     } catch (error) {
    //         Swal.fire('Erro', 'Não foi possível buscar o endereço.', 'error');
    //     }
    // });

    // Validação de confirmação de senha em tempo real
if (senhaInput && confirmaSenhaInput && feedback) {
    function verificarSenhas() {
        console.log("Verificando senhas...");

        const senha = senhaInput.value;
        const confirmaSenha = confirmaSenhaInput.value;

        if (!senha || !confirmaSenha) {
            feedback.textContent = '';
            feedback.classList.remove('text-success', 'text-danger');
            return;
        }

        if (senha === confirmaSenha) {
            feedback.textContent = 'As senhas coincidem ✔️';
            feedback.classList.remove('text-danger');
            feedback.classList.add('text-success');
        } else {
            feedback.textContent = 'As senhas não coincidem ❌';
            feedback.classList.remove('text-success');
            feedback.classList.add('text-danger');
        }
    }

    senhaInput.addEventListener('input', verificarSenhas);
    confirmaSenhaInput.addEventListener('input', verificarSenhas);
}
});