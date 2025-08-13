-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: mariadb
-- Tempo de geração: 01/07/2025 às 18:50
-- Versão do servidor: 11.8.2-MariaDB-ubu2404
-- Versão do PHP: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


DROP DATABASE IF EXISTS rent_a_bike;

-- Criar banco de dados rent_a_bike
CREATE DATABASE IF NOT EXISTS rent_a_bike
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE rent_a_bike;

--
-- Banco de dados: `rent_a_bike`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `avaria`
--

CREATE TABLE `avaria` (
  `id` int(11) NOT NULL,
  `devolucao_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `funcionario_id` int(11) NOT NULL,
  `data_hora` datetime NOT NULL,
  `descricao` text NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `foto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `avaria`
--

INSERT INTO `avaria` (`id`, `devolucao_id`, `item_id`, `funcionario_id`, `data_hora`, `descricao`, `valor`, `foto`) VALUES
(1, 3, 1, 1, '2025-07-01 15:31:06', 'Aro amassado', 100.00, 'fotos/avarias/dev-3-item-1-7561281e.jpg'),
(2, 4, 1, 1, '2025-07-01 15:32:37', 'Aro amassdo', 111.00, 'fotos/avarias/dev-4-item-1-22181abb.jpg'),
(3, 4, 5, 1, '2025-07-01 15:32:38', 'aro amassado', 200.00, 'fotos/avarias/dev-4-item-5-acb0ef01.jpg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente`
--

CREATE TABLE `cliente` (
  `id` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `data_nascimento` date NOT NULL,
  `cpf` varchar(11) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Despejando dados para a tabela `cliente`
--

INSERT INTO `cliente` (`id`, `codigo`, `nome`, `foto`, `data_nascimento`, `cpf`, `telefone`, `email`, `endereco`, `created_at`, `updated_at`) VALUES
(1, 'CLI001', 'Ana Silva', 'fotos/Ana_Silva.png', '1990-05-12', '12345678901', '11988887777', 'ana@example.com', 'Rua A, 123', '2025-07-01 18:29:25', '2025-07-01 18:29:25'),
(2, 'CLI002', 'Bruno Souza', 'fotos/Bruno_Souza.png', '1985-10-30', '98765432100', '21999998888', 'bruno@example.com', 'Av. B, 456', '2025-07-01 18:29:25', '2025-07-01 18:29:25'),
(3, 'CLI003', 'Carla Pereira', 'fotos/Carla_Pereira.png', '1992-03-21', '45612378900', '31977775555', 'carla@example.com', 'Rua C, 789', '2025-07-01 18:29:25', '2025-07-01 18:29:25'),
(4, 'CLI004', 'Diego Almeida', 'fotos/Diego_Almeida.png', '1988-07-15', '78945612300', '41966664444', 'diego@example.com', 'Av. D, 101', '2025-07-01 18:29:25', '2025-07-01 18:29:25'),
(5, 'CLI005', 'Elisa Rodrigues', 'fotos/Elisa_Rodrigues.png', '1995-11-05', '32165498700', '51955553333', 'elisa@example.com', 'Rua E, 202', '2025-07-01 18:29:25', '2025-07-01 18:29:25');

-- --------------------------------------------------------

--
-- Estrutura para tabela `devolucao`
--

CREATE TABLE `devolucao` (
  `id` int(11) NOT NULL,
  `locacao_id` int(11) NOT NULL,
  `funcionario_id` int(11) NOT NULL,
  `data_hora` datetime NOT NULL DEFAULT current_timestamp(),
  `horas_usadas` int(11) NOT NULL CHECK (`horas_usadas` >= 0),
  `desconto_aplicado` decimal(10,2) NOT NULL DEFAULT 0.00,
  `valor_pago` decimal(10,2) NOT NULL CHECK (`valor_pago` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Despejando dados para a tabela `devolucao`
--

INSERT INTO `devolucao` (`id`, `locacao_id`, `funcionario_id`, `data_hora`, `horas_usadas`, `desconto_aplicado`, `valor_pago`) VALUES
(1, 5, 1, '2025-07-01 15:29:54', 10, 8.00, 72.00),
(2, 1, 1, '2025-07-01 15:30:06', 1, 0.00, 12.00),
(3, 6, 1, '2025-07-01 15:31:06', 1, 0.00, 113.20),
(4, 7, 1, '2025-07-01 15:32:37', 20, 57.00, 864.00),
(5, 8, 1, '2025-07-01 15:33:27', 720, 1656.00, 14904.00),
(6, 9, 2, '2025-07-01 15:43:48', 20, 40.00, 360.00),
(7, 10, 1, '2025-07-01 15:50:06', 150, 225.00, 2250.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `funcionario`
--

CREATE TABLE `funcionario` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `cpf` varchar(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `cargo` enum('GERENTE','ATENDENTE','MECANICO') NOT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `editado_em` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Despejando dados para a tabela `funcionario`
--

INSERT INTO `funcionario` (`id`, `usuario_id`, `codigo`, `cpf`, `nome`, `telefone`, `email`, `cargo`, `criado_em`, `editado_em`) VALUES
(1, 1, 'FUN001', '12345678910', 'Carlos Lima', '21977776666', 'carlos@example.com', 'GERENTE', '2025-07-01 18:29:25', '2025-07-01 18:29:25'),
(2, 2, 'FUN002', '12312312312', 'Mariana Costa', '11988885555', 'mariana@example.com', 'ATENDENTE', '2025-07-01 18:29:25', '2025-07-01 18:29:25'),
(3, 3, 'FUN003', '98765432101', 'Pedro Santos', '31999990000', 'pedro@example.com', 'MECANICO', '2025-07-01 18:29:25', '2025-07-01 18:29:25');

-- --------------------------------------------------------

--
-- Estrutura para tabela `item`
--

CREATE TABLE `item` (
  `id` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `fabricante` varchar(100) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `valor_hora` decimal(10,2) NOT NULL CHECK (`valor_hora` >= 0),
  `numero_seguro` varchar(50) DEFAULT NULL,
  `disponivel` tinyint(1) NOT NULL DEFAULT 1,
  `tipo` enum('BICICLETA','EQUIPAMENTO') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Despejando dados para a tabela `item`
--

INSERT INTO `item` (`id`, `codigo`, `modelo`, `fabricante`, `descricao`, `valor_hora`, `numero_seguro`, `disponivel`, `tipo`, `created_at`, `updated_at`) VALUES
(1, 'IT001', 'Caloi Speed', 'Caloi', '', 12.00, 'SEG1001', 1, 'BICICLETA', '2025-07-01 18:29:25', '2025-07-01 18:43:48'),
(2, 'IT002', 'Capacete Básico', 'ProTech', '', 2.50, NULL, 0, 'EQUIPAMENTO', '2025-07-01 18:29:25', '2025-07-01 18:29:25'),
(3, 'IT003', 'Monark Urban', 'Monark', '', 10.00, 'SEG1002', 0, 'BICICLETA', '2025-07-01 18:29:25', '2025-07-01 18:29:25'),
(4, 'IT004', 'Mountain Bike Pro', 'MTB', '', 15.00, 'SEG1003', 0, 'BICICLETA', '2025-07-01 18:29:25', '2025-07-01 18:29:25'),
(5, 'IT005', 'Triciclo Infantil', 'Infanti', '', 8.00, 'SEG1004', 1, 'BICICLETA', '2025-07-01 18:29:25', '2025-07-01 18:50:06'),
(6, 'IT006', 'Cadeado Simples', 'SegureFácil', '', 1.50, NULL, 1, 'EQUIPAMENTO', '2025-07-01 18:29:25', '2025-07-01 18:29:25'),
(7, 'IT007', 'Lanterna Frontal', 'X-Light', '', 3.00, NULL, 1, 'EQUIPAMENTO', '2025-07-01 18:29:25', '2025-07-01 18:29:25'),
(8, 'IT008', 'Garrafa Térmica', 'HydroPro', '', 2.00, NULL, 1, 'EQUIPAMENTO', '2025-07-01 18:29:25', '2025-07-01 18:29:25'),
(9, 'IT009', 'Bicicleta Infantil', 'KidsRide', '', 6.00, 'SEG1005', 1, 'BICICLETA', '2025-07-01 18:29:25', '2025-07-01 18:29:25'),
(10, 'IT010', 'Suporte de Celular', 'CycleMount', '', 1.00, NULL, 1, 'EQUIPAMENTO', '2025-07-01 18:29:25', '2025-07-01 18:32:37'),
(11, 'IT011', 'Bomba de Ar Portátil', 'AirPumpCo', 'Bomba manual de ar compacta', 4.00, NULL, 1, 'EQUIPAMENTO', '2025-07-01 18:29:25', '2025-07-01 18:33:27'),
(12, 'IT012', 'Cadeado Revolver', 'LockMaster', 'Cadeado em formato de revólver', 5.00, NULL, 1, 'EQUIPAMENTO', '2025-07-01 18:29:25', '2025-07-01 18:32:37'),
(13, 'IT013', 'Suporte para Garrafa', 'HydroFix', 'Suporte universal para garrafas', 2.50, NULL, 1, 'EQUIPAMENTO', '2025-07-01 18:29:25', '2025-07-01 18:32:37'),
(14, 'IT014', 'Kit Reparos', 'FixIt', 'Kit básico de reparos com ferramentas', 6.00, NULL, 1, 'EQUIPAMENTO', '2025-07-01 18:29:25', '2025-07-01 18:29:25'),
(15, 'IT015', 'Cesta Traseira', 'BikeBasket', 'Cesta grande para transporte', 7.00, NULL, 1, 'EQUIPAMENTO', '2025-07-01 18:29:25', '2025-07-01 18:50:06');

-- --------------------------------------------------------

--
-- Estrutura para tabela `locacao`
--

CREATE TABLE `locacao` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `funcionario_id` int(11) NOT NULL,
  `data_hora_locacao` datetime NOT NULL DEFAULT current_timestamp(),
  `horas_contratadas` int(11) NOT NULL CHECK (`horas_contratadas` > 0),
  `data_hora_entrega_prevista` datetime NOT NULL,
  `desconto_aplicado` decimal(10,2) NOT NULL DEFAULT 0.00,
  `valor_total_previsto` decimal(10,2) NOT NULL CHECK (`valor_total_previsto` >= 0),
  `status` enum('EM_ANDAMENTO','FINALIZADA','CANCELADA') NOT NULL DEFAULT 'EM_ANDAMENTO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Despejando dados para a tabela `locacao`
--

INSERT INTO `locacao` (`id`, `cliente_id`, `funcionario_id`, `data_hora_locacao`, `horas_contratadas`, `data_hora_entrega_prevista`, `desconto_aplicado`, `valor_total_previsto`, `status`) VALUES
(1, 1, 1, '2025-07-01 15:29:25', 1, '2025-07-01 16:29:25', 0.00, 12.00, 'FINALIZADA'),
(2, 2, 1, '2025-06-01 15:29:25', 1, '2025-07-01 16:29:25', 0.00, 2.50, 'EM_ANDAMENTO'),
(3, 3, 1, '2025-07-01 15:29:25', 1, '2025-07-01 16:29:25', 0.00, 10.00, 'EM_ANDAMENTO'),
(4, 4, 1, '2025-07-01 15:29:25', 1, '2025-07-01 16:29:25', 0.00, 15.00, 'EM_ANDAMENTO'),
(5, 1, 1, '2025-06-13 15:29:46', 10, '2025-07-02 01:29:46', 8.00, 72.00, 'FINALIZADA'),
(6, 1, 1, '2025-06-10 15:30:25', 1, '2025-07-01 16:30:25', 0.00, 12.00, 'FINALIZADA'),
(7, 5, 1, '2025-06-28 15:31:51', 20, '2025-07-02 11:31:51', 57.00, 513.00, 'FINALIZADA'),
(8, 4, 1, '2025-06-30 15:33:19', 720, '2025-07-31 15:33:19', 1656.00, 14904.00, 'FINALIZADA'),
(9, 1, 2, '2025-07-01 15:43:40', 20, '2025-07-02 11:43:40', 40.00, 360.00, 'FINALIZADA'),
(10, 5, 1, '2025-06-15 15:49:51', 150, '2025-07-07 21:49:51', 225.00, 2025.00, 'FINALIZADA');

-- --------------------------------------------------------

--
-- Estrutura para tabela `locacao_item`
--

CREATE TABLE `locacao_item` (
  `locacao_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `valor_hora` decimal(10,2) NOT NULL CHECK (`valor_hora` >= 0),
  `limpeza_aplicada` tinyint(1) NOT NULL DEFAULT 0,
  `valor_taxa_limpeza` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Despejando dados para a tabela `locacao_item`
--

INSERT INTO `locacao_item` (`locacao_id`, `item_id`, `valor_hora`, `limpeza_aplicada`, `valor_taxa_limpeza`) VALUES
(1, 1, 12.00, 0, 0.00),
(2, 2, 2.50, 0, 0.00),
(3, 3, 10.00, 0, 0.00),
(4, 4, 15.00, 0, 0.00),
(5, 5, 8.00, 0, 0.00),
(6, 1, 12.00, 1, 1.20),
(7, 1, 12.00, 1, 24.00),
(7, 5, 8.00, 1, 16.00),
(7, 10, 1.00, 0, 0.00),
(7, 12, 5.00, 0, 0.00),
(7, 13, 2.50, 0, 0.00),
(8, 1, 12.00, 0, 0.00),
(8, 11, 4.00, 0, 0.00),
(8, 15, 7.00, 0, 0.00),
(9, 1, 12.00, 0, 0.00),
(9, 5, 8.00, 0, 0.00),
(10, 5, 8.00, 1, 120.00),
(10, 15, 7.00, 1, 105.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `login` varchar(255) NOT NULL,
  `senha_hash` char(128) NOT NULL,
  `salt` char(32) NOT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `editado_em` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id`, `login`, `senha_hash`, `salt`, `criado_em`, `editado_em`) VALUES
(1, '12345678910', 'c434877837d9c496e9e6fdc54ac6c11d74f1c78ea8b714448e39a0b5584ecf017430926137783d950a6c94fe5ff602107e0a613737d847a532411f39391050d7', 'e87e686ade6cd7dd8dffabfac166018d', '2025-07-01 18:29:25', '2025-07-01 18:29:25'),
(2, '12312312312', '3aeb45a0cb2e13a9f38771381de649231a4898402a0d847930c00f1a6fb06daac05dce2deb66aca8026d49c38861a63e05cc952e135f499952a82f0380afd1cd', '9a5196d38498f50dd6a2d48d63d6da69', '2025-07-01 18:29:25', '2025-07-01 18:29:25'),
(3, '98765432101', '9de630a5439f72eceb388d5886a53b9ed817f3919bc5f332b8e28e5541dea23590f69a4daa5ddbd77d2efb25b01da9de962a4c33d0765f17fdca49b52e5b94fd', '2fb167a9b4338447d56e7c763a3fc00a', '2025-07-01 18:29:25', '2025-07-01 18:29:25');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `avaria`
--
ALTER TABLE `avaria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_avaria_devolucao` (`devolucao_id`),
  ADD KEY `idx_avaria_item` (`item_id`),
  ADD KEY `idx_avaria_funcionario` (`funcionario_id`);

--
-- Índices de tabela `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- Índices de tabela `devolucao`
--
ALTER TABLE `devolucao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_devolucao_locacao` (`locacao_id`),
  ADD KEY `funcionario_id` (`funcionario_id`);

--
-- Índices de tabela `funcionario`
--
ALTER TABLE `funcionario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- Índices de tabela `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Índices de tabela `locacao`
--
ALTER TABLE `locacao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_locacao_cliente` (`cliente_id`),
  ADD KEY `idx_locacao_funcionario` (`funcionario_id`);

--
-- Índices de tabela `locacao_item`
--
ALTER TABLE `locacao_item`
  ADD PRIMARY KEY (`locacao_id`,`item_id`),
  ADD KEY `idx_locacao_item_item` (`item_id`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `avaria`
--
ALTER TABLE `avaria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `devolucao`
--
ALTER TABLE `devolucao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `funcionario`
--
ALTER TABLE `funcionario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `item`
--
ALTER TABLE `item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `locacao`
--
ALTER TABLE `locacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `avaria`
--
ALTER TABLE `avaria`
  ADD CONSTRAINT `fk_avaria_devolucao` FOREIGN KEY (`devolucao_id`) REFERENCES `devolucao` (`id`),
  ADD CONSTRAINT `fk_avaria_funcionario` FOREIGN KEY (`funcionario_id`) REFERENCES `funcionario` (`id`),
  ADD CONSTRAINT `fk_avaria_item` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`);

--
-- Restrições para tabelas `devolucao`
--
ALTER TABLE `devolucao`
  ADD CONSTRAINT `devolucao_ibfk_1` FOREIGN KEY (`locacao_id`) REFERENCES `locacao` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `devolucao_ibfk_2` FOREIGN KEY (`funcionario_id`) REFERENCES `funcionario` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `funcionario`
--
ALTER TABLE `funcionario`
  ADD CONSTRAINT `fk_funcionario_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `locacao`
--
ALTER TABLE `locacao`
  ADD CONSTRAINT `locacao_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `locacao_ibfk_2` FOREIGN KEY (`funcionario_id`) REFERENCES `funcionario` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `locacao_item`
--
ALTER TABLE `locacao_item`
  ADD CONSTRAINT `locacao_item_ibfk_1` FOREIGN KEY (`locacao_id`) REFERENCES `locacao` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `locacao_item_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
