 -- Criação do banco de dados
CREATE DATABASE pastelaria;
USE pastelaria;

-- Tabela: Categoria
CREATE TABLE Categoria (
    ID_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL
);

-- Tabela: Produto
CREATE TABLE Produto (
    ID_produto INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    estoque INT DEFAULT 0,
    ID_categoria INT,
    FOREIGN KEY (ID_categoria) REFERENCES Categoria(ID_categoria)
);

-- Tabela: Cliente
CREATE TABLE Cliente (
    ID_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telefone VARCHAR(20),
    endereco VARCHAR(255),
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela: Funcionario
CREATE TABLE Funcionario (
    ID_funcionario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cargo VARCHAR(50),
    telefone VARCHAR(20),
    salario DECIMAL(10,2),
    data_admissao DATE
);

-- Tabela: Pedido
CREATE TABLE Pedido (
    ID_pedido INT AUTO_INCREMENT PRIMARY KEY,
    ID_cliente INT,
    data_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'em preparo',
    valor_total DECIMAL(10,2),
    FOREIGN KEY (ID_cliente) REFERENCES Cliente(ID_cliente)
);

-- Tabela: Item_Pedido
CREATE TABLE Item_Pedido (
    ID_item_pedido INT AUTO_INCREMENT PRIMARY KEY,
    ID_pedido INT,
    ID_produto INT,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (ID_pedido) REFERENCES Pedido(ID_pedido),
    FOREIGN KEY (ID_produto) REFERENCES Produto(ID_produto)
);

-- Tabela: Pagamento
CREATE TABLE Pagamento (
    ID_pagamento INT AUTO_INCREMENT PRIMARY KEY,
    ID_pedido INT UNIQUE,
    data_pagamento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    valor_pago DECIMAL(10,2),
    metodo_pagamento VARCHAR(50),
    FOREIGN KEY (ID_pedido) REFERENCES Pedido(ID_pedido)
);

-- Tabela intermediária: Funcionario_Pedido (opcional, para registrar quem participou de cada pedido)
CREATE TABLE Funcionario_Pedido (
    ID_funcionario INT,
    ID_pedido INT,
    funcao_no_pedido VARCHAR(100), -- Ex: "atendente", "cozinheiro"
    PRIMARY KEY (ID_funcionario, ID_pedido),
    FOREIGN KEY (ID_funcionario) REFERENCES Funcionario(ID_funcionario),
    FOREIGN KEY (ID_pedido) REFERENCES Pedido(ID_pedido)
);




-- Inserir dados na tabela Categoria
INSERT INTO Categoria (nome) VALUES
('Pastéis'),
('Bebidas'),
('Sobremesas');

-- Inserir dados na tabela Produto
INSERT INTO Produto (nome, descricao, preco, estoque, ID_categoria) VALUES
('Pastel de Carne', 'Pastel com recheio de carne moída', 5.00, 50, 1),
('Pastel de Queijo', 'Pastel com recheio de queijo', 4.50, 30, 1),
('Suco de Laranja', 'Suco natural de laranja', 3.00, 100, 2),
('Coca-Cola', 'Refrigerante Coca-Cola', 4.00, 40, 2),
('Torta de Limão', 'Torta doce com recheio de limão', 6.00, 20, 3);

-- Inserir dados na tabela Cliente
INSERT INTO Cliente (nome, email, telefone, endereco) VALUES
('João Silva', 'joao@email.com', '(11) 1234-5678', 'Rua A, 123'),
('Maria Souza', 'maria@email.com', '(11) 9876-5432', 'Rua B, 456'),
('Carlos Pereira', 'carlos@email.com', '(11) 1122-3344', 'Rua C, 789');

-- Inserir dados na tabela Funcionario
INSERT INTO Funcionario (nome, cargo, telefone, salario, data_admissao) VALUES
('Ana Costa', 'Atendente', '(11) 3344-5566', 1500.00, '2023-01-10'),
('Pedro Alves', 'Cozinheiro', '(11) 6677-8899', 2000.00, '2022-11-01'),
('Lucas Oliveira', 'Caixa', '(11) 1122-3344', 1800.00, '2022-08-25');

-- Inserir dados na tabela Pedido
INSERT INTO Pedido (ID_cliente, status, valor_total) VALUES
(1, 'em preparo', 9.50),
(2, 'finalizado', 12.00),
(3, 'entregue', 6.50);

-- Inserir dados na tabela Item_Pedido
INSERT INTO Item_Pedido (ID_pedido, ID_produto, quantidade, preco_unitario) VALUES
(1, 1, 1, 5.00), -- Pastel de Carne
(1, 3, 1, 3.00), -- Suco de Laranja
(2, 2, 2, 4.50), -- Pastel de Queijo
(2, 4, 1, 4.00), -- Coca-Cola
(3, 5, 1, 6.00); -- Torta de Limão

-- Inserir dados na tabela Pagamento
INSERT INTO Pagamento (ID_pedido, valor_pago, metodo_pagamento) VALUES
(1, 9.50, 'dinheiro'),
(2, 12.00, 'cartão de crédito'),
(3, 6.50, 'PIX');

-- Inserir dados na tabela Funcionario_Pedido (se necessário)
INSERT INTO Funcionario_Pedido (ID_funcionario, ID_pedido, funcao_no_pedido) VALUES
(1, 1, 'Atendente'),
(2, 1, 'Cozinheiro'),
(3, 2, 'Caixa'),
(2, 3, 'Cozinheiro');


