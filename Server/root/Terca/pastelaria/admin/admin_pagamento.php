<?php
// Conectando ao banco de dados
include 'conexao.php';

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
// Adicionar pagamento
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pedido_id = $_POST['pedido_id'];
    $data_pagamento = $_POST['data_pagamento'];
    $valor_pago = $_POST['valor_pago'];
    $metodo_pagamento = $_POST['metodo_pagamento'];
    
    // Inserir novo pagamento
    $stmt = $conn->prepare("INSERT INTO pagamento (ID_pedido, data_pagamento, valor_pago, metodo_pagamento) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isds", $pedido_id, $data_pagamento, $valor_pago, $metodo_pagamento);
    $stmt->execute();
    $stmt->close();
    
    // Atualizar status do pedido para 'Pago'
    $stmt = $conn->prepare("UPDATE pedido SET status = 'Pago' WHERE ID_pedido = ?");
    $stmt->bind_param("i", $pedido_id);
    $stmt->execute();
    $stmt->close();
    
    // Redirecionar para a página de pagamento
    header("Location: admin_pagamento.php");
    exit();
}

// Obter todos os pagamentos
$pagamentos = $conn->query("SELECT p.ID_pagamento, p.data_pagamento, p.valor_pago, p.metodo_pagamento, pe.ID_pedido, cl.nome AS cliente_nome FROM pagamento p JOIN pedido pe ON p.ID_pedido = pe.ID_pedido JOIN cliente cl ON pe.ID_cliente = cl.ID_cliente");

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Pagamentos</title>
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
    <h2>Pagamentos Realizados</h2>

    <!-- Formulário para adicionar pagamento -->
    <h3>Adicionar Pagamento</h3>
    <form action="admin_pagamento.php" method="POST">
        <div class="mb-3">
            <label for="pedido_id" class="form-label">Pedido</label>
            <select class="form-control" id="pedido_id" name="pedido_id" required>
                <?php
                // Obter todos os pedidos para o formulário
                $pedidos = $conn->query("SELECT ID_pedido, ID_cliente FROM pedido");
                while ($pedido = $pedidos->fetch_assoc()):
                    $cliente_id = $pedido['ID_cliente'];
                    $cliente = $conn->query("SELECT nome FROM cliente WHERE ID_cliente = $cliente_id")->fetch_assoc();
                ?>
                    <option value="<?php echo $pedido['ID_pedido']; ?>">Pedido #<?php echo $pedido['ID_pedido']; ?> - Cliente: <?php echo $cliente['nome']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="data_pagamento" class="form-label">Data do Pagamento</label>
            <?php
                // Definindo a data de hoje no formato YYYY-MM-DD
                $data_hoje = date('Y-m-d');
            ?>
            <input type="date" class="form-control" id="data_pagamento" name="data_pagamento" value="<?php echo $data_hoje; ?>" required>
        </div>
        <div class="mb-3">
            <label for="valor_pago" class="form-label">Valor Pago</label>
            <input type="number" class="form-control" id="valor_pago" name="valor_pago" step="0.01" required>
        </div>
        <div class="mb-3">
            <label for="metodo_pagamento" class="form-label">Método de Pagamento</label>
            <select class="form-control" id="metodo_pagamento" name="metodo_pagamento" required>
                <option value="Dinheiro">Dinheiro</option>
                <option value="Cartão de Crédito">Cartão de Crédito</option>
                <option value="Cartão de Débito">Cartão de Débito</option>
                <option value="Transferência Bancária">Transferência Bancária</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Adicionar Pagamento</button>
    </form>

    <hr>

    <!-- Listagem de pagamentos -->
    <h3>Todos os Pagamentos</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Pedido</th>
                <th>Cliente</th>
                <th>Data do Pagamento</th>
                <th>Valor Pago</th>
                <th>Método de Pagamento</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($pagamento = $pagamentos->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $pagamento['ID_pedido']; ?></td>
                    <td><?php echo $pagamento['cliente_nome']; ?></td>
                    <td><?php echo $pagamento['data_pagamento']; ?></td>
                    <td>R$ <?php echo number_format($pagamento['valor_pago'], 2, ',', '.'); ?></td>
                    <td><?php echo $pagamento['metodo_pagamento']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
