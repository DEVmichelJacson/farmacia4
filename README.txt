
Painel de Promoções — Farmácia

Descrição curta
Projeto simples para identificar produtos próximos da data de validade e sugerir promoções.

Credenciais de teste
- Usuário: teste     ***********************************
- Senha: 1234        ***********************************

Como abrir localmente (rápido)
1) Inicie Apache e MySQL no XAMPP.
2) Coloque a pasta do projeto em `C:\xampp\htdocs\farmacia_projeto 2\farmacia4`.
3) Importe o banco (phpMyAdmin ou linha de comando):

```bash
mysql -u root -p
CREATE DATABASE farmacia;
exit
mysql -u root -p farmacia < "C:\xampp\htdocs\farmacia_projeto 2\farmacia4\banco_farmacia3.sql"
```

4) Ajuste `conexao.php` se necessário (host/usuário/senha/db).
5) Abra no navegador:

- http://localhost/farmacia_projeto%202/farmacia4/index.php
- http://localhost/farmacia_projeto%202/farmacia4/produtos_listar.php

Notas rápidas
- Os dados são uma exportação real: remova ou anonimize antes de publicar.
- A página de importação CSV está em `import_form.php` / `importar.php`.

Stack
- PHP (recomendado 7.4+)
- MySQL / MariaDB
- Apache (XAMPP)
- HTML, CSS, JavaScript
- Scripts PHP para importação CSV


