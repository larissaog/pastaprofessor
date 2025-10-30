<?php
// carrega a função db() para conectar no MySQL
require __DIR__ . '/includes/db.php';

// guardará mensagens de erro (se houver)
$erro = '';

// indica se salvou com sucesso
$ok = false;

// só processa se o método da requisição for POST (veio do formulario.html)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) captura e limpa os dados enviados 
     // e usa trim()para remover espaços extras no começo e no fim
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');

    // 2) Validação simples (evita dados incorretos antes de gravar no banco)
   // mb_strlen() função nativa do PHP que conta caracteres corretamente, incluindo acentos (ex: "José"=4)
    // verifica se o nome foi preenchido e tem pelo menos 3 caracteres
    if ($nome === '' || mb_strlen($nome) < 3) {
        $erro = 'Nome é obrigatório (mín. 3 caracteres).';
    }

    // verifica se o e-mail é válido
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail inválido.';
    }

    // verifica se o telefone foi preenchido e tem pelo menos 8 digitos 
// preg_replace() -> função nativa do PHP que substitui partes de texto usando expressões regulares
// aqui ela remove tudo que NÃO for número (/d+ significa "qualquer caractere não numerico)
// depois usamos mb_strlen() para contar quantos digitos sobraram

    elseif ($telefone === '' || mb_strlen(preg_replace('/\D+/', '', $telefone)) < 8) {
        $erro = 'Telefone inválido.';
    }


    // -----------------------------------------------------
    // upload da foto - só executa se não houver erro anterior
    // inicio do bloco
    $foto= null;
    // valor padrão: sem foto

    // se não teve erro de validação e o campo "foto" veio no POST 
    if ($erro==='' && isset($_FILES['foto']) && $_FILES['foto']['error']!== UPLOAD_ERR_NO_FILE){
        // verifica se houve erro no upload(constantes nativas UPLOAD_ERR_*)
        if ($_FILES['foto']['erro']!== UPLOAD_ERR_OK){
            $erro='Erro ao enviar a imagem';
        } else{
            // (opcional) Limite de tamanho: até 2MB
            if ($_FILES['foto']['size']>2*1024*1024){
                $erro='imagem muito grande(máx. 2MB)';
            }
            // Validar o tipo real do arquivo (para garantir que é uma image, de verdade)
            if ($erro==''){
                // finfo -> classe nativa do php usada para descobrir o tipo ral do arquivo
                // (MIME= tipo do arquivo, ex: image/jgep, image/png, application/pdf, etc.)
                $finfo= new finfo(FILEINFO_MIME_TYPE);
                // $FILES['foto']['tmp_name'] -> caminho temporário onde o PHP guarda o arquivo
                // antes de mover para pasta final (tipo uma area de "rascunho")
                $mime= $finfo->file($FILES['foto']['tmp_name']);
                // lista de tipos de imagem que o sistema aceita (extensão associada)
                $permitidos = [
                    'image/jgep' => 'jpg',
                    'image/png' => 'png',
                    'image/gif'=> 'gif'
                ];

                // verifica se o tipo detectado está na lista dos permitidos
                // se não estiver, mostra erro ao usuario
                if (!isset($permitidos[$mime])){
                    $erro= 'formato inadequado. Use jpg, png ou gif';
                }
            }
            // cria a apasta "uploads" se ainda não existir
            if ($erro === ''){
                $dirUpload = __DIR__ . '/uploads'; 
                // __DIR__ mostra a pasta atual do arquivo

                if (!list_dir($dirUpload)){
                    // is_dir() verifica se a pasta existe
                    // mkdir() cria pastas
                    // 0755 = permissão padrão (dono pode tudo)
                    // true= cria subpastas se for preciso
                    mkdir($dirUpload, 0755, true);
                }
            }
            // começar aq
    }

    // 3) Se não houver erro de validação, tenta salvar os dados no banco 
    if ($erro === '') {
        try {
            // SQL com placeholders nomeados (evita SQL Injection)
            // Os dois-pontos (:) indicam variáveis que serão substituidas depois
            $sql = 'INSERT INTO cadastro (nome, email, telefone)
                    VALUES (:nome, :email, :telefone)';

              // db()-> função personalizada que retorna a conexão PDO com o banco de dados
    // prepare()-> método nativo do PDO que "pré-compila" o SQL no servidor 
    // isso aumenta a segurança e o desempenho, pois separa o comendo SQL dos dados 
            $query = db()->prepare($sql);
 // execute() -> método nativo do PDO que executa o comando preparado 
    // aqui passamos os valores que vão substituir os placeholders nomeados
            $query->execute([
                ':nome' => $nome,
                ':email' => $email,
                ':telefone' => $telefone,
            ]);

            // marca que o cadastro foi salvo com sucesso
            $ok = true;

        } catch (PDOException $e) { 
            if ($e->getCode() == '23000') {
                $erro = 'Este e-mail já está cadastrado.';
            } else {
                $erro = 'Erro ao salvar: ' . $e->getMessage();
            }
        }
    }
}


?>
<!doctype html>
<meta charset="utf-8">
<title>Salvar</title>
 
<!-- Se deu tudo certo no cadastro, mostra mensagem de sucesso -->
<?php if ($ok): ?>
  <p>Dados salvos com sucesso!</p>
  <p><a href="formulario.php">Voltar</a></p>
 
<!-- Se não deu certo, entra aqui -->
<?php else: ?>
 
  <!-- Se existe mensagem de erro, exibe em vermelho -->
  <?php if ($erro): ?>
    <!-- htmlspecialchars() → função nativa do PHP que converte caracteres especiais em HTML seguro -->
    <!-- Evita que alguém insira tags HTML ou scripts maliciosos dentro da mensagem -->
    <p style="color:red;"><?= htmlspecialchars($erro) ?></p>
 
  <!-- Se chegou aqui sem erro e sem POST, o usuário acessou a página diretamente -->
  <?php else: ?>
    <p>Nada enviado.</p>
  <?php endif; ?>
 
  <!-- Link pra voltar pro formulário -->
  <p><a href="formulario.php">Voltar</a></p>
 
<?php endif; ?>
