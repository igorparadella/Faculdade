<?php
// Conectando ao banco de dados
include 'conexao.php';

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pegando os dados do formulário
    $nome = $_POST["nome"];
    $descricao = $_POST["descricao"];
    $preco = $_POST["preco"];
    $categoria = $_POST["categoria"];
    $estoque = $_POST["estoque"];

    // Inicializa a variável de imagem
    $imagem_nome = null;

    // Verifica se há um arquivo sendo enviado
    if (!empty($_FILES["imagem"]["name"])) {
        // Verifica se o upload foi bem-sucedido
        if ($_FILES["imagem"]["error"] == 0) {
            // Gera um nome único para a imagem
            $imagem_nome = basename($_FILES["imagem"]["name"]);
            // Caminho para salvar a imagem na pasta "imagens"
            $destino = "imagens/" . $imagem_nome;

            // Tenta mover a imagem para a pasta
            if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $destino)) {
                echo "Imagem enviada com sucesso!";
            } else {
                echo "Erro ao salvar a imagem na pasta.";
            }
        } else {
            echo "Erro no upload da imagem: " . $_FILES["imagem"]["error"];
        }
    }

    // Inserção no banco de dados
    $stmt = $conn->prepare("INSERT INTO produto (nome, descricao, preco, categoria, estoque, imagem) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdiis", $nome, $descricao, $preco, $categoria, $estoque, $imagem_nome);
    $stmt->execute();
    $stmt->close();

    // Redireciona de volta para a página admin_produto.php após o cadastro
    header("Location: admin_produto.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Produtos</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="estilo.css">
</head>
<body>

    
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <a class="navbar-brand" href="#">Dashboard Administrativo</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
      <li class="nav-item"><a class="nav-link" href="admin_cliente.php">Clientes</a></li>
      <li class="nav-item"><a class="nav-link" href="admin_produto.php">Produtos</a></li>
      <li class="nav-item"><a class="nav-link" href="admin_pedido.php">Pedidos</a></li>
      <li class="nav-item"><a class="nav-link" href="admin_funcionario.php">Funcionários</a></li>
      <li class="nav-item"><a class="nav-link" href="admin_pagamento.php">Pagamentos</a></li>
      <li class="nav-item">
        <a class="nav-link btn btn-danger text-white" href="logout.php">Sair</a>
      </li>
    </ul>
  </div>
</nav>

<div class="container mt-5">
    <h2>Cadastro de Produto</h2>

    <!-- Botão para abrir o formulário -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cadastroProdutoModal">
        Cadastrar Produto
    </button>

    <!-- Modal para cadastro de produto -->
    <div class="modal fade" id="cadastroProdutoModal" tabindex="-1" aria-labelledby="cadastroProdutoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cadastroProdutoModalLabel">Cadastrar Produto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <!-- Formulário de cadastro de produto -->
                    <form action="admin_produto.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome do Produto</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="preco" class="form-label">Preço</label>
                            <input type="number" class="form-control" id="preco" name="preco" required>
                        </div>
                        <div class="mb-3">
                            <label for="categoria" class="form-label">Categoria</label>
                            <select class="form-control" id="categoria" name="categoria" required>
                                <!-- Aqui, você pode adicionar as opções de categoria do seu banco de dados -->
                                <?php
                                $result = $conn->query("SELECT * FROM categoria");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['ID_categoria']}'>{$row['nome']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="estoque" class="form-label">Estoque</label>
                            <input type="number" class="form-control" id="estoque" name="estoque" required>
                        </div>
                        <div class="mb-3">
                            <label for="imagem" class="form-label">Imagem</label>
                            <input type="file" class="form-control" id="imagem" name="imagem">
                        </div>
                        <button type="submit" class="btn btn-success">Cadastrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <!-- Listagem de produtos -->
    <h3>Produtos Cadastrados</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Preço</th>
                <th>Categoria</th>
                <th>Estoque</th>
                <th>Imagem</th>
                <th>Apagar</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Exibindo os produtos cadastrados
            $result = $conn->query("SELECT p.*, c.nome AS categoria_nome FROM produto p JOIN categoria c ON p.categoria = c.ID_categoria");
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['nome']}</td>";
                echo "<td>{$row['preco']}</td>";
                echo "<td>{$row['categoria_nome']}</td>";
                echo "<td>{$row['estoque']}</td>";
                echo "<td><img src='imagens/{$row['imagem']}' alt='Imagem do produto' width='100'></td>";
                echo "<td>
                        <a href='#' class='btn btn-danger'>Excluir</a>
                    </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
