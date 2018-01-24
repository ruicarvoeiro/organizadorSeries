<?php
require_once "pesquisador.php";
require_once "tabelaEpisodios.php";
/*
$serie = $_POST['Serie'];
    novaSerie($serie);
*/

file_put_contents("serie.html",criarTabela());
?>
