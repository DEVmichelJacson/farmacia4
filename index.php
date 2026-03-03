<?php
include 'conexao.php';
proteger();

// ==========================================================
// 🧮 Estatísticas gerais
// ==========================================================

$totalProdutos = 0;
$totalValidos = 0;
$totalVencidos = 0;
$totalAltaPrioridade = 0;

// ----------------------------------------------------------
// Total de produtos
// ----------------------------------------------------------
$sqlTotal = "SELECT COUNT(*) AS qtd FROM produto";
$resultTotal = $conn->query($sqlTotal);

if ($resultTotal && $resultTotal->num_rows > 0) {
    $linhaTotal = $resultTotal->fetch_assoc();
    $totalProdutos = (int) $linhaTotal['qtd'];
}

// ----------------------------------------------------------
// Produtos válidos e vencidos
// ----------------------------------------------------------
$sqlValidade = "
    SELECT
        SUM(DATEDIFF(data_validade, CURDATE()) >= 0) AS validos,
        SUM(DATEDIFF(data_validade, CURDATE()) < 0) AS vencidos
    FROM produto
";
$resultValidade = $conn->query($sqlValidade);

if ($resultValidade && $resultValidade->num_rows > 0) {
    $linhaVal = $resultValidade->fetch_assoc();
    $totalValidos  = (int) $linhaVal['validos'];
    $totalVencidos = (int) $linhaVal['vencidos'];
}

// ----------------------------------------------------------
// Promoções de alta prioridade
// ----------------------------------------------------------
$sqlAlta = "
    SELECT COUNT(*) AS qtd
    FROM vw_promocoes
    WHERE prioridade >= 0.7
      AND dias_para_vencer >= 0
";
$resultAlta = $conn->query($sqlAlta);

if ($resultAlta && $resultAlta->num_rows > 0) {
    $linhaAlta = $resultAlta->fetch_assoc();
    $totalAltaPrioridade = (int) $linhaAlta['qtd'];
}

// ----------------------------------------------------------
// Usuário logado
// ----------------------------------------------------------
$usuarioNome = "";
if (isset($_SESSION['usuario_nome'])) {
    $usuarioNome = $_SESSION['usuario_nome'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel • Drogaria São Pedro</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- ===================================================== -->
    <!-- Cabeçalho -->
    <!-- ===================================================== -->
    <header class="topo">
        <div class="topo-esquerda">
            <h1>Drogaria São Pedro</h1>
            <p>Sistema de Promoções e Estoque</p>
        </div>

        <div class="topo-direita">
            <span class="muted">Olá, <?php echo htmlspecialchars($usuarioNome); ?></span>
            <a class="btn" href="logout.php">Sair</a>
        </div>
    </header>

    <!-- ===================================================== -->
    <!-- Conteúdo principal -->
    <!-- ===================================================== -->
    <main class="container">

        <h2>Painel geral</h2>

        <!-- Cards principais -->
        <div class="cards">
            <div class="card">
                <div class="card-titulo">📦 Produtos</div>
                <div class="card-numero"><?php echo $totalProdutos; ?></div>
            </div>

            <div class="card">
                <div class="card-titulo">✅ Válidos</div>
                <div class="card-numero"><?php echo $totalValidos; ?></div>
            </div>

            <div class="card">
                <div class="card-titulo">⚠️ Vencidos</div>
                <div class="card-numero"><?php echo $totalVencidos; ?></div>
            </div>

            <div class="card">
                <div class="card-titulo">🔥 Prioridade alta</div>
                <div class="card-numero"><?php echo $totalAltaPrioridade; ?></div>
            </div>
        </div>

        <!-- Atalhos -->
        <h3>Atalhos</h3>
        <div class="cards">
            <a class="card" href="promocoes.php">
                <div class="card-titulo">🔎 Ver promoções</div>
            </a>

            <a class="card" href="produtos_listar.php">
                <div class="card-titulo">📋 Gerenciar produtos</div>
            </a>

            <a class="card" href="produto_novo.php">
                <div class="card-titulo">➕ Novo produto</div>
            </a>

            <a class="card" href="exportar_promocoes_csv.php">
                <div class="card-titulo">⬇ Exportar promoções (CSV)</div>
            </a>
        </div>

    </main>

    <!-- ===================================================== -->
    <!-- Rodapé -->
    <!-- ===================================================== -->
    <footer class="rodape">
        <small>Feito por: Drogaria São Pedro</small>
    </footer>

</body>
</html>
