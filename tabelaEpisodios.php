<?php
require_once "pesquisador.php";
$css = file_get_contents('css/style.css');
//echo $css;

function criarTabela()
{
    //if (!isset($_POST['apagar']$_POST['linkEpi'] !== "") {
        $linkEpi = $_POST['linkEpi'];

    //} else {
    //if ($_POST['Serie'] !== "") {
        $serie = $_POST['Serie'];
    //}
    //ob_start();
    
    //ob_clean();
    novaSerie($serie);


    if($linkEpi != ""){
        echo "<h2 id='tituloSerie' style='text-align: center; color: darkblue'>$linkEpi</h2>";
        echo "<table>";
        echo "<tr>";
        echo "<th>Nome</th>";
        echo "<th>Url</th>";
        echo "<th>Temporada</th>";
        echo "<th>Episodio</th>";
        echo "<th>Visto</th>";

        $epPorVerDaSerie = selectPorVer($linkEpi);
        for ($i = 0; $i < count($epPorVerDaSerie); $i++) {
            echo "<tr>";
            echo "<td>" . $epPorVerDaSerie[$i][0] . "</td>";
            echo "<td> <a href='" . $epPorVerDaSerie[$i][1] . "'>" . $epPorVerDaSerie[$i][1] . "</a> </td>";
            echo "<td>" . $epPorVerDaSerie[$i][2] . "</td>";
            echo "<td>" . $epPorVerDaSerie[$i][3] . "</td>";
            echo "<td><form method='post' action='minhasSeries.html'  style='width:100%; height: 100%;'>";
            echo "<button type='submit' name='apagar'>";
            echo "<input type='hidden' name='marcarVistoNome' value='".$epPorVerDaSerie[$i][0]."'/>";
            echo "<input type='hidden' name='marcarVistoTemporada' value='".$epPorVerDaSerie[$i][2]."'/>";
            echo "<input type='hidden' name='marcarVistoEpisodio' value='".$epPorVerDaSerie[$i][3]."'/>";
            echo "</button></form></td>";
            //echo "<td><input type=\"button\"  class=\"btnX\"value=\"Visto\" onclick=\"location.href='minhasSeries.html'\">";
            echo "</tr>";
        }

        echo "</table>";
    }else{
        echo "<h2 id='tituloSerie' style='text-align: center; color: darkblue'>$serie</h2>";
        echo "<table>";
        echo "<tr>";
        echo "<th>Nome</th>";
        echo "<th>Url</th>";
        echo "<th>Temporada</th>";
        echo "<th>Episodio</th>";
        echo "<th>Visto</th>";
        echo "</tr>";
        $epPorVerDaSerie = selectPorVer($serie);
        for ($i = 0; $i < count($epPorVerDaSerie); $i++) {
            echo "<tr>";
            echo "<td>" . $epPorVerDaSerie[$i][0] . "</td>";
            echo "<td> <a href='" . $epPorVerDaSerie[$i][1] . "'>" . $epPorVerDaSerie[$i][1] . "</a> </td>";
            echo "<td>" . $epPorVerDaSerie[$i][2] . "</td>";
            echo "<td>" . $epPorVerDaSerie[$i][3] . "</td>";
            echo "<td><form method='post' action='minhasSeries.html'  style='width:100%; height: 100%;'>";
            echo "<button type='submit' name='apagar'>";
            echo "<input type='hidden' name='marcarVistoNome' value='".$epPorVerDaSerie[$i][0]."'/>";
            echo "<input type='hidden' name='marcarVistoTemporada' value='".$epPorVerDaSerie[$i][2]."'/>";
            echo "<input type='hidden' name='marcarVistoEpisodio' value='".$epPorVerDaSerie[$i][3]."'/>";
            echo "</button>";
            echo "</form></td>";
            
            echo "</tr>";
        }

        echo "</table>";
    }
}

//criarTabela();
//if (!isset($_POST['apagar'])/*!isset($_POST['marcarVistoNome']) && !isset($_POST['marcarVistoTemporada']) && !isset($_POST['marcarVistoEpisodio'])*/){
    //echo var_dump($_POST['marcarVistoNome']." - " .$_POST['marcarVistoTemporada']." - " . $_POST['marcarVistoEpisodio']);
    apagarEpVisto($_POST['marcarVistoNome'], $_POST['marcarVistoTemporada'], $_POST['marcarVistoEpisodio']);
//}
