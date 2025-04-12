<?php
include 'conexao.php';

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}


// Contagens para os cards
$clientes = $conn->query("SELECT COUNT(*) AS total FROM cliente")->fetch_assoc();
$pedidos = $conn->query("SELECT COUNT(*) AS total FROM pedido")->fetch_assoc();
$produtos = $conn->query("SELECT COUNT(*) AS total FROM produto")->fetch_assoc();
$funcionarios = $conn->query("SELECT COUNT(*) AS total FROM funcionario")->fetch_assoc();

// Pedidos por status
$statusData = array();
$res = $conn->query("SELECT status, COUNT(*) as total FROM pedido GROUP BY status");
while ($row = $res->fetch_assoc()) {
    $statusData[] = $row;
}

// Pagamentos por mês
$pagamentoData = array();
$res = $conn->query("SELECT DATE_FORMAT(data_pagamento, '%Y-%m') as mes, SUM(valor_pago) as total FROM pagamento GROUP BY mes ORDER BY mes");
while ($row = $res->fetch_assoc()) {
    $pagamentoData[] = $row;
}

// Produtos mais vendidos
$produtoData = array();
$res = $conn->query("
    SELECT p.nome, SUM(ip.quantidade) as total 
    FROM item_pedido ip
    JOIN produto p ON ip.ID_produto = p.ID_produto
    GROUP BY p.nome
    ORDER BY total DESC
    LIMIT 5
");
while ($row = $res->fetch_assoc()) {
    $produtoData[] = $row;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Pastelaria</title>
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


<div class="container">

  <!-- Quick Overview Cards -->
  <div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card shadow-sm border-left-primary">
        <div class="card-body">
          <i class="fas fa-users"></i>
          <h5 class="card-title">Clientes</h5>
          <p class="card-text"><?php echo $clientes['total']; ?></p>
        </div>
        <div class="card-footer">
          <a href="admin_cliente.php" class="btn btn-primary btn-sm">Ver Todos</a>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card shadow-sm border-left-success">
        <div class="card-body">
          <i class="fas fa-box-open"></i>
          <h5 class="card-title">Pedidos</h5>
          <p class="card-text"><?php echo $pedidos['total']; ?></p>
        </div>
        <div class="card-footer">
          <a href="admin_pedido.php" class="btn btn-success btn-sm">Ver Todos</a>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card shadow-sm border-left-warning">
        <div class="card-body">
          <i class="fas fa-cogs"></i>
          <h5 class="card-title">Produtos</h5>
          <p class="card-text"><?php echo $produtos['total']; ?></p>
        </div>
        <div class="card-footer">
          <a href="admin_produto.php" class="btn btn-warning btn-sm">Ver Todos</a>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card shadow-sm border-left-danger">
        <div class="card-body">
          <i class="fas fa-users-cog"></i>
          <h5 class="card-title">Funcionários</h5>
          <p class="card-text"><?php echo $funcionarios['total']; ?></p>
        </div>
        <div class="card-footer">
          <a href="admin_funcionario.php" class="btn btn-danger btn-sm">Ver Todos</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Gráficos -->
  <div class="chart-container">
    <h4 class="text-center">Pedidos por Status</h4>
    <canvas id="statusChart"></canvas>
  </div>

  <div class="chart-container">
    <h4 class="text-center">Pagamentos por Mês</h4>
    <canvas id="pagamentoChart"></canvas>
  </div>

  <div class="chart-container">
    <h4 class="text-center">Produtos Mais Vendidos</h4>
    <canvas id="produtoChart"></canvas>
  </div>

</div>

<script>
  // Status do Pedido
  var statusChart = new Chart(document.getElementById('statusChart'), {
    type: 'pie',
    data: {
      labels: [<?php foreach($statusData as $s) echo "'" . $s['status'] . "',"; ?>],
      datasets: [{
        data: [<?php foreach($statusData as $s) echo $s['total'] . ','; ?>],
        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e']
      }]
    }
  });

  // Pagamento por Mês
  var pagamentoChart = new Chart(document.getElementById('pagamentoChart'), {
    type: 'line',
    data: {
      labels: [<?php foreach($pagamentoData as $p) echo "'" . $p['mes'] . "',"; ?>],
      datasets: [{
        label: 'Pagamentos R$',
        data: [<?php foreach($pagamentoData as $p) echo $p['total'] . ','; ?>],
        backgroundColor: 'rgba(54, 162, 235, 0.2)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
      }]
    }
  });

  // Produtos mais vendidos
  var produtoChart = new Chart(document.getElementById('produtoChart'), {
    type: 'bar',
    data: {
      labels: [<?php foreach($produtoData as $p) echo "'" . $p['nome'] . "',"; ?>],
      datasets: [{
        label: 'Unidades Vendidas',
        data: [<?php foreach($produtoData as $p) echo $p['total'] . ','; ?>],
        backgroundColor: '#36b9cc'
      }]
    }
  });
</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
