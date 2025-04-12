<?php
include 'conexao.php';

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$editando = false;
$cliente = array('ID_cliente' => '', 'nome' => '', 'email' => '', 'telefone' => '', 'endereco' => '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];

    if ($_POST['id'] != '') {
        $id = $_POST['id'];
        $sql = "UPDATE cliente SET nome='$nome', email='$email', telefone='$telefone', endereco='$endereco' WHERE ID_cliente=$id";
        $conn->query($sql);
    } else {
        $sql = "INSERT INTO cliente (nome, email, telefone, endereco) VALUES ('$nome', '$email', '$telefone', '$endereco')";
        $conn->query($sql);
    }

    header("Location: admin_cliente.php");
    exit;
}

if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    $conn->query("DELETE FROM cliente WHERE ID_cliente=$id");
    header("Location: admin_cliente.php");
    exit;
}

$result = $conn->query("SELECT * FROM cliente ORDER BY ID_cliente DESC");
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



<body class="container">
    <h1 class="page-header">Gerenciar Clientes</h1>

    <button class="btn btn-success" data-toggle="modal" data-target="#clienteModal" onclick="abrirModal()">+ Novo Cliente</button>

    <br><br>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th><th>Nome</th><th>Email</th><th>Telefone</th><th>Endereço</th><th>Registro</th><th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['ID_cliente']; ?></td>
                <td><?php echo $row['nome']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['telefone']; ?></td>
                <td><?php echo $row['endereco']; ?></td>
                <td><?php echo $row['data_registro']; ?></td>
                <td>
                    <a href="admin_cliente.php?excluir=<?php echo $row['ID_cliente']; ?>" class="btn btn-danger btn-xs" onclick="return confirm('Excluir este cliente?')">Excluir</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Modal -->
    <div id="clienteModal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <form method="post" action="admin_cliente.php">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Cliente</h4>
              </div>
              <div class="modal-body">
                    <input type="hidden" name="id" id="cliente-id">
                    <div class="form-group">
                        <label>Nome</label>
                        <input type="text" name="nome" id="cliente-nome" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" id="cliente-email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Telefone</label>
                        <input type="text" name="telefone" id="cliente-telefone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Endereço</label>
                        <input type="text" name="endereco" id="cliente-endereco" class="form-control">
                    </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
              </div>
            </div>
        </form>
      </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script>
        function editarCliente(cliente) {
            $('#cliente-id').val(cliente.ID_cliente);
            $('#cliente-nome').val(cliente.nome);
            $('#cliente-email').val(cliente.email);
            $('#cliente-telefone').val(cliente.telefone);
            $('#cliente-endereco').val(cliente.endereco);
            $('#clienteModal').modal('show');
        }

        function abrirModal() {
            $('#cliente-id').val('');
            $('#cliente-nome').val('');
            $('#cliente-email').val('');
            $('#cliente-telefone').val('');
            $('#cliente-endereco').val('');
        }
    </script>
</body>
</html>
