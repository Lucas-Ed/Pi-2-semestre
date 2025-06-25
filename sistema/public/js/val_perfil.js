// Script para validação de perfil do usuário.
document.addEventListener("DOMContentLoaded", function () {
    const params = new URLSearchParams(window.location.search);
    const status = params.get('status');

    // Exibe erros vindos do PHP via window.formErrors (injetado no HTML via PHP)
    if (window.formErrors && Array.isArray(window.formErrors) && window.formErrors.length > 0) {
        const htmlList = window.formErrors.map(erro => `<li>${erro}</li>`).join('');
        Swal.fire({
            icon: 'error',
            title: 'Erro ao atualizar perfil',
            html: `<ul style="text-align: left;">${htmlList}</ul>`,
            confirmButtonText: 'OK'
        });
    } else if (status === 'sucesso') {
        Swal.fire({
            icon: "success",
            title: "Perfil Atualizado!",
            text: "Atualização foi concluída com sucesso.",
            showConfirmButton: false,
            timer: 2000
        }).then(() => {
            history.replaceState(null, '', window.location.pathname);
            window.location.href = '../views/dashboard_user.php';
        });
    }

    // Seletores de campos
     const cepInput = document.getElementById('cepInput');
     const telefoneInput = document.getElementById('telefoneInput');
     const cpfInput = document.getElementById('cpfInput');


    // console.log({
    //     senhaInput,
    //     confirmaSenhaInput,
    //     feedback
    // });

    // Máscaras com IMask
     if (IMask) {
         IMask(cepInput, { mask: '00000-000' });
         IMask(cpfInput, { mask: '000.000.000-00' });
         IMask(telefoneInput, { mask: '(00) 00000-0000' });
    }
// Exibe modal de perfil se houver erros
if (window.formErrors && window.formErrors.length > 0) {
    const perfilModal = new bootstrap.Modal(document.getElementById('perfilModal'));
    perfilModal.show();
}


});