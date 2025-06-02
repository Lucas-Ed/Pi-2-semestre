// Função central para executar a busca
function executarBusca() {
    const termo = document.getElementById('campoBusca').value.trim();
    console.log('Buscando:', termo); // debug opcional

    if (termo === '') {
        Swal.fire('Atenção', 'Digite algo para pesquisar.', 'warning');
        return;
    }

    // Exibe loading
    Swal.fire({
        title: 'Buscando cliente...',
        html: 'Aguarde um instante',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(`../controllers/api/buscar_clientes.php?q=${encodeURIComponent(termo)}`)
        .then(res => res.json())
        .then(data => {
            console.log('Resposta da API:', data); // debug no console

            // Fecha o loading
            Swal.close();

            if (data && data.sucesso) {
                const cliente = data.cliente;

                document.getElementById('modalNome').innerText = cliente.nome;

                const telefoneEl = document.getElementById('modalTelefone');
                telefoneEl.textContent = cliente.telefone;
                telefoneEl.href = `https://wa.me/55${cliente.telefone.replace(/\D/g, '')}`;

                document.getElementById('modalEmail').innerText = cliente.email;
                document.getElementById('modalCpf').innerText = cliente.cpf;

                const modal = new bootstrap.Modal(document.getElementById('modalDetalhes'));
                modal.show();

            } else if (data && typeof data.erro === 'string') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Cliente não encontrado',
                    text: data.erro,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    willClose: () => {
                        window.location.href = '../views/admin_usuarios.php';
                    }
                });

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Resposta inesperada do servidor.'
                });
            }
        })
        .catch(err => {
            console.error('Erro na requisição:', err);
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Erro ao buscar cliente. Tente novamente mais tarde.'
            });
        });
}

// Dispara busca ao clicar no botão
document.getElementById('botaoBusca').addEventListener('click', executarBusca);

// Dispara busca ao pressionar Enter no input
document.getElementById('campoBusca').addEventListener('keypress', function (event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        executarBusca();
    }
});