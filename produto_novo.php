<?php
include'conexao.php';
proteger();

// Carrega todas as categorias
$categorias = [];
$result = $conn->query("SELECT id, nome FROM categoria ORDER BY nome");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categorias[] = $row;
    }
}

// Mensagem de erro (se houver)
$erro = isset($_GET['erro']) ? $_GET['erro'] : '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Novo Produto</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="topo">
    <div class="topo-left">
        <h1>Novo Produto</h1>
    </div>
    <div class="topo-right">
        <a href="produtos_listar.php" class="btn">⬅ Voltar</a>
    </div>
</header>

<main class="container">

    <?php if ($erro): ?>
        <div class="alert err"><?php echo htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form class="form" action="produto_salvar.php" method="POST">
        <input type="hidden" name="id" value="0">

        <label>Nome *</label>
        <input type="text" name="nome" required>

        <label>Categoria *</label>
        <select name="id_categoria" required>
            <option value="">-- selecione --</option>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?php echo $cat['id'] ?>"><?php echo htmlspecialchars($cat['nome']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Data de validade *</label>
        <input type="date" name="data_validade" required>

        <div class="grid2">
            <div>
                <label>Estoque *</label>
                <input type="number" name="estoque_atual" min="0" value="0" required>
            </div>
            <div>
                <label>Código de barras</label>
                <input type="text" name="codigo_barras">
            </div>
        </div>

        <div class="grid2">
            <div>
                <label>Preço custo *</label>
                <input type="number" name="preco_custo" min="0" step="0.01" required>
            </div>
            <div>
                <label>Preço venda *</label>
                <input type="number" name="preco_venda" min="0" step="0.01" required>
            </div>
        </div>

        <div class="grid2">
            <div>
                <label>Margem mínima *</label>
                <input type="number" name="margem_min" min="0" max="1" step="0.01" value="0.15" required>
            </div>
            <div>
                <label>Imposto *</label>
                <input type="number" name="imposto" min="0" max="1" step="0.01" value="0.18" required>
            </div>
        </div>

        <div class="actions">
            <button class="btn primary">Salvar</button>
        </div>
    </form>
</main>
</body>
</html>
