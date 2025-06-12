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
    const cepInput = document.getElementById('cep');
    const ruaInput = document.getElementById('rua');
    const bairroInput = document.getElementById('bairro');
    const telefoneInput = document.getElementById('telefone');
    const cpfInput = document.getElementById('cpf');
    const senhaInput = document.getElementById('senha');
    const confirmaSenhaInput = document.getElementById('confirma_senha');
    const feedback = document.getElementById('senha-feedback');

    // Máscaras com IMask
    if (IMask) {
        IMask(cepInput, { mask: '00000-000' });
        IMask(cpfInput, { mask: '000.000.000-00' });
        IMask(telefoneInput, { mask: '(00) 00000-0000' });
    }

    // ViaCEP: Autopreenchimento de endereço
    cepInput.addEventListener('blur', async () => {
        const cep = cepInput.value.replace(/\D/g, '');
        if (cep.length !== 8) {
            Swal.fire('CEP inválido', 'Digite um CEP com 8 dígitos.', 'warning');
            return;
        }

        try {
            const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
            const data = await response.json();

            if (data.erro) {
                Swal.fire('CEP não encontrado', 'Verifique se o CEP está correto.', 'error');
                return;
            }

            ruaInput.value = data.logradouro;
            bairroInput.value = data.bairro;

        } catch (error) {
            Swal.fire('Erro', 'Não foi possível buscar o endereço.', 'error');
        }
    });

    // Validação de confirmação de senha em tempo real
    function verificarSenhas() {
        if (!senhaInput.value || !confirmaSenhaInput.value) {
            feedback.textContent = '';
            return;
        }

        if (senhaInput.value === confirmaSenhaInput.value) {
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
});

// document.addEventListener("DOMContentLoaded", function () {
//     const params = new URLSearchParams(window.location.search);
//     const erro = params.get('erro');
//     const status = params.get('status');

//     // Alert para erro de senha: senhas não coincidem
//     if (erro === 'senha') {
//         Swal.fire({
//             icon: 'error',
//             title: 'Oops...',
//             text: 'As senhas não coincidem.',
//             confirmButtonText: 'OK'
//         }).then(() => {
//             history.replaceState(null, '', window.location.pathname); // remove parâmetro da URL
//         });
//     }

//     // Alert para erro de e-mail já cadastrado ou CPF já cadastrado
//     else if (erro === 'email_ou_cpf') {
//         Swal.fire({
//             icon: 'error',
//             title: 'Oops...',
//             text: 'E-mail ou CPF já cadastrado!',
//             confirmButtonText: 'OK'
//         }).then(() => {
//             history.replaceState(null, '', window.location.pathname); // remove parâmetro da URL
//         });
//     }

//     // Alert para erro de e-mail inválido (sem ".com" ou ".com.br")
//     else if (erro === 'email_invalido') {
//         Swal.fire({
//             icon: 'error',
//             title: 'E-mail inválido',
//             text: 'O e-mail informado deve conter ".com" ou ".com.br".',
//             confirmButtonText: 'OK'
//         }).then(() => {
//             history.replaceState(null, '', window.location.pathname); // remove parâmetro da URL
//         });
//     }

//     //  Alert para sucesso no cadastro
//     else if (status === 'sucesso') {
//         Swal.fire({
//             // icon: 'success',
//             // title: 'Cadastro realizado!',
//             // text: 'Seu cadastro foi concluído com sucesso.',
//             // confirmButtonText: 'OK'
//             icon: "success",
//             title: "Cadastro realizado!",
//             text: "Seu cadastro foi concluído com sucesso.",
//             showConfirmButton: false,
//             timer: 2000
//         }).then(() => {
//             history.replaceState(null, '', window.location.pathname); // remove parâmetro da URL
//             window.location.href = '../views/dashboard_user.php'; // redireciona o usuário e já logado para  o dashboard do usuário.
//             // window.location.href = '../views/index.php'; // redireciona após confirmação
//         });
//     }// alert de senha redefinida com sucesso
// //     else if (status === 'senha_redefinida') {
// //             Swal.fire({
// //                 icon: 'success',
// //                 title: 'Senha redefinida!',
// //                 text: 'Você já pode fazer login com a nova senha.'
// //             });
// // }
// });
