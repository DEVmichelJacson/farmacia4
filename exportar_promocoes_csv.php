<?php
include 'conexao.php';
proteger();

// --- Cabeçalhos do arquivo CSV ---
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="promocoes.csv"');

// Adiciona BOM para compatibilidade com Excel
echo "\xEF\xBB\xBF";

// --- Criação do arquivo de saída ---
$saida = fopen('php://output', 'w');

// Cabeçalho das colunas
$colunas = [
    'Produto',
    'Categoria',
    'Estoque',
    'Dias para vencer',
    'Preço venda',
    'Desconto (%)',
    'Preço sugerido',
    'Prioridade'
];

fputcsv($saida, $colunas, ';');

// --- Consulta SQL ---
$sql = "
    SELECT 
        nome_produto,
        categoria_nome,
        estoque_atual,
        dias_para_vencer,
        preco_venda,
        desconto_percentual,
        preco_sugerido,
        prioridade
    FROM vw_promocoes
    ORDER BY 
        (dias_para_vencer < 0) DESC, 
        prioridade DESC, 
        dias_para_vencer ASC
";

if ($resultado = $conn->query($sql)) {
    while ($promocao = $resultado->fetch_assoc()) {
        // Formatação dos valores numéricos
        $linha = [
            $promocao['nome_produto'],
            $promocao['categoria_nome'],
            $promocao['estoque_atual'],
            $promocao['dias_para_vencer'],
            number_format((float)$promocao['preco_venda'], 2, ',', '.'),
            number_format((float)$promocao['desconto_percentual'], 0, ',', '.'),
            number_format((float)$promocao['preco_sugerido'], 2, ',', '.'),
            number_format((float)$promocao['prioridade'], 2, ',', '.')
        ];

        fputcsv($saida, $linha, ';');
    }
}

// --- Finaliza ---
fclose($saida);
exit;
