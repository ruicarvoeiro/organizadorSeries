<?php
require_once "pesquisador.php";
$css = file_get_contents('css/style.css');

function criarTabela()
{
        $linkEpi = $_POST['linkEpi'];

        $serie = $_POST['Serie'];

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
           echo "<td><form method='post' action='minhasSeries.html'>
            <input type='hidden' name='marcarVistoNome' value='".$epPorVerDaSerie[$i][0]."'/>
            <input type='hidden' name='marcarVistoTemporada' value='".$epPorVerDaSerie[$i][2]."'/>
            <input type='hidden' name='marcarVistoEpisodio' value='".$epPorVerDaSerie[$i][3]."'/>
            <input type='submit' name='apagar' value='Visto'></form></td>";
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
            echo "<td><form method='post' action='minhasSeries.html'>
            <input type='hidden' name='marcarVistoNome' value='".$epPorVerDaSerie[$i][0]."'/>
            <input type='hidden' name='marcarVistoTemporada' value='".$epPorVerDaSerie[$i][2]."'/>
            <input type='hidden' name='marcarVistoEpisodio' value='".$epPorVerDaSerie[$i][3]."'/>
            <input type='submit' name='apagar' value='Visto'></form></td>";
            echo "</tr>";
        }

        echo "</table>";
    }
}

