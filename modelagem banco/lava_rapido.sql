-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 01/07/2025 às 06:20
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
DROP PROCEDURE IF EXISTS `update_ag_expirados`$$
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
DROP FUNCTION IF EXISTS `total_agendamentos_hoje`$$
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

DROP TABLE IF EXISTS `agendamentos`;
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
-- Acionadores `agendamentos`
--
DROP TRIGGER IF EXISTS `insert_status_ag_executado`;
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

DROP TABLE IF EXISTS `cartoes`;
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

DROP TABLE IF EXISTS `enderecos`;
CREATE TABLE `enderecos` (
  `idenderecos` int(10) UNSIGNED NOT NULL,
  `usuarios_idusuarios` int(10) UNSIGNED NOT NULL,
  `rua` varchar(100) DEFAULT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamentos`
--

DROP TABLE IF EXISTS `pagamentos`;
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

DROP TABLE IF EXISTS `status_ag`;
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

DROP TABLE IF EXISTS `usuarios`;
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

-- --------------------------------------------------------

--
-- Estrutura para tabela `veiculos`
--

DROP TABLE IF EXISTS `veiculos`;
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
  MODIFY `idagendamentos` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT de tabela `cartoes`
--
ALTER TABLE `cartoes`
  MODIFY `idcartoes` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `enderecos`
--
ALTER TABLE `enderecos`
  MODIFY `idenderecos` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  MODIFY `idpagamentos` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `status_ag`
--
ALTER TABLE `status_ag`
  MODIFY `idstatus_ag` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idusuarios` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT de tabela `veiculos`
--
ALTER TABLE `veiculos`
  MODIFY `idveiculos` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

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
DROP EVENT IF EXISTS `event_update_ag_expired`$$
CREATE DEFINER=`root`@`localhost` EVENT `event_update_ag_expired` ON SCHEDULE EVERY 1 DAY STARTS '2025-06-15 23:59:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    CALL update_ag_expirados();
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
