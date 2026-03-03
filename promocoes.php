<?php
include'conexao.php';
proteger();

$categoria = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

$cats = [];
$resCats = $conn->query("SELECT id, nome FROM categoria ORDER BY nome");
if ($resCats) while($r=$resCats->fetch_assoc()) $cats[]=$r;

$sql = "SELECT * FROM vw_promocoes WHERE 1=1";
if ($categoria>0) $sql .= " AND id_categoria=".$categoria;
if ($busca!=='') { $b=$conn->real_escape_string($busca); $sql .= " AND nome_produto LIKE '%$b%'"; }
$sql .= " ORDER BY (dias_para_vencer < 0) DESC, prioridade DESC, dias_para_vencer ASC";
$res = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Promoções • Drogaria São Pedro</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="topo">
  <div class="topo-left"><h1>Promoções 💸</h1><p>Prioridade por validade e estoque.</p></div>
  <div class="topo-right"><a href="index.php" class="btn">⬅ Painel</a></div>
</header>
<main class="container">
  <form method="GET" class="toolbar toolbar-wrap">
    <div class="toolbar-item">
      <label class="muted">Categoria</label>
      <select name="categoria" class="input">
        <option value="0">Todas</option>
        <?php foreach($cats as $c): ?>
          <option value="<?php echo $c['id'] ?>" <?php echo $categoria==$c['id']?'selected':'' ?>><?php echo htmlspecialchars($c['nome']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="toolbar-item flex-grow">
      <label class="muted">Busca</label>
      <input name="busca" class="input" value="<?php echo htmlspecialchars($busca) ?>">
    </div>
    <div class="toolbar-item">
      <button class="btn primary">Filtrar</button>
    </div>
  </form>
  <div class="toolbar">
    <a class="btn" href="exportar_promocoes_csv.php">⬇ Exportar CSV</a>
  </div>
  <div class="tabela-wrapper">
    <table>
      <thead><tr>
        <th>Produto</th><th>Categoria</th><th>Estoque</th><th>Dias p/ vencer</th>
        <th>Preço venda</th><th>Desconto</th><th>Preço sugerido</th><th>Prioridade</th>
      </tr></thead>
      <tbody>
      <?php if ($res && $res->num_rows>0): while($row=$res->fetch_assoc()):
        $dias = (int)$row['dias_para_vencer'];
        $dias_txt = $dias < 0 ? "Vencido há ".abs($dias)." dia(s)" : $dias." dia(s)";
        $prior = (float)$row['prioridade'];
        $prior_txt = number_format($prior,2,',','.');
        $badge_class = $prior>=0.7?'badge alta':($prior>=0.4?'badge media':'badge baixa');
        $badge_label = $prior>=0.7?'Alta':($prior>=0.4?'Média':'Baixa');
        $preco_venda = "R$ ".number_format((float)$row['preco_venda'],2,',','.');
        $preco_sug = "R$ ".number_format((float)$row['preco_sugerido'],2,',','.');
        $desc = number_format((float)$row['desconto_percentual'],0,',','.');
      ?>
      <tr>
        <td><?php echo htmlspecialchars($row['nome_produto']) ?></td>
        <td><?php echo htmlspecialchars($row['categoria_nome']) ?></td>
        <td><?php echo (int)$row['estoque_atual'] ?></td>
        <td><?php echo $dias_txt ?></td>
        <td><?php echo $preco_venda ?></td>
        <td><?php echo $desc ?>%</td>
        <td><strong class="txt-azul"><?php echo $preco_sug ?></strong></td>
        <td><span class="<?php echo $badge_class ?>"><?php echo $badge_label ?></span> <small class="muted">(<?php echo $prior_txt ?>)</small></td>
      </tr>
      <?php endwhile; else: ?>
        <tr><td colspan="8">Nenhuma promoção encontrada.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
</body>
</html>
