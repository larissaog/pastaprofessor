<?php

// inicio

require __DIR__ . '/includes/db.php';
// pega o ID que veio pela URL 
// se não existir ou for inválido (0, texto, etc), volta para pagina de listragem

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0){
    header('Location: listar.php');
    exit;
}
// fim conexão e captura do ID

// inicio-busca do registro (para preencher o formulario)

$sql= 'SELECT id, nome, email, telefone, foto, data_cadastro
        FROM cadastro
        WHERE id = :id';
$stmt = db()->prepare($sql);
$stmt->execute([':id' => $id]);
$registro = $stmt->fetch(PDO::FETCH_ASSOC);
// Se não encontrou o registro, volta para a lista

if (!$registro){
    header('Location: listar.php');
    exit;
}

// guarda a foto atual do registro (vinda do banco)
// senão enviar uma foto no formulario,
//essa aqui continua sendo usada (para não apagar a existente)
$fotoAtual= $registro['foto'] ?? null;
// fim- busca registro

// inicio - processamento do POST (quando clicar em salvar)
$erro='';
$ok= false;

// inicializa as variáveis para evitar warnings
$nome = '';
$email = '';
$telefone = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    // captura dos dados
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $fotoAtual = $_POST['foto_atual'] ?? null;

    // validações basicas
    if ($nome === '' || mb_strlen($nome) <3){
        $erro= 'Nome é obrigatorio(mín. 3 caracteres)';
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $erro= 'E-mail inválido.';
    }
    elseif ($telefone==='' || mb_strlen(preg_replace('/\D+/', '', $telefone)) <8){
        $erro = 'Telefone inválido.';
    }

    // Upload da nova foto
    $novaFoto=null; //se não tiver, vamos manter a foto atual
    if ($erro === '' && isset($_FILES['foto']) && $_FILES['foto']['error']!== UPLOAD_ERR_NO_FILE){
        if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK){
            $erro= 'erro ao enviar a imagem';
        }
        else{
            if ($_FILES['foto']['size'] > 2*1024*1024){
                $erro = 'imagem muito grande (máx.2 MB)';
            }
            // valida tipo real 
            if ($erro === ''){
                $finfo = new finfo(FILEINFO_MIME_TYPE); //classe nativa para detectar MIME
                $mime = $finfo->file($_FILES['foto']['tmp_name']); //tipo real do arquivo
                $permitidos=[
                    'image/jpeg' => 'jpg',
                    'image/png' =>  'png',
                    'image/gif' =>  'gif'
                ];
                if (!isset($permitidos[$mime])){
                    $erro='foto de imagem inválida. Use jpg, png, gif';
                }
                if ($erro === ''){
                    $dirUpload = __DIR__ . '/uploads';
                    if (!is_dir($dirUpload)){
                        mkdir($dirUpload, 0755, true);
                    }
                    $novoNome = uniqid('img', true). '.' . $permitidos[$mime]; //nome único
                    $destino = $dirUpload . '/' . $novoNome;
                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)){
                        $novaFoto = 'uploads/' . $novoNome; //salva caminho relativo
                    } else{
                        $erro='falha ao salvar a image, no servidor.';
                    }
                }
            }
        }
    }

    // se tiver tudo ok, faz update
    if ($erro === ''){
        try {
            // define qual foto será salva: nova (se enviada) ou mantem atual
            $fotoParaSalvar= $novaFoto !== null ? $novaFoto : $fotoAtual;

            $sql = 'UPDATE cadastro
                    SET nome= :nome,
                        email = :email,
                        telefone = :telefone,
                        foto= :foto
                    WHERE id= :id';
            $stmt = db()->prepare($sql);
            $stmt->execute([
                ':nome'=> $nome,
                ':email'=>$email,
                ':telefone'=>$telefone,
                ':foto'=>$fotoParaSalvar,
                ':id'=> $id,
            ]);
            // se trocou a foto, apaga a antiga do disco (se existir)
            if ($novaFoto != null && !empty($fotoAtual) && file_exists(__DIR__ . '/'. $fotoAtual)){
                unlink(__DIR__. '/'. $fotoAtual);
            }
            $ok = true;
            // redireciona para a lista após atualizar (fluxo que você quer)
            header('Location: listar.php?msg=atualizado');
            exit;
        }
        catch (PDOException $e){
            if ($e->getCode()=== '23000'){
                $erro='Este e-mail já está cadastrado';
            }
            else {
                $erro= 'erro ao atualizar: '. $e->getMessage();
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>editar cadastro</title>
</head>
<body>

<h1>Editar Cadastro</h1>

<?php if ($erro): ?>
    <p style="color:red;"> <?= htmlspecialchars($erro) ?></p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <p>
        <label>Nome:<br>
        <input type="text" name="nome" require minlength="3"
                value="<?= htmlspecialchars($registro['nome']?? '')?>">
        </label>
    </p>

    <p>
        <label>Email<br>
        <input type="email" name="email" required
                value="<?= htmlspecialchars($registro['email']?? '') ?>">
    
    </label>
    </p>

    <p>
        <label>Telefone:<br>
        <input type="text" name="telefone" required
                placeholder="11 91234-5493"
                value="<?= htmlspecialchars($registro['telefone']??'')?>">
        
    </label>
    </p>

    <p>
        foto atual:
        <?php if (!empty($fotoAtual)): ?>
            <br>
            <img src="<?= htmlspecialchars($fotoAtual) ?>" alt="foto atual" style="max-width:120px; max-height:120px;">
            <? php else: ?>
                (sem foto)
          <?php endif; ?>
    </p>
    <p>
        <label>Trocar de foto (opcional): <br>
        <input type="file" name=foto>
</label>
    </p>

    <!-- mantem o caminho da foto atual escondido (caso não troque) -->
     <input type="hidden" name="foto_atual" value="<?=htmlspecialchars($fotoAtual ?? '') ?>">
     <p>
        <button type="submit">Salvar alteracoes</button>
        <a href="listar.php">Cancelar</a>
     </p>
</form>


</body>
</html>
