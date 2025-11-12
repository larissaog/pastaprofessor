<?php
    include __DIR__. '/includes/header.php';

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style1.css">
</head>
<body class="bg-secondary-subtle">

    <h1 class="title ">Cadastro</h1>
    <div class="formulario">
          <form action="salvar.php" method="post" enctype="multipart/form-data">
            <!-- O atributo enctype serve para avisar o navegador que o formulario vai enviar arquivos, não só textos -->
        <label for="">Nome:
        <input type="text" name="nome" id="input" required>
    </label><br>
        <label for="">Email:
            <input type="email" name="email" id="input" required>
        </label><br>

        <label for="">Telefone:
            <input type="text" name="telefone" id="input" required>
        </label><br>
        <label for="">Foto:</label>
        <input type="file" name=foto id=foto><br><br>
        <!-- Button trigger modal -->
<button type="button" class="button-envair" data-bs-toggle="modal" data-bs-target="#exampleModal">
  enviar
</button>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Olá. Seja bem-vindo(a)!!</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Seu cadastro foi realizado com sucesso. Aperte em fechar para continuar navegando.
        Aceita receber informações no seu email? <br>
        <div>
  <input class="form-check-input" type="checkbox" id="checkboxNoLabel" value="" aria-label="...">
</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>
        <button class="button-envair" type="reset">Limpar</button>
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