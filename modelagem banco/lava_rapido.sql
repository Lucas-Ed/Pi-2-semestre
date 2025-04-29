-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 29/04/2025 às 04:50
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `lava_rapido`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamentos`
--

CREATE TABLE `agendamentos` (
  `idagendamentos` int(10) UNSIGNED NOT NULL,
  `usuarios_idusuarios` int(10) UNSIGNED NOT NULL,
  `veiculos_idveiculos` int(10) UNSIGNED NOT NULL,
  `data_agendamento` date NOT NULL,
  `hora_agendamento` time NOT NULL,
  `leva_e_tras` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cartoes`
--

CREATE TABLE `cartoes` (
  `idcartoes` int(10) UNSIGNED NOT NULL,
  `usuarios_idusuarios` int(10) UNSIGNED NOT NULL,
  `nome_titular` tinyblob NOT NULL,
  `numero_cartao` tinyblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `enderecos`
--

CREATE TABLE `enderecos` (
  `idenderecos` int(10) UNSIGNED NOT NULL,
  `usuarios_idusuarios` int(10) UNSIGNED NOT NULL,
  `rua` varchar(100) DEFAULT NULL,
  `numero` int(10) UNSIGNED DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `cep` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `enderecos`
--

INSERT INTO `enderecos` (`idenderecos`, `usuarios_idusuarios`, `rua`, `numero`, `bairro`, `cep`) VALUES
(2, 5, 'rua um', 387, 'jardim das flores', 13609),
(3, 6, 'rua dois', 1, 'jardim das laranjeiras', 13609),
(4, 7, 'rua dois', 1, 'jardim das laranjeiras', 13609),
(5, 8, 'rua três', 3, 'jardim das primas', 13609),
(6, 9, 'rua quatro', 4, 'jardim das primas', 13609),
(7, 10, 'rua cinco', 5, 'jardim das primas', 13609),
(8, 12, 'rua seis', 6, 'jardim das laranjeiras', 13609),
(9, 13, 'rua sete', 7, 'jardim das primas', 13609),
(10, 14, 'rua oito', 8, 'jardim das primas', 13609);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamentos`
--

CREATE TABLE `pagamentos` (
  `idpagamentos` int(10) UNSIGNED NOT NULL,
  `cartoes_idcartoes` int(10) UNSIGNED NOT NULL,
  `agendamentos_idagendamentos` int(10) UNSIGNED NOT NULL,
  `payment_id` varchar(50) NOT NULL,
  `valor` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `status_ag`
--

CREATE TABLE `status_ag` (
  `idstatus_ag` int(10) UNSIGNED NOT NULL,
  `agendamentos_idagendamentos` int(10) UNSIGNED NOT NULL,
  `status_pg` varchar(10) NOT NULL,
  `executado` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `idusuarios` int(10) UNSIGNED NOT NULL,
  `nome` varchar(45) NOT NULL,
  `cpf` int(11) NOT NULL,
  `telefone` int(11) NOT NULL,
  `email` varchar(45) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` varchar(20) NOT NULL,
  `termos` tinyint(1) NOT NULL,
  `token_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `criacao_token` datetime NOT NULL,
  `expiracao_token` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`idusuarios`, `nome`, `cpf`, `telefone`, `email`, `senha`, `tipo`, `termos`, `token_hash`, `criacao_token`, `expiracao_token`) VALUES
(1, 'Lucas eduardo rosolem', 0, 0, '', '$2y$10$pMDgFCfWgoPwp.9sEjPMFutmvWpw8i41gjUhdVbXVPmshkjWB2aoK', '', 0, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(5, 'teste1', 2147483647, 2147483647, 'teste1@teste1.com.br', '$2y$10$gpI6QrL93DDtKU6wmUsIYeFclcB4wFtK6Rug1lPEwUtic84xCDqSq', 'cliente', 1, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(6, 'teste2', 2147483647, 2147483647, 'teste2@teste2.com.br', '$2y$10$TTT1w/Zn8O.6zbvQpxvjuudrOOahMiP9LrVGuwNA51.qPEEFbW9e6', 'cliente', 1, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(7, 'teste2', 2147483647, 2147483647, 'teste2@teste2.com.br', '$2y$10$ONrv7FnUkBqbNIkF28LU2ed48RXNkuv74i3L2bmmCwNq7dms1GaMm', 'cliente', 1, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(8, 'teste3', 2147483647, 2147483647, 'teste3@teste3.com.br', '$2y$10$P4EEvYxkEQ3xypI24AZGaeyVFFLXkZ/CAYIlrnqRnoau4oqYtfNP2', 'cliente', 1, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(9, 'teste4', 2147483647, 2147483647, 'teste4@teste4.com.br', '$2y$10$w2Kc/fyKFeaJXPZhRokuQuAwpYBGnbxQVG41x8p2hO1nqyDvPYajO', 'cliente', 1, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(10, 'teste5', 2147483647, 2147483647, 'teste5@teste5.com.br', '$2y$10$T7QrcQmqY.GXbo3912E0.eZupzdcxqtuhnfhP0IVsMEDONKyFlTKO', 'cliente', 1, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(11, 'Admin', 2147483647, 2147483647, 'admin@seudominio.com', '$2y$10$SvwSbIDOpK8dnz13JTSlzOxvCL7zNs2bMG/AGIJaqjL/xZUFOz646', 'admin', 1, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(12, 'teste6', 2147483647, 2147483647, 'teste6@teste6.com.br', '$2y$10$y7WaujoaEEd8TJstN7xDtO7ROyFy46otORSQB0.Vb.ml9kr9Or1JC', 'cliente', 1, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(13, 'teste7', 2147483647, 2147483647, 'teste7@teste7.com.br', '$2y$10$HlP/.mw5G18r7KBQNUgZYu/uYNLtJLhVKn6tJj9ynlV5dnh2wjqgi', 'cliente', 1, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(14, 'teste8', 2147483647, 2147483647, 'teste8@teste8.com.br', '$2y$10$.2RF6O1hv0uROtFvwJ2mCeknn/u2bc/pxxfpRDLC9IAh9XzG9PfjS', 'cliente', 1, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `veiculos`
--

CREATE TABLE `veiculos` (
  `idveiculos` int(10) UNSIGNED NOT NULL,
  `usuarios_idusuarios` int(10) UNSIGNED NOT NULL,
  `modelo` varchar(20) NOT NULL,
  `placa` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD PRIMARY KEY (`idagendamentos`),
  ADD KEY `agendamentos_FKIndex1` (`veiculos_idveiculos`),
  ADD KEY `agendamentos_FKIndex2` (`usuarios_idusuarios`);

--
-- Índices de tabela `cartoes`
--
ALTER TABLE `cartoes`
  ADD PRIMARY KEY (`idcartoes`),
  ADD KEY `cartoes_FKIndex1` (`usuarios_idusuarios`);

--
-- Índices de tabela `enderecos`
--
ALTER TABLE `enderecos`
  ADD PRIMARY KEY (`idenderecos`),
  ADD KEY `enderecos_FKIndex1` (`usuarios_idusuarios`);

--
-- Índices de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD PRIMARY KEY (`idpagamentos`),
  ADD KEY `pagamentos_FKIndex1` (`agendamentos_idagendamentos`),
  ADD KEY `pagamentos_FKIndex2` (`cartoes_idcartoes`);

--
-- Índices de tabela `status_ag`
--
ALTER TABLE `status_ag`
  ADD PRIMARY KEY (`idstatus_ag`),
  ADD KEY `status_ag_FKIndex1` (`agendamentos_idagendamentos`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`idusuarios`);

--
-- Índices de tabela `veiculos`
--
ALTER TABLE `veiculos`
  ADD PRIMARY KEY (`idveiculos`),
  ADD KEY `veiculos_FKIndex1` (`usuarios_idusuarios`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  MODIFY `idagendamentos` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `cartoes`
--
ALTER TABLE `cartoes`
  MODIFY `idcartoes` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `enderecos`
--
ALTER TABLE `enderecos`
  MODIFY `idenderecos` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  MODIFY `idpagamentos` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `status_ag`
--
ALTER TABLE `status_ag`
  MODIFY `idstatus_ag` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idusuarios` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `veiculos`
--
ALTER TABLE `veiculos`
  MODIFY `idveiculos` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD CONSTRAINT `agendamentos_ibfk_1` FOREIGN KEY (`veiculos_idveiculos`) REFERENCES `veiculos` (`idveiculos`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `agendamentos_ibfk_2` FOREIGN KEY (`usuarios_idusuarios`) REFERENCES `usuarios` (`idusuarios`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `cartoes`
--
ALTER TABLE `cartoes`
  ADD CONSTRAINT `cartoes_ibfk_1` FOREIGN KEY (`usuarios_idusuarios`) REFERENCES `usuarios` (`idusuarios`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `enderecos`
--
ALTER TABLE `enderecos`
  ADD CONSTRAINT `enderecos_ibfk_1` FOREIGN KEY (`usuarios_idusuarios`) REFERENCES `usuarios` (`idusuarios`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD CONSTRAINT `pagamentos_ibfk_1` FOREIGN KEY (`agendamentos_idagendamentos`) REFERENCES `agendamentos` (`idagendamentos`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `status_ag`
--
ALTER TABLE `status_ag`
  ADD CONSTRAINT `status_ag_ibfk_1` FOREIGN KEY (`agendamentos_idagendamentos`) REFERENCES `agendamentos` (`idagendamentos`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `veiculos`
--
ALTER TABLE `veiculos`
  ADD CONSTRAINT `veiculos_ibfk_1` FOREIGN KEY (`usuarios_idusuarios`) REFERENCES `usuarios` (`idusuarios`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
