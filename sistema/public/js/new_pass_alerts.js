document.addEventListener("DOMContentLoaded", function () {
    const params = new URLSearchParams(window.location.search);
    const erro = params.get('erro');
    // aqui const status = params.get('status');
    let status = window.phpStatus || null;
    if (!status) {
    const params = new URLSearchParams(window.location.search);
    status = params.get('status');
}
    console.log("Status detectado:", status);
    // console.log("JS carregado: ", window.location.href);
    // console.log("location.search:", window.location.search);


//     Swal.fire({
//     // icon: 'info',
//     title: 'Alerta de teste',
//     text: 'JS e SweetAlert estão funcionando!'
// });


    if (erro === 'senhas_diferentes') {
        Swal.fire({
            icon: 'error',
            title: 'Senhas diferentes',
            text: 'A confirmação não corresponde à nova senha.',
            confirmButtonText: 'OK'
        }).then(() => history.replaceState(null, '', window.location.pathname));
    }
    else if (erro === 'falha_redefinicao') {
        Swal.fire({
            icon: 'error',
            title: 'Erro ao redefinir',
            text: 'Houve um problema ao tentar atualizar sua senha.',
            confirmButtonText: 'OK'
        }).then(() => history.replaceState(null, '', window.location.pathname));
    }
    else if (erro === 'csrf_invalido') {
        Swal.fire({
            icon: 'error',
            title: 'Sessão expirada',
            text: 'O tempo de segurança para redefinição expirou. Tente novamente.',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = '../views/recovery.php';
        });
    }
    else if (status === 'senha_redefinida') {
        console.log("STATUS detectado:", status); // <-- ESSENCIAL
        Swal.fire({
            icon: 'success',
            title: 'Senha redefinida!',
            text: 'Você já pode fazer login com a nova senha.',
            confirmButtonText: 'Ir para login'
        }).then(() => {
            window.location.href = '../views/login.php';
        });
    }
});