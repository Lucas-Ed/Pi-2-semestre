<?php
//  Arquivo de inicialização do projeto.
// Define o fuso horário.
date_default_timezone_set('America/Sao_Paulo');
ob_start(); // inicia buffer de saída
// Define o caminho base do projeto (opcional, mas útil)
define('BASE_PATH', __DIR__);

// Carrega as libs necessárias do Composer
require_once __DIR__ . '/Vendor/autoload.php';

//require_once BASE_PATH . '/vendor/autoload.php';
// Agqui pode usar as classes das bibliotecas do Composer diretamente
// sem precisar de require individual para cada arquivo.
// Exemplo (se você tiver uma biblioteca chamada 'Monolog' para logs):
// use Monolog\Logger;
// use Monolog\Handler\StreamHandler

// Carrega as variáveis do .env
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Lembrete: outras configurações globais podem ser adicionadas aqui, como:
// - Configurações de sessão
// - Definição de constantes globais
// - Inicialização de algum serviço essencial

// Inicia a sessão:
if (session_status() === PHP_SESSION_NONE) {
    // Configurações de segurança para a sessão.
    ini_set('session.cookie_httponly', 1); // Impede acesso ao cookie via JS
    ini_set('session.cookie_secure', 1);   // Garante que só trafegue via HTTPS
    ini_set('session.cookie_samesite', 'Strict'); // Protege  contra ataques cross-site (CSRF)
    session_start(); // Inicia a sessão se ainda não estiver iniciada.
    
}

// incluir outros arquivos de configuração:
require_once __DIR__ . '/model/db.php';
// require_once BASE_PATH . '/config/global.php';
// define como variavel global
global $conn;

?>