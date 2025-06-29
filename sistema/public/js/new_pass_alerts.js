// Script para exibir alertas e validar senhas na página de redefinição de senha.
document.addEventListener("DOMContentLoaded", function () {
    const params = new URLSearchParams(window.location.search);
    const erro = params.get('erro');
    // aqui const status = params.get('status');
    const status = window.phpStatus || params.get('status');
    // dados do form das senhas.
    const senhaInput = document.querySelector('input[name="senha"]');
    const confirmaSenhaInput = document.querySelector('input[name="nova_senha"]');
    const feedback = document.getElementById('senha-feedback');

//     let status = window.phpStatus || null;
//     if (!status) {
//     const params = new URLSearchParams(window.location.search);
//     status = params.get('status');
// }
//     console.log("Status detectado:", status);
    // console.log("JS carregado: ", window.location.href);
    // console.log("location.search:", window.location.search);


//     Swal.fire({
//     // icon: 'info',
//     title: 'Alerta de teste',
//     text: 'JS e SweetAlert estão funcionando!'
// });

    // Tratamento de erros
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
    } else if (erro === 'senha_fraca') {
            Swal.fire({
                icon: 'warning',
                title: 'Senha muito curta',
                text: 'A senha deve ter pelo menos 8 caracteres e conter letras e números.',
                showConfirmButton: false,
                timer: 3000
            }).then(() => {
                history.replaceState(null, '', window.location.pathname);
            });
  } // else if (status === 'senha_redefinida') {
//           //console.log("STATUS detectado:", status); // para depuração
//               Swal.fire({
//                   icon: "success",
//                   title: "Senha redefinida!",
//                   text: "Login automático autorizado",
//                   showConfirmButton: false,
//                   timer: 3000
//                   }).then(() => {
//                   window.location.href = '../views/dashboard_user.php';
//                   });
//       }

    // Validação de confirmação de senha em tempo real
    if (senhaInput && confirmaSenhaInput && feedback) {
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
    }
});

