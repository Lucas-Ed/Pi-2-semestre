-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 16/06/2025 às 04:21
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

DELIMITER $$
--
-- Procedimentos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_ag_expirados` ()   BEGIN
    UPDATE status_ag s
    INNER JOIN agendamentos a ON s.agendamentos_idagendamentos = a.idagendamentos
    SET s.executado = 'Expirado'
    WHERE a.data_agendamento < CURDATE()
      AND s.executado NOT IN ('Concluída', 'Expirado');
END$$

--
-- Funções
--
CREATE DEFINER=`root`@`localhost` FUNCTION `total_agendamentos_hoje` () RETURNS INT(11) DETERMINISTIC BEGIN
    DECLARE total INT;

    SELECT COUNT(*) INTO total
    FROM agendamentos
    WHERE data_agendamento = CURDATE();

    RETURN total;
END$$

DELIMITER ;

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
  `servico` varchar(255) NOT NULL,
  `preco` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `agendamentos`
--

INSERT INTO `agendamentos` (`idagendamentos`, `usuarios_idusuarios`, `veiculos_idveiculos`, `data_agendamento`, `hora_agendamento`, `leva_e_tras`, `pagamento_na_hora`, `servico`, `preco`) VALUES
(110, 30, 46, '2025-06-16', '11:00:00', 0, 0, 'enceramento', 500.00),
(111, 30, 44, '2025-06-16', '08:20:00', 0, 0, 'lavagem_externa', 70.00),
(112, 30, 45, '2025-06-16', '11:40:00', 0, 0, 'lavagem_externa', 40.00),
(113, 30, 47, '2025-06-16', '07:00:00', 0, 0, 'lavagem_externa', 40.00);

--
-- Acionadores `agendamentos`
--
DELIMITER $$
CREATE TRIGGER `insert_status_ag_executado` AFTER INSERT ON `agendamentos` FOR EACH ROW BEGIN
    INSERT INTO status_ag (
        agendamentos_idagendamentos,
        executado
    ) VALUES (
        NEW.idagendamentos,
        'Pendente'
    );
END
$$
DELIMITER ;

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
(23, 30, 'Rua Celso Luz', '1', 'Jardim Bela Vista', '13609400'),
(24, 31, 'Rua Celso Luz', '1', 'Jardim Bela Vista', '13609400');

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

--
-- Despejando dados para a tabela `status_ag`
--

INSERT INTO `status_ag` (`idstatus_ag`, `agendamentos_idagendamentos`, `status_pg`, `executado`) VALUES
(83, 110, '', 'Pendente'),
(84, 111, '', 'Pendente'),
(85, 112, '', 'Pendente'),
(86, 113, '', 'Pendente');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `idusuarios` int(10) UNSIGNED NOT NULL,
  `nome` varchar(45) NOT NULL,
  `cpf` varchar(255) DEFAULT NULL,
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
(26, 'admin', 'uwFHBjM78OflwVikhQZ0mw==', '11999999999', 'admin@seudominio.com', '$2y$10$Bm.o3fvsb1ildhYuupdd0.p12DAfJttc3dUqP15NOcE7HSL9LTgRS', 'admin', 1, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(30, 'Lucas eduardo rosolem', 't5gdxJwiEYw+xfDfSnojzQ==', '19996689483', 'madruguinha.racing+teste@gmail.com', '$2y$10$5mlXvx0S93XD6xZllx79zumqO17GtsHa2Jl4tWtPHuEUQfv6JFvwm', 'cliente', 1, '$2y$10$fx/hRizfTRAPg2rKHikrdewkJgmFC6wskiszsGYMzLmCIczqYLtZO', '2025-06-15 22:36:49', '2025-06-16 00:36:49'),
(31, 'eduardo', 'FTaQ6EnNExSDff3u26oskg==', '19996689480', 'madruguinha.racing+teste1@gmail.com', '$2y$10$7u1GMK0g7tHRKG5o4.D5ueZLoBCeul/yoOVwzmXQ17K3uUYZy3Vv2', 'cliente', 1, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

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
  `tipo` varchar(50) NOT NULL,
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `veiculos`
--

INSERT INTO `veiculos` (`idveiculos`, `usuarios_idusuarios`, `modelo`, `placa`, `marca`, `tipo`, `ativo`) VALUES
(44, 30, 'a3', 'JJJ1D43', 'audi', 'carro', 1),
(45, 30, 'titan', 'GGG-3456', 'honda', 'moto', 1),
(46, 30, '440', 'DDD1234', 'scania', 'caminhao', 1),
(47, 30, '150', 'HHH1G45', 'honda', 'moto', 1),
(48, 30, 'voyage', 'HHH3H87', 'volkswagen', 'carro', 0);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD PRIMARY KEY (`idagendamentos`),
  ADD UNIQUE KEY `unique_data_hora` (`data_agendamento`,`hora_agendamento`),
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
  MODIFY `idagendamentos` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT de tabela `cartoes`
--
ALTER TABLE `cartoes`
  MODIFY `idcartoes` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `enderecos`
--
ALTER TABLE `enderecos`
  MODIFY `idenderecos` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  MODIFY `idpagamentos` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `status_ag`
--
ALTER TABLE `status_ag`
  MODIFY `idstatus_ag` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idusuarios` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de tabela `veiculos`
--
ALTER TABLE `veiculos`
  MODIFY `idveiculos` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

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

DELIMITER $$
--
-- Eventos
--
CREATE DEFINER=`root`@`localhost` EVENT `event_update_ag_expired` ON SCHEDULE EVERY 1 DAY STARTS '2025-06-15 23:59:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    CALL update_ag_expirados();
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
