-- INSERT_tests.sql
-- Inserções de teste no banco de dados da pastelaria

-- 1. Inserir novas categorias
INSERT INTO categoria (nome) VALUES 
('Lanches'),
('Porções'),
('Vegetarianos');

-- 2. Inserir novos clientes
INSERT INTO cliente (nome, email, telefone, endereco) VALUES 
('Carlos Oliveira', 'carlos@email.com', '11988889999', 'Rua das Flores, 300'),
('Ana Paula Santos', 'ana@email.com', '11977778888', 'Av. Brasil, 1500'),
('Marcos Vinicius', 'marcos@email.com', '11966667777', 'Travessa dos Pastéis, 25');

-- 3. Inserir novos funcionários
INSERT INTO funcionario (nome, cargo, telefone, salario, data_admissao) VALUES 
('Patricia Gomes', 'Atendente', '11955556666', 1900.00, '2025-02-15'),
('Ricardo Almeida', 'Cozinheiro', '11944445555', 2400.00, '2024-11-10'),
('Juliana Castro', 'Entregadora', '11933334444', 1600.00, '2025-03-01');

-- 4. Inserir novos produtos
INSERT INTO produto (nome, descricao, preco, categoria, estoque, imagem) VALUES 
('Pastel de Pizza', 'Recheado com mussarela, tomate e oregano', 9.50, 1, 45, 'pizza.jpg'),
('Suco Natural', 'Suco de laranja feito na hora', 6.00, 2, 60, 'suco.jpg'),
('Pastel de Palmito', 'Opção vegetariana com palmito', 8.00, 5, 30, 'palmito.jpg'),
('Batata Frita', 'Porção de batata frita crocante', 12.00, 4, 25, 'batata.jpg');

-- 5. Inserir novos pedidos
INSERT INTO pedido (ID_cliente, status, valor_total) VALUES 
(6, 'em preparo', 27.50),
(7, 'entregue', 34.00),
(11, 'pago', 18.00);

-- 6. Inserir itens nos pedidos
INSERT INTO item_pedido (ID_pedido, ID_produto, quantidade, preco_unitario) VALUES 
(3, 5, 2, 9.50),
(3, 7, 1, 6.00),
(4, 6, 3, 8.00),
(4, 8, 1, 12.00),
(5, 2, 2, 7.00),
(5, 3, 1, 5.00);

-- 7. Associar funcionários aos pedidos
INSERT INTO funcionario_pedido (ID_funcionario, ID_pedido, funcao_no_pedido) VALUES 
(1, 3, 'Atendimento'),
(3, 3, 'Entrega'),
(4, 4, 'Atendimento'),
(2, 4, 'Cozinha'),
(1, 5, 'Atendimento');

-- 8. Inserir pagamentos
INSERT INTO pagamento (ID_pedido, valor_pago, metodo_pagamento) VALUES 
(3, 27.50, 'Cartão de Débito'),
(4, 34.00, 'PIX'),
(5, 18.00, 'Dinheiro');

-- 9. Inserir um pedido completo com transação
START TRANSACTION;

-- Inserir o pedido
INSERT INTO pedido (ID_cliente, status, valor_total) VALUES 
(8, 'em preparo', 15.00);

-- Obter o ID do último pedido inserido
SET @last_pedido_id = LAST_INSERT_ID();

-- Inserir itens do pedido
INSERT INTO item_pedido (ID_pedido, ID_produto, quantidade, preco_unitario) VALUES 
(@last_pedido_id, 1, 1, 8.50),
(@last_pedido_id, 3, 1, 5.00);

-- Associar funcionário
INSERT INTO funcionario_pedido (ID_funcionario, ID_pedido, funcao_no_pedido) VALUES 
(1, @last_pedido_id, 'Atendimento');

-- Registrar pagamento
INSERT INTO pagamento (ID_pedido, valor_pago, metodo_pagamento) VALUES 
(@last_pedido_id, 15.00, 'Cartão de Crédito');

COMMIT;