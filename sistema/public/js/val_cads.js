document.addEventListener("DOMContentLoaded", function () {
    const params = new URLSearchParams(window.location.search);
    const erro = params.get('erro');
    const status = params.get('status');

    // Alert para erro de senha: senhas não coincidem
    if (erro === 'senha') {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'As senhas não coincidem.',
            confirmButtonText: 'OK'
        }).then(() => {
            history.replaceState(null, '', window.location.pathname); // remove parâmetro da URL
        });
    }

    // Alert para erro de e-mail já cadastrado ou CPF já cadastrado
    else if (erro === 'email_ou_cpf') {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'E-mail ou CPF já cadastrado!',
            confirmButtonText: 'OK'
        }).then(() => {
            history.replaceState(null, '', window.location.pathname); // remove parâmetro da URL
        });
    }

    // Alert para erro de e-mail inválido (sem ".com" ou ".com.br")
    else if (erro === 'email_invalido') {
        Swal.fire({
            icon: 'error',
            title: 'E-mail inválido',
            text: 'O e-mail informado deve conter ".com" ou ".com.br".',
            confirmButtonText: 'OK'
        }).then(() => {
            history.replaceState(null, '', window.location.pathname); // remove parâmetro da URL
        });
    }

    //  Alert para sucesso no cadastro
    else if (status === 'sucesso') {
        Swal.fire({
            // icon: 'success',
            // title: 'Cadastro realizado!',
            // text: 'Seu cadastro foi concluído com sucesso.',
            // confirmButtonText: 'OK'
            icon: "success",
            title: "Cadastro realizado!",
            text: "Seu cadastro foi concluído com sucesso.",
            showConfirmButton: false,
            timer: 2000
        }).then(() => {
            history.replaceState(null, '', window.location.pathname); // remove parâmetro da URL
            window.location.href = '../views/dashboard_user.php'; // redireciona o usuário e já logado para  o dashboard do usuário.
            // window.location.href = '../views/index.php'; // redireciona após confirmação
        });
    }// alert de senha redefinida com sucesso
//     else if (status === 'senha_redefinida') {
//             Swal.fire({
//                 icon: 'success',
//                 title: 'Senha redefinida!',
//                 text: 'Você já pode fazer login com a nova senha.'
//             });
// }
});
