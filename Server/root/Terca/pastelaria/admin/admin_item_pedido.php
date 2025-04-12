<?php
// Conectando ao banco de dados
include 'conexao.php';

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
// Obter o ID do pedido a partir da URL
if (isset($_GET['pedido_id'])) {
    $pedido_id = $_GET['pedido_id'];
} else {
    echo "Pedido não encontrado!";
    exit();
}

// Adicionar item ao pedido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ID_produto = $_POST['ID_produto'];
    $quantidade = $_POST['quantidade'];
    $preco_unitario = $_POST['preco_unitario'];
    
    // Inserir novo item no pedido
    $stmt = $conn->prepare("INSERT INTO item_pedido (ID_pedido, ID_produto, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $pedido_id, $ID_produto, $quantidade, $preco_unitario);
    $stmt->execute();
    $stmt->close();
    
    // Atualizar valor total do pedido
    $stmt = $conn->prepare("UPDATE pedido SET valor_total = (SELECT SUM(ip.quantidade * ip.preco_unitario) FROM item_pedido ip WHERE ip.ID_pedido = ?) WHERE ID_pedido = ?");
    $stmt->bind_param("ii", $pedido_id, $pedido_id);
    $stmt->execute();
    $stmt->close();
    
    // Redirecionar para a página de itens do pedido
    header("Location: admin_item_pedido.php?pedido_id=" . $pedido_id);
    exit();
}

// Deletar item do pedido
if (isset($_GET['delete'])) {
    $ID_item_pedido = $_GET['delete'];
    
    // Deletar o item
    $stmt = $conn->prepare("DELETE FROM item_pedido WHERE ID_item_pedido = ?");
    $stmt->bind_param("i", $ID_item_pedido);
    $stmt->execute();
    $stmt->close();
    
    // Atualizar o valor total do pedido após a exclusão
    $stmt = $conn->prepare("UPDATE pedido SET valor_total = (SELECT SUM(ip.quantidade * ip.preco_unitario) FROM item_pedido ip WHERE ip.ID_pedido = ?) WHERE ID_pedido = ?");
    $stmt->bind_param("ii", $pedido_id, $pedido_id);
    $stmt->execute();
    $stmt->close();
    
    // Redirecionar após a exclusão do item
    header("Location: admin_item_pedido.php?pedido_id=" . $pedido_id);
    exit();
}

// Obter produtos disponíveis para adicionar ao pedido
$produtos = $conn->query("SELECT ID_produto, nome, preco FROM produto");

// Listar os itens do pedido
$itens_pedido = $conn->query("SELECT ip.ID_item_pedido, p.nome AS produto, ip.quantidade, ip.preco_unitario, (ip.quantidade * ip.preco_unitario) AS total FROM item_pedido ip JOIN produto p ON ip.ID_produto = p.ID_produto WHERE ip.ID_pedido = $pedido_id");

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Itens do Pedido</title>
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
    <h2>Itens do Pedido #<?php echo $pedido_id; ?></h2>

    <!-- Formulário para adicionar novo item -->
    <h3>Adicionar Item</h3>
    <form action="admin_item_pedido.php?pedido_id=<?php echo $pedido_id; ?>" method="POST">
        <div class="mb-3">
            <label for="ID_produto" class="form-label">Produto</label>
            <select class="form-control" id="ID_produto" name="ID_produto" required onchange="atualizarPreco()">
                <option value="" disabled selected>Escolha o produto</option>
                <?php while ($produto = $produtos->fetch_assoc()): ?>
                    <option value="<?php echo $produto['ID_produto']; ?>" data-preco="<?php echo $produto['preco']; ?>"><?php echo $produto['nome']; ?> - R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="quantidade" class="form-label">Quantidade</label>
            <input type="number" class="form-control" id="quantidade" name="quantidade" min="1" required>
        </div>
        <div class="mb-3">
            <label for="preco_unitario" class="form-label">Preço Unitário</label>
            <input type="text" class="form-control" id="preco_unitario" name="preco_unitario" required readonly>
        </div>
        <button type="submit" class="btn btn-success">Adicionar Item</button>
    </form>

    <hr>

    <!-- Listagem de itens do pedido -->
    <h3>Itens no Pedido</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Preço Unitário</th>
                <th>Total</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $itens_pedido->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $item['produto']; ?></td>
                    <td><?php echo $item['quantidade']; ?></td>
                    <td>R$ <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?></td>
                    <td>R$ <?php echo number_format($item['total'], 2, ',', '.'); ?></td>
                    <td>
                        <!-- Link para excluir o item -->
                        <a href="admin_item_pedido.php?pedido_id=<?php echo $pedido_id; ?>&delete=<?php echo $item['ID_item_pedido']; ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este item?')">Excluir</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Função para atualizar o preço unitário ao selecionar um produto
function atualizarPreco() {
    var select = document.getElementById('ID_produto');
    var preco = select.options[select.selectedIndex].getAttribute('data-preco');
    document.getElementById('preco_unitario').value = preco;
}
</script>

</body>
</html>
