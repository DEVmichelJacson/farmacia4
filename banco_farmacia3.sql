DROP DATABASE IF EXISTS farmacia3;
CREATE DATABASE farmacia3 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE farmacia3;

CREATE TABLE usuario (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome  VARCHAR(150) NOT NULL,
  login VARCHAR(100) NOT NULL UNIQUE,
  senha VARCHAR(100) NOT NULL
);

CREATE TABLE categoria (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL
);

CREATE TABLE produto (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(200) NOT NULL,
  id_categoria INT NOT NULL,
  data_validade DATE,
  estoque_atual INT DEFAULT 0,
  preco_custo DECIMAL(10,2) DEFAULT 0.00,
  preco_venda DECIMAL(10,2) DEFAULT 0.00,
  margem_min DECIMAL(5,2) DEFAULT 0.15,
  imposto DECIMAL(5,2) DEFAULT 0.18,
  codigo_barras VARCHAR(50) NULL,
  CONSTRAINT fk_prod_cat3 FOREIGN KEY (id_categoria) REFERENCES categoria(id)
) ENGINE=InnoDB;

CREATE TABLE venda (
  id INT AUTO_INCREMENT PRIMARY KEY,
  data_venda DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE venda_item (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_venda INT NOT NULL,
  id_produto INT NOT NULL,
  quantidade INT NOT NULL,
  preco_unitario DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_vi_venda3 FOREIGN KEY (id_venda) REFERENCES venda(id),
  CONSTRAINT fk_vi_prod3 FOREIGN KEY (id_produto) REFERENCES produto(id)
) ENGINE=InnoDB;

CREATE TABLE historico_preco (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_produto INT NOT NULL,
  preco_antigo DECIMAL(10,2) NOT NULL,
  preco_novo   DECIMAL(10,2) NOT NULL,
  data_alteracao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_hist_prod3 FOREIGN KEY (id_produto) REFERENCES produto(id)
) ENGINE=InnoDB;

INSERT INTO usuario (nome, login, senha) VALUES
('Nicolas Cristyan', 'Nicolas Cristyan', '21deAgost@');

INSERT INTO categoria (nome) VALUES
('Perfumaria'),
('Medicamentos'),
('Higiene Pessoal'),
('Vitaminas');

INSERT INTO produto (nome,id_categoria,data_validade,estoque_atual,preco_custo,preco_venda,margem_min,imposto,codigo_barras) VALUES
('Shampoo Dove 400ml',1,'2025-11-15',80,12,19.90,0.15,0.18,'7891234560011'),
('Condicionador Dove 400ml',1,'2025-11-20',65,12.50,20.90,0.15,0.18,'7891234560012'),
('Perfume Floratta 100ml',1,'2025-12-30',20,75.00,129.00,0.20,0.18,'7891234560013'),
('Hidratante Nivea Soft 200ml',1,'2025-10-10',150,10.00,17.90,0.15,0.18,'7891234560014'),
('Paracetamol 500mg 20cp',2,'2025-06-10',120,3.00,8.00,0.15,0.12,'7891234560101'),
('Dipirona 1g 10cp',2,'2025-05-25',200,2.50,7.50,0.15,0.12,'7891234560102'),
('Ibuprofeno 400mg 20cp',2,'2025-06-30',150,3.50,9.90,0.15,0.12,'7891234560104'),
('Sabonete Nivea 90g',3,'2025-09-01',300,2.50,5.90,0.15,0.18,'7891234560201'),
('Desodorante Rexona',3,'2025-07-20',220,6.00,12.90,0.15,0.18,'7891234560202'),
('Vitamina C 1g',4,'2025-04-20',60,15.00,29.90,0.20,0.18,'7891234560301'),
('Multivitamínico A-Z 60cp',4,'2025-08-15',35,18.00,39.90,0.20,0.18,'7891234560302'),
('Ômega 3 1000mg 120cáps',4,'2026-03-10',25,22.00,49.90,0.20,0.18,'7891234560303');

DELIMITER $$
CREATE TRIGGER tr_baixa_estoque3
AFTER INSERT ON venda_item
FOR EACH ROW
BEGIN
  UPDATE produto
     SET estoque_atual = GREATEST(estoque_atual - NEW.quantidade, 0)
   WHERE id = NEW.id_produto;
END$$

CREATE TRIGGER tr_log_alteracao_preco3
AFTER UPDATE ON produto
FOR EACH ROW
BEGIN
  IF OLD.preco_venda <> NEW.preco_venda THEN
    INSERT INTO historico_preco (id_produto, preco_antigo, preco_novo)
    VALUES (NEW.id, OLD.preco_venda, NEW.preco_venda);
  END IF;
END$$
DELIMITER ;

CREATE OR REPLACE VIEW vw_promocoes AS
SELECT
  p.id,
  p.nome AS nome_produto,
  c.id   AS id_categoria,
  c.nome AS categoria_nome,
  p.estoque_atual,
  p.data_validade,
  DATEDIFF(p.data_validade, CURDATE()) AS dias_para_vencer,
  p.preco_venda,
  p.preco_custo,
  p.imposto,
  p.margem_min,
  (p.preco_custo * (1 + p.imposto) * (1 + p.margem_min)) AS preco_piso,
  LEAST(GREATEST((30 - DATEDIFF(p.data_validade, CURDATE())) / 30, 0), 1) AS expiry_score,
  LEAST(p.estoque_atual / 200, 1) AS overstock_score,
  CASE
    WHEN DATEDIFF(p.data_validade, CURDATE()) < 0 THEN 0
    ELSE
      0.6 * LEAST(GREATEST((30 - DATEDIFF(p.data_validade, CURDATE())) / 30, 0), 1)
    + 0.4 * LEAST(p.estoque_atual / 200, 1)
  END AS prioridade,
  CASE
    WHEN DATEDIFF(p.data_validade, CURDATE()) < 0 THEN 0
    ELSE LEAST(
      5
      + 25 * LEAST(GREATEST((30 - DATEDIFF(p.data_validade, CURDATE())) / 30, 0), 1)
      + 15 * LEAST(p.estoque_atual / 200, 1)
    , 50)
  END AS desconto_percentual,
  CASE
    WHEN DATEDIFF(p.data_validade, CURDATE()) < 0 THEN p.preco_venda
    ELSE GREATEST(
      (p.preco_custo * (1 + p.imposto) * (1 + p.margem_min)),
      (p.preco_venda * (1 - (
        LEAST(
          5
          + 25 * LEAST(GREATEST((30 - DATEDIFF(p.data_validade, CURDATE())) / 30, 0), 1)
          + 15 * LEAST(p.estoque_atual / 200, 1)
        , 50) / 100
      )))
    )
  END AS preco_sugerido
FROM produto p
JOIN categoria c ON c.id = p.id_categoria;
