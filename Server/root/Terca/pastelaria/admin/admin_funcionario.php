<?php
// Conectando ao banco de dados
include 'conexao.php';

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pegando os dados do formulário
    $nome = $_POST["nome"];
    $cargo = $_POST["cargo"];
    $telefone = $_POST["telefone"];
    $salario = $_POST["salario"];
    $data_admissao = $_POST["data_admissao"];

    // Verifica se o ID do funcionário está presente para edição
    if (isset($_POST["ID_funcionario"])) {
        $ID_funcionario = $_POST["ID_funcionario"];
        // Editando o funcionário
        $stmt = $conn->prepare("UPDATE funcionario SET nome = ?, cargo = ?, telefone = ?, salario = ?, data_admissao = ? WHERE ID_funcionario = ?");
        $stmt->bind_param("sssssi", $nome, $cargo, $telefone, $salario, $data_admissao, $ID_funcionario);
        $stmt->execute();
        $stmt->close();
    } else {
        // Inserção de novo funcionário
        $stmt = $conn->prepare("INSERT INTO funcionario (nome, cargo, telefone, salario, data_admissao) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nome, $cargo, $telefone, $salario, $data_admissao);
        $stmt->execute();
        $stmt->close();
    }

    // Redireciona de volta para a página admin_funcionario.php após o cadastro ou edição
    header("Location: admin_funcionario.php");
    exit();
}

// Deletando funcionário
if (isset($_GET["delete"])) {
    $ID_funcionario = $_GET["delete"];
    $stmt = $conn->prepare("DELETE FROM funcionario WHERE ID_funcionario = ?");
    $stmt->bind_param("i", $ID_funcionario);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_funcionario.php");
    exit();
}

// Definir data atual para o campo de Data de Admissão
$data_atual = date('Y-m-d');  // Obtendo a data atual
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Funcionários</title>
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
    <h2>Gestão de Funcionários</h2>

    <!-- Botão para abrir o formulário de cadastro -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cadastroFuncionarioModal">
        Cadastrar Funcionário
    </button>

    <!-- Modal para cadastro de funcionário -->
    <div class="modal fade" id="cadastroFuncionarioModal" tabindex="-1" aria-labelledby="cadastroFuncionarioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cadastroFuncionarioModalLabel">Cadastrar Funcionário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <!-- Formulário de cadastro de funcionário -->
                    <form action="admin_funcionario.php" method="POST">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="cargo" class="form-label">Cargo</label>
                            <input type="text" class="form-control" id="cargo" name="cargo" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="telefone" name="telefone" required>
                        </div>
                        <div class="mb-3">
                            <label for="salario" class="form-label">Salário</label>
                            <input type="number" step="0.01" class="form-control" id="salario" name="salario" required>
                        </div>
                        <div class="mb-3">
                            <label for="data_admissao" class="form-label">Data de Admissão</label>
                            <!-- Preenchendo com a data atual -->
                            <input type="date" class="form-control" id="data_admissao" name="data_admissao" value="<?php echo $data_atual; ?>" required>
                        </div>
                        <button type="submit" class="btn btn-success">Cadastrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <!-- Listagem de funcionários -->
    <h3>Funcionários Cadastrados</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Cargo</th>
                <th>Telefone</th>
                <th>Salário</th>
                <th>Data de Admissão</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Exibindo os funcionários cadastrados
            $result = $conn->query("SELECT * FROM funcionario");
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['nome']}</td>";
                echo "<td>{$row['cargo']}</td>";
                echo "<td>{$row['telefone']}</td>";
                echo "<td>R$ " . number_format($row['salario'], 2, ',', '.') . "</td>";
                echo "<td>{$row['data_admissao']}</td>";
                echo "<td>
                        <a href='#' class='btn btn-warning' data-bs-toggle='modal' data-bs-target='#editarFuncionarioModal' data-id='{$row['ID_funcionario']}' data-nome='{$row['nome']}' data-cargo='{$row['cargo']}' data-telefone='{$row['telefone']}' data-salario='{$row['salario']}' data-data_admissao='{$row['data_admissao']}'>Editar</a>
                        <a href='admin_funcionario.php?delete={$row['ID_funcionario']}' class='btn btn-danger' onclick='return confirm(\"Tem certeza que deseja excluir este funcionário?\")'>Excluir</a>
                    </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Modal de edição de funcionário -->
<div class="modal fade" id="editarFuncionarioModal" tabindex="-1" aria-labelledby="editarFuncionarioModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarFuncionarioModalLabel">Editar Funcionário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <!-- Formulário de edição de funcionário -->
                <form action="admin_funcionario.php" method="POST">
                    <input type="hidden" id="edit_ID_funcionario" name="ID_funcionario">
                    <div class="mb-3">
                        <label for="edit_nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="edit_nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_cargo" class="form-label">Cargo</label>
                        <input type="text" class="form-control" id="edit_cargo" name="cargo" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_telefone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="edit_telefone" name="telefone" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_salario" class="form-label">Salário</label>
                        <input type="number" step="0.01" class="form-control" id="edit_salario" name="salario" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_data_admissao" class="form-label">Data de Admissão</label>
                        <input type="date" class="form-control" id="edit_data_admissao" name="data_admissao" value="<?php echo $data_atual; ?>" required>
                    </div>
                    <button type="submit" class="btn btn-success">Salvar Alterações</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Preenche o formulário de edição com os dados do funcionário
    var editarFuncionarioModal = document.getElementById('editarFuncionarioModal');
    editarFuncionarioModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var idFuncionario = button.getAttribute('data-id');
        var nomeFuncionario = button.getAttribute('data-nome');
        var cargoFuncionario = button.getAttribute('data-cargo');
        var telefoneFuncionario = button.getAttribute('data-telefone');
        var salarioFuncionario = button.getAttribute('data-salario');
        var dataAdmissaoFuncionario = button.getAttribute('data-data_admissao');
        
        var modalTitle = editarFuncionarioModal.querySelector('.modal-title');
        modalTitle.textContent = 'Editar Funcionário: ' + nomeFuncionario;

        var inputIdFuncionario = editarFuncionarioModal.querySelector('#edit_ID_funcionario');
        var inputNomeFuncionario = editarFuncionarioModal.querySelector('#edit_nome');
        var inputCargoFuncionario = editarFuncionarioModal.querySelector('#edit_cargo');
        var inputTelefoneFuncionario = editarFuncionarioModal.querySelector('#edit_telefone');
        var inputSalarioFuncionario = editarFuncionarioModal.querySelector('#edit_salario');
        var inputDataAdmissaoFuncionario = editarFuncionarioModal.querySelector('#edit_data_admissao');

        inputIdFuncionario.value = idFuncionario;
        inputNomeFuncionario.value = nomeFuncionario;
        inputCargoFuncionario.value = cargoFuncionario;
        inputTelefoneFuncionario.value = telefoneFuncionario;
        inputSalarioFuncionario.value = salarioFuncionario;
        inputDataAdmissaoFuncionario.value = dataAdmissaoFuncionario;
    });
</script>

</body>
</html>
