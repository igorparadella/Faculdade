-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tempo de Geração: 28/03/2025 às 12h56min
-- Versão do Servidor: 5.5.20
-- Versão do PHP: 5.3.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Banco de Dados: `locacoes`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `acomodacoes`
--

CREATE TABLE IF NOT EXISTS `acomodacoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `endereco` varchar(150) NOT NULL,
  `preco_base` double NOT NULL,
  `descricao_acomodacao` varchar(150) NOT NULL,
  `adicionais` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Extraindo dados da tabela `acomodacoes`
--

INSERT INTO `acomodacoes` (`id`, `endereco`, `preco_base`, `descricao_acomodacao`, `adicionais`) VALUES
(1, 'Av Salgado Filho', 1000, 'Casa Cara da porra', 'um Urso de Segurança');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
