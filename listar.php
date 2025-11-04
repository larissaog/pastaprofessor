<?php
// Conectando com o banco de dados
require __DIR__ . '/includes/db.php'; 

// Inicializando a busca


// $_GET pega o valor digitado no campo de busca (se existir)
// trim () remove espaços antes e depois do texto
$busca = trim($_GET['busca'] ?? ''); 
// GET é usado aq pq estamos apenas consultando dados, não salvando nd
// ?? Ele serve para verificar se uma variável está definida e não é null, e caso não esteja, usar um valor padrão.


// Montando SQL
if ($busca !== '') {
    // Se tiver texto na busca, filtra por nome ou e-mail
    $sql = 'SELECT id, nome, email, telefone, foto, data_cadastro
            FROM cadastro
            WHERE nome LIKE :busca OR email LIKE :busca
            ORDER BY id DESC'; //ordena pelos IDs do maior pro menor (cadastros mais novos primeiro)
    $stmt = db()->prepare($sql); //prepara o comando SQL 
    // executa substituindo o placeholder :busca
    // o % antes e depois permite buscar qualquer parte do nome/email
    $stmt->execute([':busca' => "%$busca%"]);
} else {
    // Se a busca estiver vazia, lista tudo
    $sql = 'SELECT id, nome, email, telefone, foto, data_cadastro
            FROM cadastro
            ORDER BY id DESC';  //ordernar por ordem decrecente
    $stmt = db()->prepare($sql);
    $stmt->execute();
}

// fetchAll () busca todos os resultados e retorna como array associativo
$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
    include __DIR__. '/header-listar.php';

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Cadastros</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
 
</head>
<body>
    <div class="pai">

    
    <h1 class="title">Lista de Cadastros</h1>

    <form method="get" class="header-table">
        <input type="text" name="busca" placeholder="Pesquise Info." value="<?= htmlspecialchars($busca) ?>" class="input">
        <button type="submit" class="button-table">Buscar</button>
        <a href="listar.php" class="clear">Limpar</a>
    </form>
        <!-- Link para cadastrar um novo registro -->

    <p><a href="formulario.php" class="new">+ Novo cadastro</a></p>

    <?php if (!$registros): ?>
          <!-- se não houver resultados -->
        <p>Nenhum cadastro encontrado.</p>
    <?php else: ?>
        <!-- inicio da tabela -->
        <table border="1" cellpadding="8" cellspacing="0" class="tabela">
            <thead>
                <tr class="topo">
                    <th id=tp-2>ID</th>
                    <th id=tp-2>Nome</th>
                    <th id=tp-2>E-mail</th>
                    <th id=tp-2>Telefone</th>
                    <th id=tp-2>Foto</th>
                    <th id=tp-2>Data de Cadastro</th>
                    <th id=tp-2>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($registros as $r): ?>
                     <!-- // foreach -> estrutura que percorre todos os registros do banco 
            // registros -> lista com todos os cadastros vindos do banco
            // $r-> representa UM registro por vez dentro do loop -->
                    <tr>
                        <td id="tp-1"><?= (int)$r['id'] ?>º</td>
                        <td id="tp-1"><?= htmlspecialchars($r['nome']) ?></td>
                        <td id="tp-1"><?= htmlspecialchars($r['email']) ?></td>
                        <td id="tp-1"><?= htmlspecialchars($r['telefone']) ?></td>
                         <!-- //  se tiver imagem, mostra miniatura -->
                        <td id="tp-1">
                            <?php if (!empty($r['foto'])): ?>
                                <img src="<?= htmlspecialchars($r['foto']) ?>" alt="foto" style="max-width:80px; max-height:80px;">
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <!-- Exibe data, se existir -->
                        <td id="tp-1"><?= htmlspecialchars($r['data_cadastro']) ?></td>
                        <!-- links de edição -->
                        <td id="tp-3">
                            <a href="editar.php?id=<?= (int)$r['id'] ?>" class="editores1">Editar</a> |
                            <a href="deletar.php?id=<?= (int)$r['id'] ?> " onclick="return confirm('Tem certeza que deseja excluir?')" class="editores">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</body>
</html>

<?php

// dir significa o diretorio que ele vai seguir
    include __DIR__. '/footer-listar.php';

?>

 