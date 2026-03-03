<?php
include 'conexao.php';
session_start();

// Recebe os dados do formulário
$login = isset($_POST['login']) ? trim($_POST['login']) : '';
$senha  = isset($_POST['senha']) ? trim($_POST['senha']) : '';

// Validação básica
if ($login === '' || $senha === '') {
    header("Location: login.php?erro=" . urlencode("Informe usuário e senha."));
    exit;
}

// Consulta o usuário no banco
$sql  = "SELECT id, nome, login FROM usuario WHERE login = ? AND senha = ? LIMIT 1";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    header("Location: login.php?erro=" . urlencode("Erro interno."));
    exit;
}

$stmt->bind_param("ss", $login, $senha);
$stmt->execute();
$result = $stmt->get_result();

// Verifica se encontrou o usuário
if ($usuario = $result->fetch_assoc()) {
    $_SESSION['usuario_id']   = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    header("Location: index.php");
} else {
    header("Location: login.php?erro=" . urlencode("Usuário ou senha inválidos."));
}
exit;
