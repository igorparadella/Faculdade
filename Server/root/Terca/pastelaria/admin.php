<?php
session_start();

// Verificando se o usuário está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: login_admin.php');
    exit();
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login_admin.php');
    exit();
}

$conn = new mysqli("localhost", "root", "usbw", "pastelaria");
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$tabelas_disponiveis = array(
    "Cliente" => "Clientes",
    "Produto" => "Produtos",
    "Pedido" => "Pedidos",
    "Item_Pedido" => "Itens do Pedido",
    "Funcionario" => "Funcionários",
    "Pagamento" => "Pagamentos",
    "Categoria" => "Categorias"
);

$tabela = isset($_GET['tabela']) ? $_GET['tabela'] : 'Cliente';

// Inserção de dados
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acao']) && $_POST['acao'] == 'adicionar') {
    $campos = '';
    $valores = '';

    foreach ($_POST as $campo => $valor) {
        if ($campo != 'tabela' && $campo != 'acao') {
            if (preg_match('/foto|imagem/i', $campo)) {
                if (isset($_FILES[$campo]) && $_FILES[$campo]['error'] == UPLOAD_ERR_OK) {
                    $nomeArquivo = basename($_FILES[$campo]['name']);
                    $caminhoDestino = 'uploads/' . time() . '_' . $nomeArquivo;
                    move_uploaded_file($_FILES[$campo]['tmp_name'], $caminhoDestino);
                    $campos .= "$campo, ";
                    $valores .= "'$caminhoDestino', ";
                }
            } elseif ($valor !== '') {
                $campos .= "$campo, ";
                $valores .= "'$valor', ";
            }
        }
    }

    $campos = rtrim($campos, ', ');
    $valores = rtrim($valores, ', ');

    $query = "INSERT INTO `$tabela` ($campos) VALUES ($valores)";
    if ($conn->query($query) === TRUE) {
        $mensagem = "<div class='alert alert-success'>Novo registro inserido com sucesso!</div>";
    } else {
        $mensagem = "<div class='alert alert-danger'>Erro ao inserir dados: " . $conn->error . "</div>";
    }
}

if (isset($_GET['apagar_id'])) {
    $id = $_GET['apagar_id'];
    $query = "DELETE FROM `$tabela` WHERE ID_" . strtolower($tabela) . " = $id";
    if ($conn->query($query) === TRUE) {
        $mensagem = "<div class='alert alert-success'>Registro apagado com sucesso!</div>";
    } else {
        $mensagem = "<div class='alert alert-danger'>Erro ao apagar o registro: " . $conn->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Admin - Pastelaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .alert { margin-top: -100px; opacity: 1; transition: opacity 1s ease-out; }
        .alert.fade { opacity: 0; }
    </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="admin.php">Admin Pastelaria</a>
        <form method="GET" class="d-flex">
            <select name="tabela" class="form-select" onchange="this.form.submit()">
                <?php foreach ($tabelas_disponiveis as $key => $label) {
                    $selected = ($tabela == $key) ? "selected" : "";
                    echo "<option value=\"$key\" $selected>$label</option>";
                } ?>
            </select>&nbsp;&nbsp;&nbsp;
            <button class="btn btn-outline-light" type="submit">Atualizar</button>&nbsp;&nbsp;&nbsp;
            <a href="admin.php?logout=true" class="btn btn-danger">Sair</a>
        </form>
    </div>
</nav>

<div class="container">
    <h2><?php echo $tabelas_disponiveis[$tabela]; ?></h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#formModal">Adicionar Novo Registro</button>

    <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="admin.php?tabela=<?php echo $tabela; ?>" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Adicionar Novo Registro - <?php echo $tabelas_disponiveis[$tabela]; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="acao" value="adicionar">
                        <?php
                        $resultado = $conn->query("DESCRIBE `$tabela`");
                        while ($col = $resultado->fetch_assoc()) {
                            $nomeCampo = $col['Field'];
                            if ($nomeCampo != 'ID_' . strtolower($tabela)) {
                                echo "<div class='mb-3'>";
                                echo "<label class='form-label'>" . ucfirst($nomeCampo) . "</label>";
                                if (preg_match('/foto|imagem/i', $nomeCampo)) {
                                    echo "<input type='file' class='form-control' name='$nomeCampo' accept='image/*'>";
                                } else {
                                    echo "<input type='text' class='form-control' name='$nomeCampo' required>";
                                }
                                echo "</div>";
                            }
                        }
                        ?>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Adicionar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php if (isset($mensagem)) echo $mensagem; ?>

    <div class="table-responsive mt-5">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <?php
                    $colunas = array();
                    $resultado = $conn->query("DESCRIBE `$tabela`");
                    while ($col = $resultado->fetch_assoc()) {
                        $colunas[] = $col['Field'];
                        echo "<th>" . htmlspecialchars($col['Field']) . "</th>";
                    }
                    echo "<th>Ações</th>";
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $res = $conn->query("SELECT * FROM `$tabela` LIMIT 100");
                while ($row = $res->fetch_assoc()) {
                    echo "<tr>";
                    foreach ($colunas as $campo) {
                        if (preg_match('/foto|imagem/i', $campo) && file_exists($row[$campo])) {
                            echo "<td><img src='" . $row[$campo] . "' width='60'></td>";
                        } else {
                            echo "<td>" . htmlspecialchars($row[$campo]) . "</td>";
                        }
                    }
                    echo "<td><a href='admin.php?tabela=$tabela&apagar_id=" . $row['ID_' . strtolower($tabela)] . "' class='btn btn-danger' onclick='return confirm(\"Tem certeza que deseja apagar este registro?\")'>Apagar</a></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    setTimeout(function() {
        var alert = document.querySelector('.alert');
        if (alert) {
            alert.classList.add('fade');
        }
    }, 2000);
</script>

</body>
</html>
<?php $conn->close(); ?>
