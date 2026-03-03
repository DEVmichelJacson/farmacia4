<?php
include 'conexao.php';
proteger();

// Obtém o ID do produto
$produtoId = (int)($_GET['id'] ?? 0);
if ($produtoId <= 0) {
    header("Location: produtos_listar.php");
    exit;
}

// Buscar produto
$produto = $conn
    ->query("SELECT * FROM produto WHERE id = $produtoId")
    ->fetch_assoc();

if (!$produto) {
    header("Location: produtos_listar.php");
    exit;
}

// Buscar categorias
$resultadoCategorias = $conn->query("SELECT id, nome FROM categoria ORDER BY nome");
$listaCategorias = $resultadoCategorias ? $resultadoCategorias->fetch_all(MYSQLI_ASSOC) : [];

// Mensagem de erro (opcional)
$mensagemErro = $_GET['erro'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Produto</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="topo">
    <div class="topo-left">
        <h1>Editar produto</h1>
        <p><?php echo htmlspecialchars($produto['nome']) ?></p>
    </div>
    <div class="topo-right">
        <a href="produtos_listar.php" class="btn">⬅ Voltar</a>
    </div>
</header>

<main class="container">
    <?php if ($mensagemErro): ?>
        <div class="alert err"><?php echo htmlspecialchars($mensagemErro) ?></div>
    <?php endif; ?>

    <form class="form" action="produto_salvar.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $produto['id'] ?>">

        <label>Nome *</label>
        <input type="text" name="nome" required value="<?php echo htmlspecialchars($produto['nome']) ?>">

        <label>Categoria *</label>
        <select name="id_categoria" required>
            <option value="">-- selecione --</option>
            <?php foreach ($listaCategorias as $categoria): ?>
                <option value="<?php echo $categoria['id'] ?>" <?php echo $produto['id_categoria'] == $categoria['id'] ? 'selected' : '' ?>>
                    <?php echo htmlspecialchars($categoria['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Data de validade *</label>
        <input type="date" name="data_validade" required value="<?php echo $produto['data_validade'] ?>">

        <div class="grid2">
            <div>
                <label>Estoque *</label>
                <input type="number" name="estoque_atual" min="0" required value="<?php echo (int)$produto['estoque_atual'] ?>">
            </div>
            <div>
                <label>Código de barras</label>
                <input type="text" name="codigo_barras" value="<?php echo htmlspecialchars($produto['codigo_barras']) ?>">
            </div>
        </div>

        <div class="grid2">
            <div>
                <label>Preço custo *</label>
                <input type="number" name="preco_custo" min="0" step="0.01" required value="<?php echo (float)$produto['preco_custo'] ?>">
            </div>
            <div>
                <label>Preço venda *</label>
                <input type="number" name="preco_venda" min="0" step="0.01" required value="<?php echo (float)$produto['preco_venda'] ?>">
            </div>
        </div>

        <div class="grid2">
            <div>
                <label>Margem mínima *</label>
                <input type="number" name="margem_min" min="0" max="1" step="0.01" required value="<?php echo (float)$produto['margem_min'] ?>">
            </div>
            <div>
                <label>Imposto *</label>
                <input type="number" name="imposto" min="0" max="1" step="0.01" required value="<?php echo (float)$produto['imposto'] ?>">
            </div>
        </div>

        <div class="actions">
            <button class="btn primary">Salvar alterações</button>
        </div>
    </form>
</main>

</body>
</html>
