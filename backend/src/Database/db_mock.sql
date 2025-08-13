-- Mock data completo para rent_a_bike (executar após a criação do schema)
-- Este arquivo inclui clientes, usuários, funcionários, itens E locações ativas para os testes.

USE rent_a_bike;

START TRANSACTION;

-- CLIENTES (5 registros)
INSERT INTO cliente (id, codigo, nome, foto, data_nascimento, cpf, telefone, email, endereco) VALUES
(1, 'CLI001', 'Ana Silva', 'fotos/Ana_Silva.png', '1990-05-12', '12345678901', '11988887777', 'ana@example.com', 'Rua A, 123'),
(2, 'CLI002', 'Bruno Souza', 'fotos/Bruno_Souza.png', '1985-10-30', '98765432100', '21999998888', 'bruno@example.com', 'Av. B, 456'),
(3, 'CLI003', 'Carla Pereira', 'fotos/Carla_Pereira.png', '1992-03-21', '45612378900', '31977775555', 'carla@example.com', 'Rua C, 789'),
(4, 'CLI004', 'Diego Almeida', 'fotos/Diego_Almeida.png', '1988-07-15', '78945612300', '41966664444', 'diego@example.com', 'Av. D, 101'),
(5, 'CLI005', 'Elisa Rodrigues', 'fotos/Elisa_Rodrigues.png', '1995-11-05', '32165498700', '51955553333', 'elisa@example.com', 'Rua E, 202');

-- USUÁRIOS (3 registros, login = CPF)
INSERT INTO usuario (id, login, salt, senha_hash) VALUES
(1, '12345678910', 'e87e686ade6cd7dd8dffabfac166018d', 'c434877837d9c496e9e6fdc54ac6c11d74f1c78ea8b714448e39a0b5584ecf017430926137783d950a6c94fe5ff602107e0a613737d847a532411f39391050d7'),
(2, '12312312312', '9a5196d38498f50dd6a2d48d63d6da69', '3aeb45a0cb2e13a9f38771381de649231a4898402a0d847930c00f1a6fb06daac05dce2deb66aca8026d49c38861a63e05cc952e135f499952a82f0380afd1cd'),
(3, '98765432101', '2fb167a9b4338447d56e7c763a3fc00a', '9de630a5439f72eceb388d5886a53b9ed817f3919bc5f332b8e28e5541dea23590f69a4daa5ddbd77d2efb25b01da9de962a4c33d0765f17fdca49b52e5b94fd');

-- FUNCIONÁRIOS (3 registros)
INSERT INTO funcionario (id, usuario_id, codigo, cpf, nome, telefone, email, cargo) VALUES
(1, 1, 'FUN001', '12345678910', 'Carlos Lima', '21977776666', 'carlos@example.com', 'GERENTE'),
(2, 2, 'FUN002', '12312312312', 'Mariana Costa', '11988885555', 'mariana@example.com', 'ATENDENTE'),
(3, 3, 'FUN003', '98765432101', 'Pedro Santos', '31999990000', 'pedro@example.com', 'MECANICO');

-- ITENS (15 registros)
INSERT INTO item (id, codigo, modelo, fabricante, descricao, valor_hora, numero_seguro, disponivel, tipo) VALUES
(1, 'IT001', 'Caloi Speed', 'Caloi', '', 12.00, 'SEG1001', TRUE, 'BICICLETA'),
(2, 'IT002', 'Capacete Básico', 'ProTech', '', 2.50, NULL, TRUE, 'EQUIPAMENTO'),
(3, 'IT003', 'Monark Urban', 'Monark', '', 10.00, 'SEG1002', TRUE, 'BICICLETA'),
(4, 'IT004', 'Mountain Bike Pro', 'MTB', '', 15.00, 'SEG1003', TRUE, 'BICICLETA'),
(5, 'IT005', 'Triciclo Infantil', 'Infanti', '', 8.00, 'SEG1004', TRUE, 'BICICLETA'),
(6, 'IT006', 'Cadeado Simples', 'SegureFácil', '', 1.50, NULL, TRUE, 'EQUIPAMENTO'),
(7, 'IT007', 'Lanterna Frontal', 'X-Light', '', 3.00, NULL, TRUE, 'EQUIPAMENTO'),
(8, 'IT008', 'Garrafa Térmica', 'HydroPro', '', 2.00, NULL, TRUE, 'EQUIPAMENTO'),
(9, 'IT009', 'Bicicleta Infantil', 'KidsRide', '', 6.00, 'SEG1005', TRUE, 'BICICLETA'),
(10, 'IT010', 'Suporte de Celular', 'CycleMount', '', 1.00, NULL, TRUE, 'EQUIPAMENTO'),
(11, 'IT011', 'Bomba de Ar Portátil', 'AirPumpCo', 'Bomba manual de ar compacta', 4.00, NULL, TRUE, 'EQUIPAMENTO'),
(12, 'IT012', 'Cadeado Revolver', 'LockMaster', 'Cadeado em formato de revólver', 5.00, NULL, TRUE, 'EQUIPAMENTO'),
(13, 'IT013', 'Suporte para Garrafa', 'HydroFix', 'Suporte universal para garrafas', 2.50, NULL, TRUE, 'EQUIPAMENTO'),
(14, 'IT014', 'Kit Reparos', 'FixIt', 'Kit básico de reparos com ferramentas', 6.00, NULL, TRUE, 'EQUIPAMENTO'),
(15, 'IT015', 'Cesta Traseira', 'BikeBasket', 'Cesta grande para transporte', 7.00, NULL, TRUE, 'EQUIPAMENTO');

-- -------------------------------------------------------------------
-- SEÇÃO PARA OS TESTES E2E: INSERÇÃO DE LOCAÇÕES ATIVAS
-- -------------------------------------------------------------------
INSERT INTO locacao (id, cliente_id, funcionario_id, data_hora_locacao, horas_contratadas, data_hora_entrega_prevista, valor_total_previsto, status) VALUES
(1, 1, 1, CONVERT_TZ(NOW(), '+00:00', '-03:00'), 1, DATE_ADD(CONVERT_TZ(NOW(), '+00:00', '-03:00'), INTERVAL 1 HOUR), 12.00, 'EM_ANDAMENTO'),
(2, 2, 1, CONVERT_TZ(NOW(), '+00:00', '-03:00'), 1, DATE_ADD(CONVERT_TZ(NOW(), '+00:00', '-03:00'), INTERVAL 1 HOUR), 2.50,  'EM_ANDAMENTO'),
(3, 3, 1, CONVERT_TZ(NOW(), '+00:00', '-03:00'), 1, DATE_ADD(CONVERT_TZ(NOW(), '+00:00', '-03:00'), INTERVAL 1 HOUR), 10.00, 'EM_ANDAMENTO'),
(4, 4, 1, CONVERT_TZ(NOW(), '+00:00', '-03:00'), 1, DATE_ADD(CONVERT_TZ(NOW(), '+00:00', '-03:00'), INTERVAL 1 HOUR), 15.00, 'EM_ANDAMENTO'),
(5, 5, 1, DATE_SUB(CONVERT_TZ(NOW(), '+00:00', '-03:00'), INTERVAL 80 MINUTE), 1, DATE_SUB(CONVERT_TZ(NOW(), '+00:00', '-03:00'), INTERVAL 20 MINUTE), 16.00, 'EM_ANDAMENTO');
-- Associa os itens, INCLUINDO o `valor_hora` que estava faltando
INSERT INTO locacao_item (locacao_id, item_id, valor_hora) VALUES
(1, 1, 12.00), -- Locação 1 -> Bike Caloi
(2, 2, 2.50),  -- Locação 2 -> Capacete
(3, 3, 10.00), -- Locação 3 -> Bike Monark
(4, 4, 15.00), -- Locação 4 -> Mountain Bike Pro
(5, 5, 16.00); -- Locação 5 -> Triciclo Infantil
-- Altera o status dos itens alugados para indisponível
UPDATE item SET disponivel = FALSE WHERE id IN (1, 2, 3, 4);

COMMIT;