
<h1 align="center"> Projeto Interdisciplinar | DSM</h1>
<p align="center">

  ![]()
  
</p>

<p align="center">
Projeto Interdisciplinar 2° semestre, do curso <a href="https://fatecararas.cps.sp.gov.br/tecnologia-em-desenvolvimento-de-softwares-multiplataforma/">DSM- Desenvolvimento de software multiplataforma.</a>

<p align="center">
  <!-- <img alt="License" src="https://img.shields.io/static/v1?label=license&message=MIT&color=49AA26&labelColor=000000"> -->
</p>
<br>

<!-- Licença Creative Commons 4.0 não comercial, para mais informações acesse o link:

[![License: CC BY-NC 4.0](https://img.shields.io/badge/License-CC_BY--NC_4.0-lightgrey.svg)](https://creativecommons.org/licenses/by-nc/4.0/) -->

<!-- <h3 align="center">✅ Concluído ✅</h3> -->

<!-- <p align="center">
 <a href="#-Projeto">Sobre o projeto</a> •
 <a href="#-tecnologias">Tecnologias</a> • 
 <a href="#-layout">Layout</a> • 
<a href="## 🛠 Métodologia ágil-( Scrum ) ">Métodologia ágil</a> •
<a href="#-Autores ">Autores</a> •
<a href="#-Deploy-do-projeto">Deploy</a> •
<a href="#Licença">Licença</a>
</p> -->

<p align="center">
 <a href="#-tecnologias">Tecnologias</a> • 
 <a href="#-layout-do-projeto">Layout</a> • 
 <a href="#-metodologia-ágil">Metodologia ágil</a> •
 <a href="#-Api ">Api</a> •
 <!-- <a href="#Acessibilidade">Acessibilidade</a> -->
 <a href="#-Deploy">Deploy</a> •
 <a href="#-Live">Live</a> •
  <a href="#-autores">Autores</a> •
 <a href="#memo-licença">Licença</a>
 
</p>


<!-- ## 🚀 Tecnologias


Esse projeto foi desenvolvido com as seguintes tecnologias:

<p align="center">
  <!-- <img src="https://img.shields.io/badge/JavaScript-323330?style=for-the-badge&logo=javascript&logoColor=F7DF1E">
  <img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black"/>
  <img src="https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white"/>
  <img src="https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white"/>
  <img src="https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white"/>

</p> -->

 ## 💻 Layout do Projeto

<!-- ![](/img/gif_apresentação.gif) -->

Veja o layout completo  [aqui.](https://www.figma.com/design/PpyOYg0jN5SyXKjDlWTspq/Fatec-Projeto-Integrador-2%C2%BA-S?node-id=0-1&p=f)


 <!-- ## 🛠 Metodologia ágil

Para o gerenciamento, do projeto, foi utilizado o [Trello](https://trello.com/invite/b/670a36ac9fdfb633bd12bc42/ATTIc0bd37a0dad55feb71e78e437d7367886CFD379C/fatec-pi-documentacao-casa-sonia-fashion), solicite acesso para vizualizar ao quadro no link anterior;  O Trello é uma ferramenta para melhor controle e divisão de tarefas entre os autores, ficando da seguinte forma igual da imagem abaixo:

![](img/trello.png) -->
<!--
## 🧩 Api 
Foi construída uma API para o projeto em NodeJs, acesse [aqui](https://github.com/Lucas-Ed/Backend_grupo02_pi), o repositório da Api, para mais informações. -->
<!-- ## 🤝🏼 Acessibilidade

![](/img/acessibilidade.mp4) -->

<!-- Apresentação do projeto interdiciplinar,
Confira [aqui.](https://lucas-ed.github.io/grupo-02_pi/#1)

Acesse a documentação do projeto [aqui.](https://github.com/Lucas-Ed/grupo-02_pi/blob/main/Documentação/PI%20-%20Documentação.pdf)

## 📲 Deploy

Acesse o site do Pi [aqui.]()

# 👓 Live

<p>Assista a Live de Apresentação do projeto !</p>
<p>No video abaixo, veja a apresentação do grupo 02.</p>

[![Watch the video](./img/capa_video.PNG)](https://www.youtube.com/watch?v=jeLNnmUUFrM) -->

Para rodar o sistema localmente, siga os passos abaixo:

0. Requisito, ter o composer instalado na máquina, caso não tenha baixe [aqui.](https://getcomposer.org/download/)
1. Clone o repositório.
2. Ligue o servidor/Mysql local (XAMPP ou WAMP).
3. Coloque a pasta sistema, na pasta "htdocs" do XAMPP ou WAMP.
4. Dentro da pasta sistema, pelo terminal, instale as dependências com o comando:

```bash
composer install
```

5. Importe o backup do banco de dados "lava_rapido.sql" que esta na pasta `modelagem de banco` no phpmyadmin.
6. Acesse o sistema pelo navegador, no endereço: `http://localhost/sistema`.
7. Abra o sistema clicando na pasta views.
8. Pronto você verá a página Home do sistema.

## 📂 Arquitetura do Projeto

```bash
📂 lava_rapido/ (pasta raiz)
|
├── 📂 Config/ (arquivos de configurações)
│    ├── mercado_pago.php (configuração da API de pagamento)
|
|
├── 📂 controllers/ (lógica de negócios - salvar, listar, excluir)
│ ├── processa.php-(lógica do cadastro)
| └──logout.php-(lógica do logout do sistema)
|
├── 📂 Model/ (arquivos de conexão com o banco de dados)
│    ├── config.php (configuração do banco)
│ 
│
├── 📂 public/ (arquivos estáticos como CSS, JS, imagens)
│    ├── 📂 css/ (Bootstrap e estilos personalizados)
│    ├── 📂 js/ (scripts)
│    └── 📂 images/ (carrossel de imagens)
|
├── 📂Vendor (Blibliotecas do sistema)
|
├── 📂 views/ (páginas do sistema)
│    ├── cadastro.php (formulário de login)
│    ├── index.php (página inicial com navbar e carrossell)
│    ├── login.php (login de usuários)
│    ├── welcome.php (área logada de usuario)
│    ├── admin_agendamentos.php (listagem de agendamentos para admin)
│    └──admin_usuarios.php (listagem de usuários para admin)
|
|
├── .env (variáveis de ambiente)
├── init.php (arquivo de inicialização)
├── .gitignore (arquivo para github)
├── composer.json (declara as dependências necessárias do projeto)
└── composer.lock (registra as dependências do projeto)
```

<br>

## 👨🏼‍🎓 Autores
<table>
  <tr>
    <td align="center">
      <a href="https://github.com/Lucas-Ed">
        <img src="https://avatars.githubusercontent.com/u/30055762?v=4" width="100px;" alt="Lucas"/>
        <br />
        <sub>
          <b>Lucas Eduardo</b>
        </sub>
       </a>
       <br />
       <a href="https://www.instagram.com/lucas.eduardo007/" title="Instagram">@lucas.eduardo007</a> 
       <br />
    </td> 
    <td align="center">
      <a href="https://github.com/eliabe36i">
        <img src="https://avatars.githubusercontent.com/u/80930943?v=4" width="100px;" alt=""/>
        <br />
        <sub>
          <b> Eliabe Leme</b>
        </sub>
       </a>
       <br />
       <a href="https://www.instagram.com/lemeeliabe" title="Instagram">@lemeeliabe</a>
       <br />
    </td>
     <td align="center">
      <a href="https://github.com/brunorod07">
        <img src="https://avatars.githubusercontent.com/u/183766962?v=4" width="100px;" alt=""/>
        <br />
        <sub>
          <b>Bruno E. Rodrigues</b>
        </sub>
       </a>
       <br />
       <a href="https://www.instagram.com/brunorod07" title="instagram">@brunorod07</a>
       <br />
    </td>
     <td align="center">
      <a href="https://github.com/Paulino-Willian">
        <img src="https://avatars.githubusercontent.com/u/179543395?v=4" width="100px;" alt=""/>
        <br />
        <sub>
          <b>Willian Paulino</b>
        </sub>
       </a>
       <br />
       <a href="https://www.instagram.com/tatu_wp" title="instagram">@tatu_wp</a>
       <br />
    </td>
  </table>
  <br>

Professor, <a href="https://github.com/orlandosaraivajr">Orlando Saraiva.</a>


  ---
<!-- ## :memo: Licença

Esse projeto está sob a licença Creative Commons Attribution-NonCommercial 4.0 (CC BY-NC 4.0). -->

---


