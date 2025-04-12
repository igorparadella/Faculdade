<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login_admin.php");
    exit();
}

$host = "localhost";
$db = "pastelaria";
$user = "root";
$pass = "usbw";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$cargosDisponiveis = array("Atendente", "Cozinheiro", "Caixa", "Gerente");
$mensagens = array();

$secao = $_GET['secao'];

function renderTable($conn, $title, $query, $secao, $id_col) {
    $res = $conn->query($query);
    if ($res && $res->num_rows > 0) {
        // Exibindo o título principal
        echo "<h5 class='mt-4'>" . htmlspecialchars($title) . "</h5><table class='table table-bordered table-sm'>";
        
        $header_shown = false;
        
        while ($row = $res->fetch_assoc()) {
            if (!$header_shown) {
                // Cabeçalho da tabela
                echo "<thead><tr>";
                
                // Verificando seção para personalizar título de coluna
                if($secao == "pedidos"){
                    echo "<th class='text-center'>Pedido</th>";
                }

                // Gerar os títulos das colunas dinamicamente com base nas chaves do array
                foreach ($row as $col => $val) {
                    echo "<th class='text-center'>" . htmlspecialchars($col) . "</th>";
                }
                
                // Coluna de ações
                echo "<th class='text-center'>Ações</th></tr></thead><tbody>";
                $header_shown = true;
            }
            
            // Exibindo dados da tabela
            echo "<tr>";
            
            if($secao == "pedidos"){
                // Link para mostrar pedido
                echo "<td class='text-center'><a href='?secao=pedido&id=" . $row[$id_col] . "' class='btn btn-info btn-sm'>Mostrar pedido</a></td>";
            }
            
            // Exibindo os valores das colunas
            foreach ($row as $val) {
                echo "<td class='text-center'>" . htmlspecialchars($val) . "</td>";
            }
            
            // Link de apagar
            echo "<td class='text-center'><a href='?secao=" . $secao . "&apagar=true&id=" . $row[$id_col] . "' class='btn btn-danger btn-sm'>Apagar</a></td>";
            echo "</tr>";
        }
        
        // Fechar a tabela
        echo "</tbody></table>";
    } else {
        // Mensagem caso não haja dados
        echo "<p class='text-muted'>" . htmlspecialchars($title) . ": Nenhum dado encontrado.</p>";
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_produto"])) {
    $nome = $_POST["nome"];
    $descricao = $_POST["descricao"];
    $preco = $_POST["preco"];
    $categoria = $_POST["categoria"];
    $estoque = $_POST["estoque"];
    $imagem_nome = "";

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $nomeOriginal = $_FILES['imagem']['name'];
        $temp = $_FILES['imagem']['tmp_name'];
        $pastaDestino = "uploads/";
        $imagem_nome = uniqid() . "-" . basename($nomeOriginal);
        $caminhoCompleto = $pastaDestino . $imagem_nome;

        if (move_uploaded_file($temp, $caminhoCompleto)) {
            $sql = "INSERT INTO Produto (nome, descricao, preco, categoria, estoque, imagem) VALUES ('$nome', '$descricao', $preco, $categoria, $estoque, '$imagem_nome')";
            if ($conn->query($sql)) {
                $mensagens[] = array('tipo' => 'success', 'texto' => 'Produto adicionado com sucesso.');
            } else {
                $mensagens[] = array('tipo' => 'danger', 'texto' => 'Erro ao adicionar produto.');
            }
        } else {
            $mensagens[] = array('tipo' => 'danger', 'texto' => 'Erro ao mover o arquivo.');
        }
    } else {
        $mensagens[] = array('tipo' => 'danger', 'texto' => 'Imagem não enviada.');
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_funcionario"])) {
    $nome = $_POST["nome"];
    $cargo = $_POST["cargo"];
    $telefone = $_POST["telefone"];
    $salario = $_POST["salario"];
    $conn->query("INSERT INTO Funcionario (nome, cargo, telefone, salario, data_admissao) VALUES ('$nome', '$cargo', '$telefone', $salario, NOW())");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_categoria"])) {
    $nome = $_POST["nome"];
    $conn->query("INSERT INTO Categoria (nome) VALUES ('$nome')");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["pagar"])) {
    $pedido = $_POST["pedido"];
    $valor = $_POST["valor"];
    $metodo = $_POST["metodo"];
    $conn->query("INSERT INTO Pagamento (ID_pedido, data_pagamento, valor_pago, metodo_pagamento) VALUES ($pedido, NOW(), $valor, '$metodo')");
}


if (isset($_GET['apagar']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $secao = $_GET['secao'];

    switch ($secao) {
        case 'produtos':
            $conn->query("DELETE FROM Produto WHERE ID_produto = $id");
            break;
        case 'funcionarios':
            $conn->query("DELETE FROM Funcionario WHERE ID_funcionario = $id");
            break;
        case 'categorias':
            $conn->query("DELETE FROM Categoria WHERE ID_categoria = $id");
            break;
        case 'clientes':
            $conn->query("DELETE FROM Cliente WHERE ID_cliente = $id");
            break;

            case 'pedidos':
                $conn->query("DELETE FROM Pedido WHERE ID_pedido = $id");
                break;
    }
    header("Location: admin.php?secao=$secao&msg=apagado");
    exit();
}

if (isset($_GET['deslogar'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$secao = isset($_GET['secao']) ? $_GET['secao'] : 'produtos';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Painel Administrativo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .alert-float {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            opacity: 1;
            transition: opacity 0.5s ease-in-out;
        }
    </style>
</head>
<body class="p-4">
<div class="container">
    <h1>Painel Administrativo</h1>
    <div class="mb-4">
        <a href="?secao=produtos" class="btn btn-info">Produtos</a>
        <a href="?secao=funcionarios" class="btn btn-info">Funcionários</a>
        <a href="?secao=categorias" class="btn btn-info">Categorias</a>
        <a href="?secao=pedidos" class="btn btn-info">Pedidos</a>
        <a href="?secao=pagamentos" class="btn btn-info">Pagamentos</a>
        <a href="?deslogar=true" class="btn btn-danger">Deslogar</a>
    </div>

    <?php foreach ($mensagens as $msg): ?>
        <div class="alert alert-<?php echo $msg['tipo']; ?> alert-dismissible fade show alert-float" role="alert">
            <?php echo $msg['texto']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endforeach; ?>

    <script>
        setTimeout(function () {
            var alertas = document.querySelectorAll('.alert');
            for (var i = 0; i < alertas.length; i++) {
                alertas[i].style.opacity = '0';
                (function(alerta) {
                    setTimeout(function() {
                        alerta.remove();
                    }, 500);
                })(alertas[i]);
            }
        }, 2000);
    </script>

    <?php
    switch ($secao) {
        case 'produtos':
            echo '<h3>Adicionar Produto</h3>'; ?>
            <form method="post" enctype="multipart/form-data" class="mb-4 border p-3 rounded bg-light">
                <input name="nome" class="form-control mb-2" placeholder="Nome">
                <textarea name="descricao" class="form-control mb-2" placeholder="Descrição"></textarea>
                <input name="preco" type="number" step="0.01" class="form-control mb-2" placeholder="Preço">
                <input name="estoque" type="number" class="form-control mb-2" placeholder="Estoque">
                <input name="imagem" type="file" accept="image/*" class="form-control mb-2">
                <select name="categoria" class="form-control mb-2">
                    <option value="">Selecione a Categoria</option>
                    <?php
                    $cat = $conn->query("SELECT ID_categoria, nome FROM Categoria");
                    while ($c = $cat->fetch_assoc()) {
                        echo "<option value='" . $c['ID_categoria'] . "'>" . $c['nome'] . "</option>";
                    }
                    ?>
                </select>
                <button name="add_produto" class="btn btn-success">Adicionar Produto</button>
            </form>
            <?php
            renderTable($conn, "Produtos", "SELECT * FROM Produto", "produtos", "ID_produto");
            break;

        case 'funcionarios':
            echo '<h3>Adicionar Funcionário</h3>'; ?>
            <form method="post" class="mb-4 border p-3 rounded bg-light">
                <input name="nome" class="form-control mb-2" placeholder="Nome">
                <select name="cargo" class="form-control mb-2">
                    <option value="">Selecione o Cargo</option>
                    <?php
                    foreach ($cargosDisponiveis as $cargo) {
                        echo "<option value='" . $cargo . "'>" . $cargo . "</option>";
                    }
                    ?>
                </select>
                <input name="telefone" class="form-control mb-2" placeholder="Telefone">
                <input name="salario" type="number" step="0.01" class="form-control mb-2" placeholder="Salário">
                <button name="add_funcionario" class="btn btn-primary">Adicionar Funcionário</button>
            </form>
            <?php
            renderTable($conn, "Funcionários", "SELECT * FROM Funcionario", "funcionarios", "ID_funcionario");
            break;

        case 'categorias':
            echo '<h3>Adicionar Categoria</h3>'; ?>
            <form method="post" class="mb-4 border p-3 rounded bg-light">
                <input name="nome" class="form-control mb-2" placeholder="Nome da Categoria">
                <button name="add_categoria" class="btn btn-secondary">Adicionar Categoria</button>
            </form>
            <?php
            renderTable($conn, "Categorias", "SELECT * FROM Categoria", "categorias", "ID_categoria");
            break;

        case 'pagamentos':
            echo '<h3>Registrar Pagamento</h3>'; ?>
            <form method="post" class="mb-4 border p-3 rounded bg-light">
                <input name="pedido" class="form-control mb-2" placeholder="ID do Pedido">
                <input name="valor" type="number" step="0.01" class="form-control mb-2" placeholder="Valor Pago">
                <select name="metodo" class="form-control mb-2">
                    <option value="Dinheiro">Dinheiro</option>
                    <option value="Cartão de Crédito">Cartão de Crédito</option>
                    <option value="Cartão de Débito">Cartão de Débito</option>
                    <option value="Pix">Pix</option>
                </select>
                <button name="pagar" class="btn btn-warning">Registrar Pagamento</button>
            </form>
            <?php
            renderTable($conn, "Pagamentos", "SELECT * FROM Pagamento", "pagamentos", "ID_pagamento");
            break;


                    case 'pagamentos':
            echo '<h3>Registrar Pagamento</h3>'; ?>
            <form method="post" class="mb-4 border p-3 rounded bg-light">
                <input name="pedido" class="form-control mb-2" placeholder="ID do Pedido">
                <input name="valor" type="number" step="0.01" class="form-control mb-2" placeholder="Valor Pago">
                <select name="metodo" class="form-control mb-2">
                    <option value="Dinheiro">Dinheiro</option>
                    <option value="Cartão de Crédito">Cartão de Crédito</option>
                    <option value="Cartão de Débito">Cartão de Débito</option>
                    <option value="Pix">Pix</option>
                </select>
                <button name="pagar" class="btn btn-warning">Registrar Pagamento</button>
            </form>
            <?php
            renderTable($conn, "Pagamentos", "SELECT * FROM Pagamento", "pagamentos", "ID_pagamento");
            break;

            case 'pagamentos':
            echo '<h3>Registrar Pagamento</h3>'; ?>
            <form method="post" class="mb-4 border p-3 rounded bg-light">
                <input name="pedido" class="form-control mb-2" placeholder="ID do Pedido">
                <input name="valor" type="number" step="0.01" class="form-control mb-2" placeholder="Valor Pago">
                <select name="metodo" class="form-control mb-2">
                    <option value="Dinheiro">Dinheiro</option>
                    <option value="Cartão de Crédito">Cartão de Crédito</option>
                    <option value="Cartão de Débito">Cartão de Débito</option>
                    <option value="Pix">Pix</option>
                </select>
                <button name="pagar" class="btn btn-warning">Registrar Pagamento</button>
            </form>
            <?php
            renderTable($conn, "Pagamentos", "SELECT * FROM Pagamento", "pagamentos", "ID_pagamento");
            break;

            case 'pedidos':
                echo '<h3>Registrar Pedido</h3>'; ?>
                <form method="post" class="mb-4 border p-3 rounded bg-light">
                <input name="pedido" class="form-control mb-2" placeholder="ID do Pedido">
                <input name="cliente" class="form-control mb-2" placeholder="ID do Clíente">
                <input name="data" class="form-control mb-2" type="date" id="dataPedido" placeholder="Data do Pedido">
                <script>
                document.getElementById('dataPedido').valueAsDate = new Date();
                </script>
                <select name="status" class="form-control mb-2">
                        <option value="Dinheiro">Dinheiro</option>
                        <option value="Cartão de Crédito">Cartão de Crédito</option>
                        <option value="Cartão de Débito">Cartão de Débito</option>
                        <option value="Pix">Pix</option>
                    </select>
                <input name="valor" type="number" step="0.01" class="form-control mb-2" placeholder="Valor Pago">

                    <button name="pedido" class="btn btn-warning">Registrar Pagamento</button>
                </form>
                <?php
                renderTable($conn, "Pedidos", "SELECT * FROM Pedido", "pedidos", "ID_pedido");
                break;

                case 'pedido':
                    echo '<h3>Registrar Pedido</h3>'; ?>
                    <form method="post" class="mb-4 border p-3 rounded bg-light">
                    <input name="pedido" class="form-control mb-2" placeholder="ID do Pedido">
                    <input name="cliente" class="form-control mb-2" placeholder="ID do Clíente">
                    <input name="data" class="form-control mb-2" type="date" id="dataPedido" placeholder="Data do Pedido">
                    <script>
                    document.getElementById('dataPedido').valueAsDate = new Date();
                    </script>
                    <select name="status" class="form-control mb-2">
                            <option value="Dinheiro">Dinheiro</option>
                            <option value="Cartão de Crédito">Cartão de Crédito</option>
                            <option value="Cartão de Débito">Cartão de Débito</option>
                            <option value="Pix">Pix</option>
                        </select>
                    <input name="valor" type="number" step="0.01" class="form-control mb-2" placeholder="Valor Pago">
    
                        <button name="pedido" class="btn btn-warning">Registrar Pagamento</button>
                    </form>
                    <?php
                    renderTable($conn, "item_pedido", "SELECT * FROM item_pedido where ID_pedido =" ID_pedido );
                    break;
    }
    ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>