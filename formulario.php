<?php
    include __DIR__. '/includes/header.php';

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    <h1 class="title">Cadastro</h1>
    <div class="formulario">
         <form action="salvar.php" method="post" enctype="multipart/form-data">
            <!-- O atributo enctype serve para avisar o navegador que o formulario vai enviar arquivos, não só textos -->
        <label for="">Nome:
        <input type="text" name="nome" id="" required><br>
    </label>

        <label for="">Email:
            <input type="email" name="email" required><br>
        </label>

        <label for="">Telefone:
            <input type="text" name="telefone" required><br>
        </label>
        <label for="">Foto:</label>
        <input type="file" name=foto id=foto>
        <button>Enviar</button>
    </form>

    </div>
   
</body>
</html>
<!-- linha -->
<hr>
<?php

// dir significa o diretorio que ele vai seguir
    include __DIR__. '/includes/footer.php';

?>