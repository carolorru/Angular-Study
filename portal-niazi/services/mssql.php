<?php

//error_reporting(E_ERROR | E_PARSE);
error_reporting(E_ALL);

ini_set("display_errors",1);

// Dados do banco
$dbhost   = "177.102.18.147";   #Nome do host
$db       = "UPERP";   #Nome do banco de dados
$user     = "sa"; #Nome do usuário
$password = "uperp@3468";   #Senha do usuário
 
// Dados da tabela
$tabela = "PAINEL_ACD_001";    #Nome da tabela
$campo1 = "EMISSAO";  #Nome do campo da tabela
$campo2 = "NUM_PED";  #Nome de outro campo da tabela
 
@mssql_connect($dbhost,$user,$password) or die("Não foi possível a conexão com o servidor!");
@mssql_select_db("$db") or die("Não foi possível selecionar o banco de dados!");
 
$instrucaoSQL = "SELECT $campo1, $campo2 FROM $tabela ORDER BY $campo1";
$consulta = mssql_query($instrucaoSQL);
$numRegistros = mssql_num_rows($consulta);
 
echo "Esta tabela contém $numRegistros registros!\n<hr>\n";
 
if ($numRegistros!=0) {
    while ($cadaLinha = mssql_fetch_array($consulta)) {
        echo "$cadaLinha[$campo1] - $cadaLinha[$campo2]\n<br>\n";
    }
}

