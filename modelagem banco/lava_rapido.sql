-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 30/05/2025 às 19:29
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
CREATE DATABASE IF NOT EXISTS `lava_rapido` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `lava_rapido`;

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
  `leva_e_tras` tinyint(1) NOT NULL,
  `pagamento_na_hora` tinyint(1) NOT NULL,
  `servico` varchar(255) NOT NULL
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
  `numero` varchar(10) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `enderecos`
--

INSERT INTO `enderecos` (`idenderecos`, `usuarios_idusuarios`, `rua`, `numero`, `bairro`, `cep`) VALUES
(11, 16, 'rua oito', '008', 'jardim das primas', '13609-300'),
(12, 17, 'rua dez', '001', 'jardim das laranjeiras', '13609-400'),
(13, 18, 'rua dois', '001', 'jardim das laranjeiras', '13609-400'),
(14, 19, 'rua dois', '001', 'jardim das laranjeiras', '13609-400'),
(15, 20, 'rua dois', '001', 'jardim das laranjeiras', '13609-400'),
(16, 21, 'rua dois', '001', 'jardim das laranjeiras', '13609-400'),
(17, 22, 'rua dois', '001', 'jardim das laranjeiras', '13609-400');

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
  `executado` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `idusuarios` int(10) UNSIGNED NOT NULL,
  `nome` varchar(45) NOT NULL,
  `cpf` varchar(15) NOT NULL,
  `telefone` varchar(15) NOT NULL,
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
(1, 'Lucas eduardo rosolem', '0', '19998235078', '', '$2y$10$pMDgFCfWgoPwp.9sEjPMFutmvWpw8i41gjUhdVbXVPmshkjWB2aoK', '', 0, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(15, 'admin', '2147483647', '11999999999', 'admin@seudominio.com', '$2y$10$VpptMUovchWAR30UMdHCTumI9Y4eThiD/ghuoeb3b.v5VNp.cLgfq', 'admin', 1, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(16, 'test', '38398477623', '19996689410', 'teste6@teste6.com.br', '$2y$10$z169FAWCoi1yZI2xG6SjjuAuvzao249BcwUioqQPXwYpQoCwkW8ve', 'cliente', 1, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(17, 'testando', '55454545454545', '19996689435', 'teste@testandocom.br', '$2y$10$uTNBZ/cJImF8Yp4jyBrmCuXtvjw8ykijB5hZas5FcycA3T4aimYQC', 'cliente', 1, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(18, 'testetop', '38398477634', '19996689435', 'teste11@teste11.com.br', '$2y$10$z8s.XglGJWWtDxSZr8zpu.qprA0mbCouTGBRPLIRtnwVH1.VpbzTe', 'cliente', 1, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(19, 'teste', '38398477616', '19996689435', 'madruguinha.racing@gmail.com', '$2y$10$7B.C6nc0zaoP55ag.S8bLO9Mkfc.7mVujeDlZllTV1jyCK0NAznim', 'cliente', 1, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(20, 'teste', '38398477619', '19996689446', 'lucastradingbit@gmail.com', '$2y$10$ZuShyc5IVxojJ2R0R3R0jOYFNc5aIeELH6PghBY5zVqLhR546yPqC', 'cliente', 1, '$2y$10$L4rJ87etbmT7THN6x13vpermeQsk.dBxw0/LweYtP0a0oNov4KIeC', '2025-05-18 22:12:04', '2025-05-19 00:12:04'),
(21, 'teste', '38398477617', '19996689446', 'Renanmarques894@gmail.com', '$2y$10$j5n6yWXs15uVu.MaNudXUucWcnGMPKm4BflLW5A841e0uCWT4Ke4O', 'cliente', 1, '$2y$10$cRi25MePEjvHpCYFi9i5LOHPWA7/2Nsn4EgQGDLNlbyw1yv.EroPm', '2025-05-18 19:57:31', '2025-05-18 21:57:31'),
(22, 'Lucas eduardo rosolemm', '383957937845', '19996689483', 'madruguinha.racing+teste@gmail.com', '$2y$10$Zd3WcWRFa44WkfNInbCdMuYWZ7LB6UEmScygFAAiQdmQb2TAz3Xga', 'cliente', 1, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `veiculos`
--

CREATE TABLE `veiculos` (
  `idveiculos` int(10) UNSIGNED NOT NULL,
  `usuarios_idusuarios` int(10) UNSIGNED NOT NULL,
  `modelo` varchar(30) NOT NULL,
  `placa` varchar(10) NOT NULL,
  `marca` varchar(30) NOT NULL,
  `tipo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `veiculos`
--

INSERT INTO `veiculos` (`idveiculos`, `usuarios_idusuarios`, `modelo`, `placa`, `marca`, `tipo`) VALUES
(27, 1, 'titan 150', 'FFF-1234', 'honda', 'moto'),
(28, 1, '440', 'KKK-1234', 'volvo', 'caminhao'),
(29, 1, 'gol', 'HHH-1234', 'volkswagen', 'carro'),
(30, 22, 'gol', 'AAA-1234', 'volkswagen', 'carro'),
(31, 22, 'cg 125', 'SSS-1234', 'honda', 'moto'),
(32, 22, '440', 'GGG-1234', 'scania', 'caminhao');

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
  ADD UNIQUE KEY `unique_status` (`agendamentos_idagendamentos`,`executado`),
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
  MODIFY `idagendamentos` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT de tabela `cartoes`
--
ALTER TABLE `cartoes`
  MODIFY `idcartoes` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `enderecos`
--
ALTER TABLE `enderecos`
  MODIFY `idenderecos` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  MODIFY `idpagamentos` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `status_ag`
--
ALTER TABLE `status_ag`
  MODIFY `idstatus_ag` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idusuarios` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de tabela `veiculos`
--
ALTER TABLE `veiculos`
  MODIFY `idveiculos` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

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
