<?php
include 'conexao.php';
proteger();

// Pega o ID do produto a ser excluído
$idProduto = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Executa exclusão somente se o ID for válido
if ($idProduto > 0) {
    $stmt = $conn->prepare("DELETE FROM produto WHERE id = ?");
    $stmt->bind_param("i", $idProduto);
    $stmt->execute();
}

// Redireciona de volta para a lista de produtos
header("Location: produtos_listar.php");
exit;
