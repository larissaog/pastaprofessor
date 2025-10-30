<?php
function db(): PDO{
    // cria uma funcao chamada "db" que vai devolver uma conexão PDO com o banco de dados
    static $pdo;
    // a variavel $pdo é "static", ou seja, se a função for chamada de novo, 
    // ela reaproveita a mesma conexão (não cria uma nova a cada vez)
    if (!$pdo){
        // verifica se ainda não existe conexão ativa
        // O ! em PHP é o operador lógico de negação, também conhecido como "NOT".
        // Ele inverte o valor lógico de uma expressão:
        // Se a expressão for verdadeira (true), o ! a transforma em falsa (false).
        // Se for falsa (false), o ! a transforma em verdadeira (true).
        try{
            // tenta executar o bloco abaixo(se der erro, o catch vai tratar)
            $dsn= 'mysql:host=127.0.0.1;dbname=larissa01;charset=utf8mb4';
            // define a string de conexão(DSN) dizendo o tipo de banco (mysql),
            // o servidor (127.0.0.1 -> local ), o nome do banco de dados (larissa01),
            // e o conjunto de caracteres (utf8mb4).
            $pdo=new PDO(
                $dsn,                                 
                'root',
                '',
                // caminho do banco
                // usuario do banco (no XAMPP geralmente é "root")
                // senha do banco (no XAMPP normalmente fica vazia)
                 [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
                    // DEFINE QUE SE DER ERRO, o PDO vai lançar uma exceção (erro visível)
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    // define que quando buscar dados, eles virão como arrays associativos
                    // (ou seja, com nomes das colunas, e não números)

                 ]

                 );
                //  echo "<b> Conectado com sucesso ao banco!</b>";
                //  mostra a mensagem direto na tela se a conexão der certo
   
            
        }catch (PDOException $e){
            // se der algum erro no bloco try (acima), cai aqui
            echo "<b> Erro ao conectar ao banco: </b>". $e->getMessage();
            // Mostra a mensagem de erro diretamente na tela

            exit;
            // encerra a execução do script (opcional)
        }
 
}

return $pdo;
// retorna objeto de conexão

}

// chama a função automaticamente se o arquivo for aberto direto no navegador
if (basename(__FILE__)===basename($_SERVER['SCRIPT_FILENAME'])){
    db();
    // executa a conexão e mostra a mensagem na tela
}