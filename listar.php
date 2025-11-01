<?php
// conectando com o banco de dados
require __DIR__ . '/includes/db.php'; 

// inicializando

// $_GET pega o valor digitado no campo de busca (se existir)
// trim () remove espaços antes e depois do texto
$busca= trim($_GET['busca'] ?? '');
// GET é usado aq pq estamos apenas consultando dados, não salvando nd
// ?? Ele serve para verificar se uma variável está definida e não é null, e caso não esteja, usar um valor padrão.

// verificar se o usuario digitou algo
if ($busca !== ''){
    // se tiver texto na busca, o SQL filtra pelo nome ou e-mail
    $sql = 'SELECT id, nome, email, telefone, foto, data_cadastro
            FROM cadastros
            WHERE nome LIKE :busca OR email LIKE :busca
            ORDER BY id DESC'; //ordena pelos IDs do maior pro menor (cadastros mais novos primeiro)
    $stmt = db()->prepare($sql); //prepara o comando SQL 
    // executa substituindo o placeholder :busca
    // o % antes e depois permite buscar qualquer parte do nome/email
    $stmt -> execute ([':buscs' => "%busca%"]);
}

else{
    // se o campo estiver vazio, lista tudo
    $sql= 'SELECT id, nome, email, telefone, foto, data_cadastro
           FROM cadastros
           ORDER BY id DESC';  //ordernar por ordem decrecente
    $stmt= db () -> prepare($sql);
    $stmt-> execute();
}

// fetchAll () busca todos os resultados e retorna como array associativo
$registros = $stmt -> fechAll(PDO::FECTCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>lista de cadastro</title>
</head>
<body>
    <h1>Lista de Cadastros</h1>
    <form method="get">
        <input type="text" name="busca" placeholder="pesquisa..." value="<?= htmlspecialchars($busca) ?>">
        <button type="submit">Buscar</button>

        <a href="listar.php">Limpar</a>
    </form>

    <!-- Link para cadastrar um novo registro -->
    <p><a href="formulario.php">+Novo cadastro</a></p>
    <?php if (!$registros): ?>
        <!-- se não houver resultados -->
         <p>Nenhum cadastro encontrado.</p>
    <?php else: ?>

        <!-- inicio da tabela -->
         <table border=1 cellpadding="8" callspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Telefone</th>
                    <th>Foto</th>
                    <th>Data de Cadastro</th>
                    <th>Ações</th>
                </tr>
            </thead>
         </table>

        <tbody>
            <?php
            // foreach -> estrutura que percorre todos os registros do banco 
            // registros -> lista com todos os cadastros vindos do banco
            // $r-> representa UM registro por vez dentro do loop
            foreach ($registros as $r):
             <tr>
             <td><?=(int) $r ['id'] ?></td>
             <td>htmlspecialchars ($r['nome'])?></td>
             <td>htmlspecialchars ($r['email'])?></td>
             <td>htmlspecialchars ($r['telefone'])?></td>
              //  se tiver imagem, mostra miniatura
            <td>
                <?php if (!empy($r['foto'])): ?>
                    <img src="<?= htmlspecialchars($r['foto'])?>" alt="foto" style="max-width:80px; max-height:80px;">
                    <?php else: ?>
                        -
                    <?php endif; ?>
                
            </td>
             </tr>

           
    
</body>
</html>