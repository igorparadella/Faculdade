<?php
// index.php
include 'admin/conexao.php';

// Busca as categorias
$sqlCategorias = "SELECT * FROM categoria";
$resultCategorias = $conn->query($sqlCategorias);

// Busca os produtos por categoria
$sqlProdutos = "SELECT * FROM produto";
$resultProdutos = $conn->query($sqlProdutos);
$produtosPorCategoria = array();

while ($produto = $resultProdutos->fetch_assoc()) {
    $produtosPorCategoria[$produto['categoria']][] = $produto;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pastelaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h1 class="text-center mb-4">Bem-vindo à Nossa Pastelaria</h1>

    <?php while($categoria = $resultCategorias->fetch_assoc()): ?>
        <div class="mb-4">
            <h3><?php echo $categoria['nome']; ?></h3>
            <div id="carousel-<?php echo $categoria['ID_categoria']; ?>" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php 
                    // Agrupando produtos em blocos de 3
                    $produtosArray = $produtosPorCategoria[$categoria['ID_categoria']];
                    $chunks = array_chunk($produtosArray, 3); // Divide os produtos em grupos de 3
                    $first = true;
                    foreach ($chunks as $chunk): ?>
                        <div class="carousel-item <?php echo $first ? 'active' : ''; ?>">
                            <div class="row">
                                <?php foreach ($chunk as $produto): ?>
                                    <div class="col-md-4">
                                        <img src="images/<?php echo $produto['imagem']; ?>" class="d-block w-100" alt="<?php echo $produto['nome']; ?>">
                                        <h5><?php echo $produto['nome']; ?></h5>
                                        <p><?php echo $produto['descricao']; ?></p>
                                        <p><strong>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></strong></p>
                                        <p><a href="pedido.php?id=<?php echo $produto['ID_produto']; ?>" class="btn btn-primary">Adicionar ao Pedido</a></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php 
                    $first = false;
                    endforeach;
                    ?>
                </div>

                <!-- Setas de Navegação -->
                <button class="carousel-control-prev" type="button" data-bs-target="#carousel-<?php echo $categoria['ID_categoria']; ?>" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true">
                        <strong>&lt;</strong> <!-- Seta Esquerda -->
                    </span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carousel-<?php echo $categoria['ID_categoria']; ?>" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true">
                        <strong>&gt;</strong> <!-- Seta Direita -->
                    </span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
