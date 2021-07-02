-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 08-Jul-2019 às 18:29
-- Versão do servidor: 10.1.38-MariaDB
-- versão do PHP: 7.3.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `blog`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `categoria` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `categorias`
--

INSERT INTO `categorias` (`id`, `categoria`) VALUES
(6, 'php'),
(7, 'html');

-- --------------------------------------------------------

--
-- Estrutura da tabela `comentarios`
--

CREATE TABLE `comentarios` (
  `id` int(11) NOT NULL,
  `id_post` varchar(200) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `comentario` text NOT NULL,
  `data` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `comentarios`
--

INSERT INTO `comentarios` (`id`, `id_post`, `nome`, `comentario`, `data`) VALUES
(5, '16', 'Israel Silva', 'show!', '08-07-2019 12:51:38'),
(6, '15', 'Charlles Silva', 'Legal!', '08-07-2019 12:52:05');

-- --------------------------------------------------------

--
-- Estrutura da tabela `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `subtitulo` varchar(200) NOT NULL,
  `postagem` text NOT NULL,
  `imagem` varchar(200) NOT NULL,
  `data` varchar(200) NOT NULL,
  `categoria` varchar(200) NOT NULL,
  `id_postador` varchar(200) NOT NULL,
  `visualizacoes` int(200) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `posts`
--

INSERT INTO `posts` (`id`, `titulo`, `subtitulo`, `postagem`, `imagem`, `data`, `categoria`, `id_postador`, `visualizacoes`) VALUES
(16, 'Lorem ipsum', 'lorem-ipsum', '<p class=\"paragrafo\">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod&nbsp;<span style=\"font-size: 1rem;\">tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,&nbsp;</span><span style=\"font-size: 1rem;\">quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo&nbsp;</span><span style=\"font-size: 1rem;\">consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse&nbsp;</span><span style=\"font-size: 1rem;\">cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non&nbsp;</span><span style=\"font-size: 1rem;\">proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</span></p><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod&nbsp;<span style=\"font-size: 1rem;\">tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,&nbsp;</span><span style=\"font-size: 1rem;\">quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo&nbsp;</span><span style=\"font-size: 1rem;\">consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse&nbsp;</span><span style=\"font-size: 1rem;\">cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non&nbsp;</span><span style=\"font-size: 1rem;\">proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</span><span style=\"font-size: 1rem;\"><br></span></p>', 'images/uploads/logo-blog.fw.png', '08-07-2019 12:18:23', '7', 'Israel061', 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `usuario` varchar(200) NOT NULL,
  `senha` varchar(500) NOT NULL,
  `superadmin` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `usuario`, `senha`, `superadmin`) VALUES
(1, 'Israel', 'Israel061', '$2y$10$IH1sOwf2aayxls0d3vl/zun391zEI88bkUJIM7cUlkyQnB0C0MzJe', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
