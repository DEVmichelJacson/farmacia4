<?php
require_once 'conexao.php';
proteger();

// Função utilitária para obter valores do POST com valor padrão
function post($chave, $padrao = '') {
    if (isset($_POST[$chave])) {
        return trim($_POST[$chave]);
    } else {
        return $padrao;
    }
}

// --- Captura de dados do formulário ---
$idProduto      = (int) post('id', 0);
$nomeProduto    = post('nome');
$idCategoria    = (int) post('id_categoria');
$dataValidade   = post('data_validade');
$estoqueAtual   = (int) post('estoque_atual', 0);
$precoCusto     = (float) post('preco_custo');
$precoVenda     = (float) post('preco_venda');
$margemMinima   = (float) post('margem_min', 0.15);
$imposto        = (float) post('imposto', 0.18);
$codigoBarras   = post('codigo_barras', null);

// --- Validação dos dados ---
$erros = [];

if ($nomeProduto === '')       $erros[] = 'Informe o nome do produto.';
if ($idCategoria <= 0)         $erros[] = 'Selecione uma categoria.';
if ($dataValidade === '')      $erros[] = 'Informe a data de validade.';
if ($estoqueAtual < 0)         $erros[] = 'Estoque não pode ser negativo.';
if ($precoCusto <= 0)          $erros[] = 'Preço de custo inválido.';
if ($precoVenda <= 0)          $erros[] = 'Preço de venda inválido.';

// Redirecionar se houver erros
$destinoErro = $idProduto > 0
    ? "produto_editar.php?id={$idProduto}"
    : "produto_novo.php";

if (!empty($erros)) {
    $mensagem = urlencode(implode(' ', $erros));
    header("Location: {$destinoErro}&erro={$mensagem}");
    exit;
}

// --- Query (INSERT ou UPDATE) ---
if ($idProduto > 0) {
    // Atualizar produto existente
    $sql = "
        UPDATE produto 
        SET nome = ?, 
            id_categoria = ?, 
            data_validade = ?, 
            estoque_atual = ?, 
            preco_custo = ?, 
            preco_venda = ?, 
            margem_min = ?, 
            imposto = ?, 
            codigo_barras = ?
        WHERE id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sisisddssi",
        $nomeProduto,
        $idCategoria,
        $dataValidade,
        $estoqueAtual,
        $precoCusto,
        $precoVenda,
        $margemMinima,
        $imposto,
        $codigoBarras,
        $idProduto
    );
} else {
    // Inserir novo produto
    $sql = "
        INSERT INTO produto (
            nome, id_categoria, data_validade, estoque_atual, 
            preco_custo, preco_venda, margem_min, imposto, codigo_barras
        ) VALUES (?,?,?,?,?,?,?,?,?)
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sisisddss",
        $nomeProduto,
        $idCategoria,
        $dataValidade,
        $estoqueAtual,
        $precoCusto,
        $precoVenda,
        $margemMinima,
        $imposto,
        $codigoBarras
    );
}

// --- Execução e redirecionamento ---
if ($stmt && $stmt->execute()) {
    header("Location: produtos_listar.php");
} else {
    $erro = urlencode("Erro ao salvar: " . $conn->error);
    header("Location: {$destinoErro}&erro={$erro}");
}

exit;
