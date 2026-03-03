<?php
include 'conexao.php';
proteger();

// Filtros
$categoriaId = (int)($_GET['categoria'] ?? 0);
$termoBusca = trim($_GET['busca'] ?? '');

// Buscar categorias
$listaCategorias = [];
if ($resultadoCategorias = $conn->query("SELECT id, nome FROM categoria ORDER BY nome")) {
    $listaCategorias = $resultadoCategorias->fetch_all(MYSQLI_ASSOC);
}

// Montar SQL principal
$sql = "
    SELECT p.*, c.nome AS categoria_nome 
    FROM produto p 
    JOIN categoria c ON c.id = p.id_categoria 
    WHERE 1=1
";

if ($categoriaId > 0) {
    $sql .= " AND p.id_categoria = $categoriaId";
}

if ($termoBusca !== '') {
    $buscaEscapada = $conn->real_escape_string($termoBusca);
    $sql .= " AND p.nome LIKE '%$buscaEscapada%'";
}

$sql .= " ORDER BY c.nome, p.nome";
$resultadoProdutos = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Produtos • Drogaria São Pedro</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="topo">
    <div class="topo-left">
        <h1>Produtos 📦</h1>
    </div>
    <div class="topo-right">
        <a href="index.php" class="btn">⬅ Painel</a>
        <a href="produto_novo.php" class="btn primary">➕ Novo</a>
    </div>
</header>

<main class="container">
    <form method="GET" class="toolbar toolbar-wrap">
        <div class="toolbar-item">
            <label class="muted">Categoria</label>
            <select name="categoria" class="input">
                <option value="0">Todas</option>
                <?php foreach ($listaCategorias as $cat): ?>
                    <option 
                        value="<?php echo $cat['id'] ?>" 
                        <?php echo $categoriaId == $cat['id'] ? 'selected' : '' ?>>
                        <?php echo htmlspecialchars($cat['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="toolbar-item flex-grow">
            <label class="muted">Busca</label>
            <input 
                name="busca" 
                class="input" 
                placeholder="Digite o nome do produto..." 
                value="<?php echo htmlspecialchars($termoBusca) ?>"
            >
        </div>

        <div class="toolbar-item">
            <button class="btn primary">Filtrar</button>
        </div>
    </form>

    <div class="tabela-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Categoria</th>
                    <th>Validade</th>
                    <th>Estoque</th>
                    <th>Preço custo</th>
                    <th>Preço venda</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultadoProdutos && $resultadoProdutos->num_rows > 0): ?>
                    <?php while ($produto = $resultadoProdutos->fetch_assoc()): 
                        $dataValidade = $produto['data_validade'] 
                            ? date('d/m/Y', strtotime($produto['data_validade'])) 
                            : '-';
                        $precoCusto = 'R$ ' . number_format((float)$produto['preco_custo'], 2, ',', '.');
                        $precoVenda = 'R$ ' . number_format((float)$produto['preco_venda'], 2, ',', '.');
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($produto['nome']) ?></td>
                            <td><?php echo htmlspecialchars($produto['categoria_nome']) ?></td>
                            <td><?php echo $dataValidade ?></td>
                            <td><?php echo (int)$produto['estoque_atual'] ?></td>
                            <td><?php echo $precoCusto ?></td>
                            <td><?php echo $precoVenda ?></td>
                            <td>
                                <a href="produto_editar.php?id=<?php echo $produto['id'] ?>">Editar</a> |
                                <a href="produto_excluir.php?id=<?php echo $produto['id'] ?>" onclick="return confirm('Confirma excluir?');">Excluir</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">Nenhum produto encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

</body>
</html>
