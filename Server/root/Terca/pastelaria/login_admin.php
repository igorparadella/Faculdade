<?php
// Definindo as credenciais de login
$usuario_correto = 'admin';  // Alterar conforme necessário
$senha_correta = 'admin'; // Alterar conforme necessário

// Verificando se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    // Verificando se as credenciais estão corretas
    if ($usuario === $usuario_correto && $senha === $senha_correta) {
        session_start();
        $_SESSION['logado'] = true;
        header('Location: admin.php'); // Redireciona para a página admin
        exit();
    } else {
        $erro = 'Usuário ou senha inválidos.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Admin Pastelaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h2 class="mt-5">Login Administrativo</h2>
    <?php if (isset($erro)) { echo "<div class='alert alert-danger'>$erro</div>"; } ?>
    <form method="POST" class="mt-3">
        <div class="mb-3">
            <label for="usuario" class="form-label">Usuário</label>
            <input type="text" class="form-control" id="usuario" name="usuario" required>
        </div>
        <div class="mb-3">
            <label for="senha" class="form-label">Senha</label>
            <input type="password" class="form-control" id="senha" name="senha" required>
        </div>
        <button type="submit" class="btn btn-primary">Entrar</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
