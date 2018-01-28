<?php
require_once "pesquisador.php";
$css = file_get_contents('css/style.css');
/*Aqui recebemos as variaveis necessarias para fazer a função de remover os episodios vistos (apagarEpVisto()), provenientes do
ficheiro tabelaEpisodios.php*/
  $nomeVisto = $_POST['marcarVistoNome'];
  $temporadaVisto =  $_POST['marcarVistoTemporada'];
  $episodioVisto =  $_POST['marcarVistoEpisodio'];

apagarEpVisto($nomeVisto,$temporadaVisto,$episodioVisto);

/*Aqui criamos dinamicamente a tabela que lista todas as series já adicionadas ao nosso site*/
function criarTabela()
{
    echo "<form method='post' action='serie.html'>";
    //echo "<input type='button' id='btnSync' name='btnSync' value='Synchronize'>";
    echo "</form>";
    echo "<table id='tabelaSeries'>";
    echo "<tr>";
    echo "<th>Nome</th>";
    echo "<th>Temporada</th>";
    echo "<th>Episodio</th>";
    echo "<th>Ver</th>";
    echo "<th>Por ver</th>";
    echo "<th>Remover</th>";
    echo "</tr>";

    $todasSerie = selectTodas();
    for ($i = 0; $i < count($todasSerie); $i++) {
        echo "<tr>";
        $x = $todasSerie[$i][0];
        echo "<td> <form method='post' action='serie.html'><input id='linkEpi' name='linkEpi' type='submit' value='".$todasSerie[$i][0]."'></form></td>";
        echo "<td>" . $todasSerie[$i][2] . "</td>";
        echo "<td>" . $todasSerie[$i][3] . "</td>";
        echo "<td> <a href='" . URL_SERIE . definirUrl(definirUrlPrincipal($todasSerie[$i][0]),FECHO_SERIE) . "'>" . URL_SERIE . definirUrl(definirUrlPrincipal($todasSerie[$i][0]),FECHO_SERIE) . "</a></td>";
        echo "<td>" . nEpPorVer($todasSerie[$i][0]) . "</td>";
        echo "<td><form method='post' action='minhasSeries.html'><input id='apagarSerie' name='apagarSerie' type='submit' value='".$todasSerie[$i][0]."'></form></td>";
        echo "</tr>";
    }

    echo "</table>";
}

   /*Aqui chamamos a função para apagar serie, esta função recebe o valor proveniente do form acima com o id='apagarSerie'*/
    apagarSerie($_POST['apagarSerie']);
/*Aqui chamamos a função para sincronizar as series, esta função recebe o valor proveniente do botao acima com o id='btnSync'*/
if(!isset($_POST['btnSync'])){
    sincronizarEpisodios();
}
