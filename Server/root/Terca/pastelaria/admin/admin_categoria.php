<?php
// Conectando ao banco de dados
include("conexao.php");
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pegando os dados do formulário
    $nome = $_POST["nome"];

    // Verifica se a categoria existe para edição
    if (isset($_POST["ID_categoria"])) {
        $ID_categoria = $_POST["ID_categoria"];
        // Editando a categoria
        $stmt = $conn->prepare("UPDATE categoria SET nome = ? WHERE ID_categoria = ?");
        $stmt->bind_param("si", $nome, $ID_categoria);
        $stmt->execute();
        $stmt->close();
    } else {
        // Inserção de nova categoria
        $stmt = $conn->prepare("INSERT INTO categoria (nome) VALUES (?)");
        $stmt->bind_param("s", $nome);
        $stmt->execute();
        $stmt->close();
    }

    // Redireciona de volta para a página admin_categoria.php após o cadastro ou edição
    header("Location: admin_categoria.php");
    exit();
}

// Deletando categoria
if (isset($_GET["delete"])) {
    $ID_categoria = $_GET["delete"];
    $stmt = $conn->prepare("DELETE FROM categoria WHERE ID_categoria = ?");
    $stmt->bind_param("i", $ID_categoria);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_categoria.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Categorias</title>
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
    <h2>Gestão de Categorias</h2>

    <!-- Botão para abrir o formulário de cadastro -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cadastroCategoriaModal">
        Cadastrar Categoria
    </button>

    <!-- Modal para cadastro de categoria -->
    <div class="modal fade" id="cadastroCategoriaModal" tabindex="-1" aria-labelledby="cadastroCategoriaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cadastroCategoriaModalLabel">Cadastrar Categoria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <!-- Formulário de cadastro de categoria -->
                    <form action="admin_categoria.php" method="POST">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome da Categoria</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        <button type="submit" class="btn btn-success">Cadastrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <!-- Listagem de categorias -->
    <h3>Categorias Cadastradas</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Exibindo as categorias cadastradas
            $result = $conn->query("SELECT * FROM categoria");
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['nome']}</td>";
                echo "<td>
                        <a href='#' class='btn btn-warning' data-bs-toggle='modal' data-bs-target='#editarCategoriaModal' data-id='{$row['ID_categoria']}' data-nome='{$row['nome']}'>Editar</a>
                        <a href='admin_categoria.php?delete={$row['ID_categoria']}' class='btn btn-danger' onclick='return confirm(\"Tem certeza que deseja excluir esta categoria?\")'>Excluir</a>
                    </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Modal de edição de categoria -->
<div class="modal fade" id="editarCategoriaModal" tabindex="-1" aria-labelledby="editarCategoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarCategoriaModalLabel">Editar Categoria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <!-- Formulário de edição de categoria -->
                <form action="admin_categoria.php" method="POST">
                    <input type="hidden" id="edit_ID_categoria" name="ID_categoria">
                    <div class="mb-3">
                        <label for="edit_nome" class="form-label">Nome da Categoria</label>
                        <input type="text" class="form-control" id="edit_nome" name="nome" required>
                    </div>
                    <button type="submit" class="btn btn-success">Salvar Alterações</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Preenche o formulário de edição com os dados da categoria
    var editarCategoriaModal = document.getElementById('editarCategoriaModal');
    editarCategoriaModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var idCategoria = button.getAttribute('data-id');
        var nomeCategoria = button.getAttribute('data-nome');
        
        var modalTitle = editarCategoriaModal.querySelector('.modal-title');
        modalTitle.textContent = 'Editar Categoria: ' + nomeCategoria;

        var inputIdCategoria = editarCategoriaModal.querySelector('#edit_ID_categoria');
        var inputNomeCategoria = editarCategoriaModal.querySelector('#edit_nome');

        inputIdCategoria.value = idCategoria;
        inputNomeCategoria.value = nomeCategoria;
    });
</script>

</body>
</html>
