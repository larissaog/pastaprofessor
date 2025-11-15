<?php

// inicio - conexão com banco e verificação do ID

require __DIR__. '/includes/db.php';

// verifica se veio o ID pela URL (GET)
$id= (int)($_GET['id'] ?? '');

// se ID for inválido (zero ou vazio), redireciona para lista
if ($id <=0){
    header('Location: index.php');
    exit;
}
// fim - Conexão com o banco e verificação do ID

// inicio - Busca do registro para excluir 

$sql= 'SELECT * FROM cadastro WHERE id= :id';
$stmt = db()->prepare($sql);
$stmt->execute([':id'=> $id]);
$registro = $stmt -> fetch (PDO::FETCH_ASSOC);

// Se não encontrar nada. volte para a lista

if (!$registro){
    header('Location: index.php');
    exit;
}
// fim busca do registro

try{
    if(!empty($registro['foto']) && file_exists(__DIR__. '/'. $registro['foto'])){
        unlink(__DIR__.'/'. $registro['foto']);
    }

    // comando SQL para excluir o registro
    $sql= 'DELETE  FROM cadastro WHERE id= :id';
    $stmt = db()->prepare($sql);
    $stmt->execute([':id'=> $id]);

    // redireciona de volta para index após excluir
    header('Location: index.php?msg=excluido');
    exit;
} 
catch (PDOException $e){
    // se der erro no banco, mostrar mensagem]
    echo '<p style="color:red;"> Erro ao Excluir:'. htmlspecialchars($e->getMessage()). '</p>';
}

// if(!empty($registro['foto'])-> verifica se o campo foto no banco não esta vazio

// file_exists(__DIR__. '/'. $registro['foto'])-> confirma se o arquivo realmente existe na pasta do servidor antes de tentar apagar

// unlink -> é a função nativa do PhP que deleta um arquivo físico.