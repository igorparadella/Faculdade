<?php
// Conectando ao banco de dados
include 'conexao.php';

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
// Adicionar um pedido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ID_cliente = $_POST["ID_cliente"];
    $data_pedido = $_POST["data_pedido"];
    $status = $_POST["status"];
    
    if (isset($_POST["ID_pedido"])) {
        // Editando pedido
        $ID_pedido = $_POST["ID_pedido"];
        $stmt = $conn->prepare("UPDATE pedido SET ID_cliente = ?, data_pedido = ?, status = ? WHERE ID_pedido = ?");
        $stmt->bind_param("isss", $ID_cliente, $data_pedido, $status, $ID_pedido);
        $stmt->execute();
        $stmt->close();
    } else {
        // Inserção de novo pedido
        $stmt = $conn->prepare("INSERT INTO pedido (ID_cliente, data_pedido, status) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $ID_cliente, $data_pedido, $status);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: admin_pedido.php");
    exit();
}

// Deletar pedido
if (isset($_GET["delete"])) {
    $ID_pedido = $_GET["delete"];
    $stmt = $conn->prepare("DELETE FROM pedido WHERE ID_pedido = ?");
    $stmt->bind_param("i", $ID_pedido);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_pedido.php");
    exit();
}

// Definir data atual para o campo de Data do Pedido
$data_atual = date('Y-m-d');

// Obtendo os clientes para o select
$clientes = $conn->query("SELECT ID_cliente, nome FROM cliente");

// Status para o select
$status_options = array("Pendente", "Em andamento", "Concluído");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Pedidos</title>
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
<br><br>


<div class="container mt-5">
    <h2>Gestão de Pedidos</h2>

    <!-- Botão para abrir o formulário de cadastro -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cadastroPedidoModal">
        Cadastrar Pedido
    </button>

    <!-- Modal para cadastro de pedido -->
    <div class="modal fade" id="cadastroPedidoModal" tabindex="-1" aria-labelledby="cadastroPedidoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cadastroPedidoModalLabel">Cadastrar Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <form action="admin_pedido.php" method="POST">
                        <div class="mb-3">
                            <label for="ID_cliente" class="form-label">Cliente</label>
                            <select class="form-control" id="ID_cliente" name="ID_cliente" required>
                                <option value="" disabled selected>Escolha o cliente</option>
                                <?php while ($cliente = $clientes->fetch_assoc()): ?>
                                    <option value="<?php echo $cliente['ID_cliente']; ?>"><?php echo $cliente['nome']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="data_pedido" class="form-label">Data do Pedido</label>
                            <input type="date" class="form-control" id="data_pedido" name="data_pedido" value="<?php echo $data_atual; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="" disabled selected>Escolha o status</option>
                                <?php
                                    // Gerando as opções do select de status
                                    foreach ($status_options as $status_option) {
                                        echo "<option value=\"$status_option\">$status_option</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Cadastrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <!-- Listagem de pedidos -->
    <h3>Pedidos Cadastrados</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID Pedido</th>
                <th>Cliente</th>
                <th>Data do Pedido</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Exibindo os pedidos cadastrados
            $result = $conn->query("SELECT p.ID_pedido, c.nome AS cliente, p.data_pedido, p.status FROM pedido p JOIN cliente c ON p.ID_cliente = c.ID_cliente");
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['ID_pedido']}</td>";
                echo "<td>{$row['cliente']}</td>";
                echo "<td>{$row['data_pedido']}</td>";
                echo "<td>{$row['status']}</td>";
                echo "<td>
                        <a href='admin_item_pedido.php?pedido_id={$row['ID_pedido']}' class='btn btn-info'>Itens</a>
                        <a href='admin_pedido.php?delete={$row['ID_pedido']}' class='btn btn-danger' onclick='return confirm(\"Tem certeza que deseja excluir este pedido?\")'>Excluir</a>
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
