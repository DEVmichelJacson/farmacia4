<?php
include 'conexao.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Login • Drogaria São Pedro</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="login-bg">
<div class="login-card">
<img src="img/image.jpg" class="login-logo" alt="Logo">
<h2>Drogaria São Pedro</h2>
<p class="login-subtitle">Acesse o sistema de promoções</p>
<form action="autenticar.php" method="POST">
<label>Usuário</label>
<input type="text" name="login" required>
<label>Senha</label>
<input type="password" name="senha" required>
<button class="btn-login">Entrar</button>
</form>
</div>
</body>