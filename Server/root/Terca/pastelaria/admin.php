<?php
session_start();

// Verificando se o usuário está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: login_admin.php'); // Redireciona para a página de login
    exit();
}

// Função de logout
if (isset($_GET['logout'])) {
    session_destroy(); // Destrói a sessão
    header('Location: login_admin.php'); // Redireciona para a página de login
    exit();
}

// Conexão com o banco de dados
$conn = new mysqli("localhost", "root", "usbw", "pastelaria");
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Definir o charset para utf8mb4
$conn->set_charset("utf8mb4");

// Lista de tabelas disponíveis
$tabelas_disponiveis = array(
    "Cliente" => "Clientes",
    "Produto" => "Produtos",
    "Pedido" => "Pedidos",
    "Item_Pedido" => "Itens do Pedido",
    "Funcionario" => "Funcionários",
    "Pagamento" => "Pagamentos",
    "Categoria" => "Categorias"
);

// Obter tabela da URL
$tabela = isset($_GET['tabela']) ? $_GET['tabela'] : 'Cliente';

// Inserir novos dados se o formulário for submetido
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acao']) && $_POST['acao'] == 'adicionar') {
    $campos = '';
    $valores = '';
    foreach ($_POST as $campo => $valor) {
        if ($campo != 'tabela' && $campo != 'acao' && $valor != '') {
            $campos .= "$campo, ";
            $valores .= "'$valor', ";
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

// Apagar um registro
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
        .alert {
            margin-top: -100px;
            opacity: 1;
            transition: opacity 1s ease-out;
        }

        .alert.fade {
            opacity: 0;
        }
    </style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="admin.php">Admin Pastelaria</a>
        <form method="GET" class="d-flex">
            <select name="tabela" class="form-select" onchange="this.form.submit()">
                <?php
                foreach ($tabelas_disponiveis as $key => $label) {
                    $selected = ($tabela == $key) ? "selected" : "";
                    echo "<option value=\"$key\" $selected>$label</option>";
                }
                ?>
            </select>&nbsp&nbsp&nbsp
            <button class="btn btn-outline-light" type="submit">Atualizar</button>&nbsp&nbsp&nbsp
            <a href="admin.php?logout=true" class="btn btn-danger">Sair</a>

        </form>

    </div>
</nav>

<div class="container">
    <h2><?php echo isset($tabelas_disponiveis[$tabela]) ? $tabelas_disponiveis[$tabela] : 'Tabela'; ?></h2>
    
    <!-- Botão para abrir o modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#formModal">
        Adicionar Novo Registro
    </button>

    <!-- Modal de Adicionar Registro -->
    <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModalLabel">Adicionar Novo Registro - <?php echo $tabelas_disponiveis[$tabela]; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="admin.php?tabela=<?php echo $tabela; ?>">
                        <input type="hidden" name="acao" value="adicionar">
                        <?php
                        // Gerar formulário baseado na tabela selecionada
                        $resultado = $conn->query("DESCRIBE `$tabela`");
                        while ($col = $resultado->fetch_assoc()) {
                            if ($col['Field'] != 'ID_' . strtolower($tabela)) {  // Evitar o campo ID, que é AUTO_INCREMENT
                                echo "<div class='mb-3'>";
                                echo "<label for='" . $col['Field'] . "' class='form-label'>" . ucfirst($col['Field']) . "</label>";
                                echo "<input type='text' class='form-control' id='" . $col['Field'] . "' name='" . $col['Field'] . "' required>";
                                echo "</div>";
                            }
                        }
                        ?>
                        <button type="submit" class="btn btn-primary">Adicionar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensagens de sucesso ou erro -->
    <?php if (isset($mensagem)) { echo $mensagem; } ?>

    <!-- Tabela com os dados -->
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
                    echo "<th>Ações</th>"; // Coluna para ações (apagar)
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $res = $conn->query("SELECT * FROM `$tabela` LIMIT 100");
                if ($res) {
                    while ($row = $res->fetch_assoc()) {
                        echo "<tr>";
                        foreach ($colunas as $campo) {
                            echo "<td>" . htmlspecialchars($row[$campo]) . "</td>";
                        }
                        // Coluna de ações (botão de apagar)
                        echo "<td><a href='admin.php?tabela=$tabela&apagar_id=" . $row['ID_' . strtolower($tabela)] . "' class='btn btn-danger' onclick='return confirm(\"Tem certeza que deseja apagar este registro?\")'>Apagar</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='100%'>Erro ao buscar dados da tabela.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Função para ocultar a mensagem após 2 segundos
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
