<?php
$host = "localhost";
$db = "nome_do_banco";
$user = "root";
$pass = "usbw";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se o arquivo foi enviado
if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $nomeOriginal = $_FILES['foto']['name'];
    $temp = $_FILES['foto']['tmp_name'];
    $pastaDestino = "uploads/";
    $nomeSalvo = uniqid() . "-" . basename($nomeOriginal); // evita duplicidade
    $caminhoCompleto = $pastaDestino . $nomeSalvo;

    // Move o arquivo para a pasta uploads
    if (move_uploaded_file($temp, $caminhoCompleto)) {
        // Insere no banco
        $stmt = $conn->prepare("INSERT INTO imagens (nome_original, caminho) VALUES (?, ?)");
        $stmt->bind_param("ss", $nomeOriginal, $caminhoCompleto);
        $stmt->execute();
        echo "Upload bem-sucedido!";
    } else {
        echo "Erro ao mover o arquivo.";
    }
} else {
    echo "Nenhuma imagem foi enviada.";
}
?>
