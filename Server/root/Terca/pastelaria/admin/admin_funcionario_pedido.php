<?php
// Conectando ao banco de dados
include 'conexao.php';

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
// Adicionar associação de funcionário ao pedido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $funcionario_id = $_POST['funcionario_id'];
    $pedido_id = $_POST['pedido_id'];
    $funcao_no_pedido = $_POST['funcao_no_pedido'];
    
    // Inserir nova associação de funcionário a pedido
    $stmt = $conn->prepare("INSERT INTO funcionario_pedido (ID_funcionario, ID_pedido, funcao_no_pedido) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $funcionario_id, $pedido_id, $funcao_no_pedido);
    $stmt->execute();
    $stmt->close();
    
    // Redirecionar para a mesma página após o submit
    header("Location: admin_funcionario_pedido.php");
    exit();
}

// Obter todos os funcionários e pedidos para o formulário
$funcionarios = $conn->query("SELECT ID_funcionario, nome FROM funcionario");
$pedidos = $conn->query("SELECT ID_pedido, ID_cliente FROM pedido");

// Obter todas as associações de funcionários a pedidos
$associacoes = $conn->query("SELECT fp.ID_funcionario, fp.ID_pedido, fp.funcao_no_pedido, f.nome AS funcionario_nome, p.ID_cliente FROM funcionario_pedido fp JOIN funcionario f ON fp.ID_funcionario = f.ID_funcionario JOIN pedido p ON fp.ID_pedido = p.ID_pedido");

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Associação de Funcionários com Pedidos</title>
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
    <h2>Associação de Funcionários com Pedidos</h2>

    <!-- Formulário para adicionar associação -->
    <h3>Adicionar Associação</h3>
    <form action="admin_funcionario_pedido.php" method="POST">
        <div class="mb-3">
            <label for="funcionario_id" class="form-label">Funcionário</label>
            <select class="form-control" id="funcionario_id" name="funcionario_id" required>
                <?php while ($funcionario = $funcionarios->fetch_assoc()): ?>
                    <option value="<?php echo $funcionario['ID_funcionario']; ?>"><?php echo $funcionario['nome']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="pedido_id" class="form-label">Pedido</label>
            <select class="form-control" id="pedido_id" name="pedido_id" required>
                <?php while ($pedido = $pedidos->fetch_assoc()): ?>
                    <option value="<?php echo $pedido['ID_pedido']; ?>">Pedido #<?php echo $pedido['ID_pedido']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="funcao_no_pedido" class="form-label">Função no Pedido</label>
            <input type="text" class="form-control" id="funcao_no_pedido" name="funcao_no_pedido" required>
        </div>
        <button type="submit" class="btn btn-success">Adicionar Associação</button>
    </form>

    <hr>

    <!-- Listagem de associações de funcionários com pedidos -->
    <h3>Associações Existentes</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Funcionário</th>
                <th>Pedido</th>
                <th>Função</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($associacao = $associacoes->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $associacao['funcionario_nome']; ?></td>
                    <td>Pedido #<?php echo $associacao['ID_pedido']; ?></td>
                    <td><?php echo $associacao['funcao_no_pedido']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
