-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tempo de Geração: 24/04/2025 às 13h49min
-- Versão do Servidor: 5.5.20
-- Versão do PHP: 5.3.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Banco de Dados: `pastelaria`
CREATE DATABASE IF NOT EXISTS pastelaria;
USE pastelaria;

--

-- --------------------------------------------------------

--
-- Estrutura da tabela `categoria`
--

CREATE TABLE IF NOT EXISTS `categoria` (
  `ID_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  PRIMARY KEY (`ID_categoria`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Extraindo dados da tabela `categoria`
--

INSERT INTO `categoria` (`ID_categoria`, `nome`) VALUES
(1, 'Pastéis'),
(2, 'Bebidas'),
(3, 'Sobremesas');

-- --------------------------------------------------------

--
-- Estrutura da tabela `cliente`
--

CREATE TABLE IF NOT EXISTS `cliente` (
  `ID_cliente` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `endereco` varchar(200) DEFAULT NULL,
  `data_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID_cliente`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Extraindo dados da tabela `cliente`
--

INSERT INTO `cliente` (`ID_cliente`, `nome`, `email`, `telefone`, `endereco`, `data_registro`) VALUES
(1, 'João da Silva', 'joao@gmail.com', '11999998888', 'Rua das Pastelarias, 123', '2025-04-11 20:16:14'),
(2, 'Maria Oliveira', 'maria@gmail.com', '11999997777', 'Av. dos Sabores, 456', '2025-04-11 20:16:14'),
(6, 'Lucas Pereira', 'lucas@gmail.com', '11988887777', 'Rua das Delícias, 100', '2025-04-01 13:00:00'),
(7, 'Fernanda Rocha', 'fernanda@gmail.com', '11977776666', 'Av. Central, 50', '2025-04-03 17:00:00'),
(8, 'Bruno Costa', 'bruno@gmail.com', '11966665555', 'Travessa do Pastel, 77', '2025-04-07 15:00:00'),
(9, 'Juliana Mendes', 'juliana@gmail.com', '11955554444', 'Rua dos Doces, 89', '2025-04-10 21:30:00'),
(10, 'André Silva', 'andre@gmail.com', '11944443333', 'Alameda das Massas, 30', '2025-04-15 12:15:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `funcionario`
--

CREATE TABLE IF NOT EXISTS `funcionario` (
  `ID_funcionario` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `cargo` varchar(50) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `salario` decimal(10,2) DEFAULT NULL,
  `data_admissao` date DEFAULT NULL,
  PRIMARY KEY (`ID_funcionario`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Extraindo dados da tabela `funcionario`
--

INSERT INTO `funcionario` (`ID_funcionario`, `nome`, `cargo`, `telefone`, `salario`, `data_admissao`) VALUES
(1, 'Carlos Souza', 'Atendente', '11912345678', 1800.00, '2023-01-15'),
(2, 'Ana Lima', 'Cozinheira', '11987654321', 2200.00, '2022-12-01');

-- --------------------------------------------------------

--
-- Estrutura da tabela `funcionario_pedido`
--

CREATE TABLE IF NOT EXISTS `funcionario_pedido` (
  `ID_funcionario` int(11) NOT NULL DEFAULT '0',
  `ID_pedido` int(11) NOT NULL DEFAULT '0',
  `funcao_no_pedido` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_funcionario`,`ID_pedido`),
  KEY `ID_pedido` (`ID_pedido`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `funcionario_pedido`
--

INSERT INTO `funcionario_pedido` (`ID_funcionario`, `ID_pedido`, `funcao_no_pedido`) VALUES
(1, 1, 'Atendimento'),
(1, 2, 'Atendimento'),
(2, 1, 'Cozinha');

-- --------------------------------------------------------

--
-- Estrutura da tabela `item_pedido`
--

CREATE TABLE IF NOT EXISTS `item_pedido` (
  `ID_item_pedido` int(11) NOT NULL AUTO_INCREMENT,
  `ID_pedido` int(11) DEFAULT NULL,
  `ID_produto` int(11) DEFAULT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`ID_item_pedido`),
  KEY `ID_pedido` (`ID_pedido`),
  KEY `ID_produto` (`ID_produto`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Extraindo dados da tabela `item_pedido`
--

INSERT INTO `item_pedido` (`ID_item_pedido`, `ID_pedido`, `ID_produto`, `quantidade`, `preco_unitario`) VALUES
(1, 1, 1, 2, 8.50),
(2, 1, 3, 1, 4.00),
(3, 2, 2, 2, 7.00),
(4, 2, 4, 1, 1.50);

-- --------------------------------------------------------

--
-- Estrutura da tabela `pagamento`
--

CREATE TABLE IF NOT EXISTS `pagamento` (
  `ID_pagamento` int(11) NOT NULL AUTO_INCREMENT,
  `ID_pedido` int(11) DEFAULT NULL,
  `data_pagamento` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `valor_pago` decimal(10,2) DEFAULT NULL,
  `metodo_pagamento` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_pagamento`),
  UNIQUE KEY `ID_pedido` (`ID_pedido`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Extraindo dados da tabela `pagamento`
--

INSERT INTO `pagamento` (`ID_pagamento`, `ID_pedido`, `data_pagamento`, `valor_pago`, `metodo_pagamento`) VALUES
(1, 1, '2025-04-11 20:16:14', 21.00, 'PIX'),
(2, 2, '2025-04-11 20:16:14', 15.50, 'Cartão de Crédito');

-- --------------------------------------------------------

--
-- Estrutura da tabela `pedido`
--

CREATE TABLE IF NOT EXISTS `pedido` (
  `ID_pedido` int(11) NOT NULL AUTO_INCREMENT,
  `ID_cliente` int(11) DEFAULT NULL,
  `data_pedido` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(50) DEFAULT 'em preparo',
  `valor_total` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`ID_pedido`),
  KEY `ID_cliente` (`ID_cliente`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Extraindo dados da tabela `pedido`
--

INSERT INTO `pedido` (`ID_pedido`, `ID_cliente`, `data_pedido`, `status`, `valor_total`) VALUES
(1, 1, '2025-04-11 20:16:14', 'Pago', 21.00),
(2, 2, '2025-04-11 20:16:14', 'em preparo', 15.50);

-- --------------------------------------------------------

--
-- Estrutura da tabela `produto`
--

CREATE TABLE IF NOT EXISTS `produto` (
  `ID_produto` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text,
  `preco` decimal(10,2) NOT NULL,
  `categoria` int(11) DEFAULT NULL,
  `estoque` int(11) DEFAULT '0',
  `imagem` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID_produto`),
  KEY `categoria` (`categoria`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Extraindo dados da tabela `produto`
--

INSERT INTO `produto` (`ID_produto`, `nome`, `descricao`, `preco`, `categoria`, `estoque`, `imagem`) VALUES
(1, 'Pastel de Carne', 'Pastel recheado com carne moída e temperos', 8.50, 1, 50, 'carne.jpg'),
(2, 'Pastel de Queijo', 'Queijo derretido no recheio', 7.00, 1, 40, 'queijo.jpg'),
(3, 'Coca-Cola 350ml', 'Refrigerante gelado', 5.00, 2, 30, 'coca.jpg'),
(4, 'Brigadeiro', 'Doce brasileiro tradicional', 3.00, 3, 100, 'brigadeiro.jpg');

--
-- Restrições para as tabelas dumpadas
--

--
-- Restrições para a tabela `funcionario_pedido`
--
ALTER TABLE `funcionario_pedido`
  ADD CONSTRAINT `funcionario_pedido_ibfk_1` FOREIGN KEY (`ID_funcionario`) REFERENCES `funcionario` (`ID_funcionario`),
  ADD CONSTRAINT `funcionario_pedido_ibfk_2` FOREIGN KEY (`ID_pedido`) REFERENCES `pedido` (`ID_pedido`);

--
-- Restrições para a tabela `item_pedido`
--
ALTER TABLE `item_pedido`
  ADD CONSTRAINT `item_pedido_ibfk_1` FOREIGN KEY (`ID_pedido`) REFERENCES `pedido` (`ID_pedido`),
  ADD CONSTRAINT `item_pedido_ibfk_2` FOREIGN KEY (`ID_produto`) REFERENCES `produto` (`ID_produto`);

--
-- Restrições para a tabela `pagamento`
--
ALTER TABLE `pagamento`
  ADD CONSTRAINT `pagamento_ibfk_1` FOREIGN KEY (`ID_pedido`) REFERENCES `pedido` (`ID_pedido`);

--
-- Restrições para a tabela `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `pedido_ibfk_1` FOREIGN KEY (`ID_cliente`) REFERENCES `cliente` (`ID_cliente`);

--
-- Restrições para a tabela `produto`
--
ALTER TABLE `produto`
  ADD CONSTRAINT `produto_ibfk_1` FOREIGN KEY (`categoria`) REFERENCES `categoria` (`ID_categoria`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
