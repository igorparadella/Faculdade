-- SELECT_tests.sql
-- Consultas no banco de dados da pastelaria

-- 1. Selecionar todos os produtos com estoque baixo (menos de 40)
SELECT nome, estoque 
FROM produto 
WHERE estoque < 40;

-- 2. Selecionar pedidos com valor total acima de R$20,00
SELECT p.ID_pedido, c.nome AS cliente, p.valor_total, p.status
FROM pedido p
JOIN cliente c ON p.ID_cliente = c.ID_cliente
WHERE p.valor_total > 20.00;

-- 3. Selecionar funcionários e seus cargos
SELECT nome, cargo, salario 
FROM funcionario 
ORDER BY salario DESC;

-- 4. Selecionar produtos por categoria com informações completas
SELECT p.nome AS produto, p.preco, p.estoque, c.nome AS categoria
FROM produto p
JOIN categoria c ON p.categoria = c.ID_categoria
ORDER BY c.nome, p.nome;

-- 5. Selecionar histórico de pedidos de um cliente específico
SELECT p.ID_pedido, p.data_pedido, p.status, p.valor_total
FROM pedido p
WHERE p.ID_cliente = 1
ORDER BY p.data_pedido DESC;