# Funcionalidades do Projeto Lava Rápido
Usuario:
usuário --> alterar agendamento[]
usuário --> cadastro [OK]
usuário --> recuperação de senha[]
usuário --> agendar serviço[]
usuário --> cadastrar veiculos[OK]
usuário --> remover veiculos[OK]
usuário --> consultar serviços[]
usuário --> cancelar agendamento[]
usuário --> consultar agendamentos[]
usuário --> login [OK]
usuário --> logout [OK]

Admin:
admin --> cancelar agendamento[]
admin --> consultar agendamentos[OK]
admin --> login [OK]
admin --> alterar data e horário[]
admin --> consultar clientes[OK]
admin --> logout [OK]

## 📂 Arquitetura(MVC) & Funcionalidades do Projeto

```bash
📂 lava_rapido/
│
├── 📂 config/
│   ├── 📄 base_url.php-(URL base do sistema)  
│   └── 📄 gateway.php-(configuração da API de pagamento)
│
├── 📂 controllers/-(lógica de negócios - salvar, listar, excluir)
│   ├── 📂 Api/
│   │    ├── 📄 get_agndamentos.php(listar agendamentos)
│   │    └── 📄 get_veiculos.php (listar veiculos)
|   |
|   ├── 📄 add_agendamento.php(adicionar agendamento)
|   ├── 📄 addcar.php(cadastrar veiculo)
|   ├── 📄 logout.php-(lógica do logout do sistema)
│   ├── 📄 processa.php- (lógica do cadastro)
|   ├── 📄 removecar.php(remover veiculo)
|   ├── 📄 remover_agendamento.php(remover agendamento)
│   └── 📄 salvar_veiculo.php(cadastrar veiculo no banco)
|
|
├── 📂 model/
│   └── 📄 db.php (configuração do banco)
│
├── 📂 public/
│   | └── 📂 js/
│   |     ├── 📄 validacao_veiculo.js- (validação de veículos)
│   |     └── 📄 welcome.js- (funcionalidades de area logada)
|   |
|   └── 📂 css/ (estilos personalizados)
|
├── 📂 vendor/
|
├── 📂 views/
│   ├── 📄 admin_agendamentos.php-(Àrea logada Admin)
│   ├── 📄 admin_usuarios.php-(Àrea logada Admin)
│   ├── 📄 cadastro.-(Página de cadastro de clientes)
│   ├── 📄 index.php-(Página Home)ok
│   ├── 📄 login.php- (Página de login de usuários)ok
│   └── 📄 welcome.php-(Àrea logada usuario cliente)
│
│
├── 📄 .env-(variáveis de ambiente)
├── 📄 .gitignore-(arquivo para github)
├── 📄 composer.json-(declara as dependências necessárias do projeto)
├── 📄 composer.lock-(registra as dependências do projeto)
└── 📄 init.php-(arquivo de inicialização)
 
```

http://localhost/sistema_2/public/api/getAppointments.php

http://localhost/sistema_2/public/api/getveiculos.php

Dado pagbank

primeiros passos
https://sandbox.pagbank.com.br/primeiros-passos.html

CC de teste:
https://sandbox.pagbank.com.br/comprador-de-testes.html

4111111111111111
Bandeira: visa Válido até: 12/2030 CVV: 123


https://developer.pagbank.com.br/reference/obter-access-token


A fazer:

em agendamentos quando não tiver agendamento nehum, exibir a mensagem "nenhum agendamento encontrado"
fazer funcionar a url base de .env
ver como funciona o .htaccess
fazer o agendamento funcionar com o botão de agendar serviço