-- UPDATE_tests.sql
-- Atualizações no banco de dados da pastelaria

-- 1. Atualizar o preço de um produto
UPDATE produto 
SET preco = 9.50 
WHERE ID_produto = 1;

-- 2. Atualizar o status de um pedido
UPDATE pedido 
SET status = 'entregue' 
WHERE ID_pedido = 2;

-- 3. Atualizar o salário de um funcionário
UPDATE funcionario 
SET salario = 2000.00 
WHERE ID_funcionario = 1;

-- 4. Atualizar o estoque de produtos após uma venda
UPDATE produto 
SET estoque = estoque - 2 
WHERE ID_produto = 1;

-- 5. Atualizar informações de um cliente
UPDATE cliente 
SET telefone = '11999999999', endereco = 'Av. Principal, 1000' 
WHERE ID_cliente = 6;