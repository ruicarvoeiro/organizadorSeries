<?php
require_once "pesquisador.php";
$css = file_get_contents('css/style.css');
//echo $css;

function criarTabela()
{
    echo "<form method='post' action='serie.html'>";
    echo "<input type='button' id='btnSync' name='btnSync' value='Synchronize'>";
    echo "</form>";
    //ob_clean();
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
        /*echo "<td><a name='linkEpisodios' href=\"serie.html\">" . $todasSerie[$i][0] . selectPorVer($todasSerie[$i][0]) . "</td>";*/
        echo "<td> <form method='post' action='serie.html' style='width:100%; height: 100%;'><button id='linkEpi' name='linkEpi' type='submit' value='".$todasSerie[$i][0]."'></button></form></td>";
        echo "<td>" . $todasSerie[$i][2] . "</td>";
        echo "<td>" . $todasSerie[$i][3] . "</td>";
        echo "<td> <a href='" . URL_SERIE . definirUrl(definirUrlPrincipal($todasSerie[$i][0]),FECHO_SERIE) . "'>" . URL_SERIE . definirUrl(definirUrlPrincipal($todasSerie[$i][0]),FECHO_SERIE) . "</a></td>";
        echo "<td>" . nEpPorVer($todasSerie[$i][0]) . "</td>";
        echo "<td><form method='post' action='minhasSeries.html'  style='width:100%; height: 100%;'><button id='apagarSerie' name='apagarSerie' type='submit' value='".$todasSerie[$i][0]."'></button></form></td>";
        echo "</tr>";
    }

    echo "</table>";
}

//criarTabela();
//if (!isset($_POST['apagarSerie'])){
    //$apagarSerie = ;
    apagarSerie($_POST['apagarSerie']);
//}

if(!isset($_POST['btnSync'])){
    sincronizarEpisodios();
}
