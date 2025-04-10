<?php
// Conexão com o banco
$conn = new mysqli("localhost", "root", "usbw", "pastelaria");
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Função para salvar pedido
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["fazer_pedido"])) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    $metodo_pagamento = $_POST['metodo_pagamento'];
    $produtos = $_POST['produtos'];

    // 1. Cadastrar cliente (ou buscar por email)
    $query_cliente = "SELECT ID_cliente FROM cliente WHERE email = ?";
    $stmt = $conn->prepare($query_cliente);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $cliente = $resultado->fetch_assoc();
        $id_cliente = $cliente['ID_cliente'];
    } else {
        $stmt = $conn->prepare("INSERT INTO cliente (nome, email, telefone, endereco, data_registro) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $nome, $email, $telefone, $endereco);
        $stmt->execute();
        $id_cliente = $stmt->insert_id;
    }

    // 2. Criar pedido
    $stmt = $conn->prepare("INSERT INTO pedido (ID_cliente, data_pedido, status, valor_total) VALUES (?, NOW(), 'em preparo', 0)");
    $stmt->bind_param("i", $id_cliente);
    $stmt->execute();
    $id_pedido = $stmt->insert_id;

    $valor_total = 0;
    foreach ($produtos as $id_produto => $quantidade) {
        if ($quantidade > 0) {
            // Buscar preço
            $query = "SELECT preco FROM produto WHERE ID_produto = ?";
            $stmt_preco = $conn->prepare($query);
            $stmt_preco->bind_param("i", $id_produto);
            $stmt_preco->execute();
            $result = $stmt_preco->get_result()->fetch_assoc();
            $preco_unitario = $result['preco'];
            $subtotal = $preco_unitario * $quantidade;
            $valor_total += $subtotal;

            // Inserir item_pedido
            $stmt = $conn->prepare("INSERT INTO item_pedido (ID_pedido, ID_produto, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiid", $id_pedido, $id_produto, $quantidade, $preco_unitario);
            $stmt->execute();
        }
    }

    // Atualizar valor_total no pedido
    $stmt = $conn->prepare("UPDATE pedido SET valor_total = ? WHERE ID_pedido = ?");
    $stmt->bind_param("di", $valor_total, $id_pedido);
    $stmt->execute();

    // Registrar pagamento
    $stmt = $conn->prepare("INSERT INTO pagamento (ID_pedido, data_pagamento, valor_pago, metodo_pagamento) VALUES (?, NOW(), ?, ?)");
    $stmt->bind_param("ids", $id_pedido, $valor_total, $metodo_pagamento);
    $stmt->execute();

    $mensagem = "Pedido realizado com sucesso!";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Pastelaria do Igor</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; }
    .btn-vermelho { background-color: #dc3545; color: white; }
    .btn-vermelho:hover { background-color: #c82333; }
    .card-img-top { height: 200px; object-fit: cover; }
  </style>
</head>
<body>
<div class="container my-5">
  <h1 class="text-center mb-4">🍽️ Bem-vindo à Pastelaria do Igor</h1>

  <?php if (isset($mensagem)) echo "<div class='alert alert-success'>$mensagem</div>"; ?>

  <h2 class="mb-3">Nosso Cardápio</h2>
  <form method="POST" class="row g-4">
    <div class="row">
      <?php
      $query = "SELECT * FROM produto";
      $resultado = $conn->query($query);
      while ($produto = $resultado->fetch_assoc()) {
          echo "
          <div class='col-md-4'>
            <div class='card shadow-sm'>
              <img src='https://via.placeholder.com/400x200?text=".urlencode($produto['nome'])."' class='card-img-top'>
              <div class='card-body'>
                <h5 class='card-title'>{$produto['nome']}</h5>
                <p class='card-text'>{$produto['descricao']}</p>
                <p class='card-text'><strong>R$ ".number_format($produto['preco'], 2, ',', '.')."</strong></p>
                <label>Quantidade:</label>
                <input type='number' class='form-control' name='produtos[{$produto['ID_produto']}]' min='0' max='99' value='0'>
              </div>
            </div>
          </div>";
      }
      ?>
    </div>

    <h2 class="mt-5">Seus Dados</h2>
    <div class="col-md-6">
      <label>Nome:</label>
      <input type="text" name="nome" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label>Email:</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label>Telefone:</label>
      <input type="text" name="telefone" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label>Endereço:</label>
      <input type="text" name="endereco" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label>Forma de Pagamento:</label>
      <select name="metodo_pagamento" class="form-select" required>
        <option value="PIX">PIX</option>
        <option value="Dinheiro">Dinheiro</option>
        <option value="Cartão">Cartão</option>
      </select>
    </div>
    <div class="col-12 mt-4">
      <button type="submit" name="fazer_pedido" class="btn btn-vermelho w-100">Finalizar Pedido</button>
    </div>
  </form>
</div>

<footer class="text-center mt-5 mb-3 text-muted">
  &copy; <?php echo date("Y"); ?> Pastelaria do Igor
</footer>
</body>
</html>
