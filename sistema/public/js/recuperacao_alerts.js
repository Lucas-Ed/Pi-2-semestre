document.addEventListener("DOMContentLoaded", function () {
    const params = new URLSearchParams(window.location.search);
    const erro = params.get('erro');
    const status = params.get('status');

    if (erro === 'email_invalido') {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'E-mail não consta em nossa base de dados!',
            confirmButtonText: 'OK'
        });
    } else if (erro === 'email_envio') {
        Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: 'Falha ao enviar e-mail. Tente novamente.',
            confirmButtonText: 'OK'
        });
    } else if (erro === 'codigo_incorreto') {
        Swal.fire({
            icon: 'error',
            title: 'Código incorreto!',
            text: 'O código digitado não corresponde.',
            confirmButtonText: 'OK'
        });
    } else if (erro === 'token_expirado') {
        Swal.fire({
            icon: 'error',
            title: 'Código expirado!',
            text: 'O código expirou. Solicite um novo.',
            confirmButtonText: 'OK'
        });
    } else if (status === 'codigo_enviado') { // Alert para o envio de e-mail com o código
        Swal.fire({
                icon: "success",
                title: "Código enviado!",
                text: "Verifique seu e-mail. Código válido por 2 horas.",
                showConfirmButton: false,
                timer: 2000
        }).then(() => {
            window.location.href = '../views/validacao_cod.php'; 
        });
    } else if (status === 'codigo_validado') {
        Swal.fire({
            icon: 'success',
            title: 'Código validado!',
            text: 'Redirecionando para redefinir senha...',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            window.location.href = '../views/alter_pass.php'; 
        });
    }

    // Limpa os parâmetros da URL
    if (erro || status) {
        history.replaceState(null, '', window.location.pathname);
    }
});
