<?php
/**
 * Created by PhpStorm.
 * User: joão Madeira
 * Date: 15/01/2018
 * Time: 16:34
 */

require_once "bd_tabelas.php";
require_once "querys.php";
ini_set('max_execution_time', 300);

define(
    "URL_PRINCIPAL_PREFIXO",
    'https://www.tugaflix.com/Series?T='
);

define(
    "URL_PRINCIPAL_SUFIXO",
    '&G=&O=1'
);

define(
    "URL_SERIE",
    "https://www.tugaflix.com"
);

define(
    "URL_EPISODIO",
    "https://www.tugaflix.com/Episodio?E="
);

//define("MARCADOR_DE_HREFS", "<a class=\"browse-movie-title\" href=\"");
define("MARCADOR_DE_HREFS", "<a href=\"");
define("MARCADOR_DE_HREFS_SERIES", "<a class=\"browse-movie-link\" href=\"");
define("FECHO_SERIE","\" class=\"browse-movie-title\" title=\"");

///////////////////////////////////// INICIO PARTE H4 EPISODIOS ///////////////////////////////////////////

define("MARCADOR_DE_NOME_SERIE", "<h4 class=\"gridepisode1\">");
define("FECHO_EPISODIO","</h4>");

///////////////////////////////////// FIM PARTE H4 EPISODIOS ///////////////////////////////////////////

function definirUrlPrincipal($pSerie){
    $partesSeries =
        explode(" ", $pSerie);

    $qualSerie = "";
    if(count($partesSeries)>0) {
            $idx = 1;
            foreach ($partesSeries as $parte) {
                if (count($partesSeries) > $idx) {
                    $qualSerie = $qualSerie . $parte . "+";
                } else {
                    $qualSerie = $qualSerie . $parte;
                }
                $idx++;
            }//for
        //}//if
        $urlSeriePesquisa = URL_PRINCIPAL_PREFIXO . $qualSerie . URL_PRINCIPAL_SUFIXO;
        return $urlSeriePesquisa;
    }//if
    return null;
}//definirUrlPrincipal

function definirUrl($pUrlPrincipal, $pFecho){
    $dadosDaPagina = get_data($pUrlPrincipal);
    $partesDaPagina =
        explode(MARCADOR_DE_HREFS, $dadosDaPagina);
    $urlSerie = "";
    $parteNumero = 0;
    foreach (
        $partesDaPagina
        as
        $parte
    ){
        //echo $parte;
        if ($parteNumero>0){
            $posicaoDaAspaDeEncerramento =
                stripos($parte, $pFecho);
            $aspaExiste =
                $posicaoDaAspaDeEncerramento!==false;
            if($aspaExiste) {
                $url = substr(
                    $parte,
                    0,
                    $posicaoDaAspaDeEncerramento
                );
                $urlSerie = $url;
            }
        }
        $parteNumero++;
    }
    return $urlSerie;
}

function get_data($pUrl) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $pUrl);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //inseguro
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}


function compararArrays($linksEpisodios, $nomesEpisodios, $pSerie){
    $insertPorVer = [];

    if(count($linksEpisodios) === count($nomesEpisodios)){
        for($i = 0; $i < count($linksEpisodios); $i++){
            $linksEpisodios[$i] = URL_SERIE . $linksEpisodios[$i];
        }
        for($i = 0; $i < count($linksEpisodios); $i++){
            //echo $col[0][$i] . ": " . $col[1][$i].PHP_EOL;
            $insertPorVer[$i] = [$pSerie, $linksEpisodios[$i], substr(
                $nomesEpisodios[$i], 0, 3), substr(
                $nomesEpisodios[$i], 3)];
        }
        return $insertPorVer;
    }
}

function pesquisaEpisodiosSerie($pSerie){
    $urlSeriePesquisa = definirUrlPrincipal($pSerie);
    if ($urlSeriePesquisa != null) {
        $urlParteSerie = definirUrl($urlSeriePesquisa, FECHO_SERIE . $pSerie);
        if ($urlParteSerie != "") {
            $urlSerie = URL_SERIE . $urlParteSerie;

            $urlEpisodios = definirUrlEpisodio($urlSerie, "\"", MARCADOR_DE_HREFS_SERIES);

            $resultadoNomes = definirUrlEpisodio($urlSerie, FECHO_EPISODIO, MARCADOR_DE_NOME_SERIE);

            $episodiosPorver = compararArrays($urlEpisodios, $resultadoNomes, $pSerie);

            return $episodiosPorver;
        }
        return "Erro de pesquisa da serie";
    }
    return "Erro na formação do url da serie";
}

function definirUrlEpisodio($pUrlPrincipal, $pFecho , $x){
    $dadosDaPagina = get_data($pUrlPrincipal);
    $partesDaPagina =
        explode($x, $dadosDaPagina);
    $urlSerie = [];
    $parteNumero = 0;
    foreach (
        $partesDaPagina
        as
        $parte
    ){
        if ($parteNumero>0){
            $posicaoDaAspaDeEncerramento =
                stripos($parte, $pFecho);
            $aspaExiste =
                $posicaoDaAspaDeEncerramento!==false;
            if($aspaExiste) {
                $url = substr(
                    $parte,
                    0,
                    $posicaoDaAspaDeEncerramento
                );
                $urlSerie[] = $url;
            }
        }
        $parteNumero++;
    }
    return $urlSerie;
}

function novaSerie($pSerie)
{
    //para bd
    $db = dbConnect();
    $jaExiste = serieExistente($db, $pSerie);
    $db->close();

    if ($jaExiste === "false") {
        $db = dbConnect();
        dbInsertNovaSerie($db, $pSerie);
        $db->close();
        $episodiosPorVer = pesquisaEpisodiosSerie($pSerie);
        //para bd
        $db = dbConnect();
        for ($i = 0; $i < count($episodiosPorVer); $i++) {
            dbInsertEpisodiosNaoVistos($db, $episodiosPorVer[$i][0], $episodiosPorVer[$i][1], $episodiosPorVer[$i][2], $episodiosPorVer[$i][3]);
        }
        $db->close();
    }

}

function apagarEpVisto($pSerie, $pTemporada, $pEpisodio){
    $db = dbConnect();
    $episodiosPorVer = dbEpisodiosPorVer($db, $pSerie);
    $db->close();
    compararEpisodiosEApagar($pTemporada, $pEpisodio, $episodiosPorVer, $pSerie);
    $db = dbConnect();
    dbAtualizarEpisodios($db, $pEpisodio, $pTemporada, $pSerie);
    $db->close();
}

function compararEpisodiosEApagar($pTemporada, $pEpisodio, $episodiosPorVer, $pNome){
    $col = [];
    $ultimoSaido = $episodiosPorVer[0];
    if($ultimoSaido[2] == $pTemporada && $ultimoSaido[3] == $pEpisodio){
        $db = dbConnect();
        dbRemove($db, $pEpisodio, $pTemporada, $pNome);
        $db->close();
    } else {
        $db = dbConnect();
        for($i = count($episodiosPorVer)-1 ; $i > -1 ; $i--){
            if($episodiosPorVer[$i][2] <= $pTemporada && $episodiosPorVer[$i][3] <= $pEpisodio){
                dbRemove($db, $episodiosPorVer[$i][3], $episodiosPorVer[$i][2], $pNome);
            }
        }$db->close();
        //return $col;
    }

}//compararEpisodios

function sincronizarEpisodios(){
    $db = dbConnect();
    $series = dbEpisodiosTodosPorVer($db);
    $db->close();
    for($i = 0; $i < count($series); $i++){
         //retorna o que tem o Ep mais alto Temp mais alta
         $db = dbConnect();
         $todosPorVerBD = dbEpisodiosPorVer($db, $series[$i][0]);
         $db->close();
        $maisAltoPorver = $todosPorVerBD[count($todosPorVerBD)-1];

         //toados os episodios disponiveis no tuga para a serie
         $seriesTuga = pesquisaEpisodiosSerie($series[$i][0]);
         //retorna o que tem o Ep mais alto Temp mais alta
         $ultimoSaido = $seriesTuga[count($seriesTuga)-1];

         $igual = compararEpisodiosSincro($ultimoSaido, $maisAltoPorver, $seriesTuga);

         if ($igual != "Esta fixolas"){
             $db = dbConnect();
             for($i = 0; $i < count($igual); $i++) {
                 dbInsertEpisodiosNaoVistos($db, $igual[$i][0], $igual[$i][1], $igual[$i][2], $igual[$i][3]);
             }
             $db->close();
         }

    }
}

function compararEpisodiosSincro($ultimoSaido, $maisAltoPorver, $seriesTuga){
    $col = [];
    if($ultimoSaido[0] === $maisAltoPorver[0] && $ultimoSaido[1] === $maisAltoPorver[1] &&
        $ultimoSaido[2] === $maisAltoPorver[2] && $ultimoSaido[3] === $maisAltoPorver[3]){
        return "Esta fixolas";
    } else {
        for($i = 0; $i < count($seriesTuga); $i++){
            if($seriesTuga[$i][2] >= $maisAltoPorver[2] && $seriesTuga[$i][3] > $maisAltoPorver[3]){
                array_push($col,$seriesTuga[$i]);
            }
        }
        return $col;
    }

}//compararEpisodiosSincro

function apagarSerie($pSerie){
    $db = dbConnect();
    removerPorVerPorNome($db, $pSerie);
    removerVistoPorNome($db, $pSerie);
    $db->close();
}

function selectTodas(){
    $db = dbConnect();
    $todasAsSeries = selectSerieVistos($db);
    $db->close();
    /*for($i = 0; $i < count($todasAsSeries); $i++){
        echo "Nome: ". $todasAsSeries[$i][0] . " Temporada " . $todasAsSeries[$i][1] . " Episodio " . $todasAsSeries[$i][2].PHP_EOL;
    }*/
    return $todasAsSeries;
}

function selectPorVer($pSerie){
    $db = dbConnect();
    $epPorVerDaSerie = vistoPorNome($db, $pSerie);
    $db->close();
    /*for($i = 0; $i < count($epPorVerDaSerie); $i++){
        echo "Nome: ". $epPorVerDaSerie[$i][0] . " Link: " . $epPorVerDaSerie[$i][1]. " Temporada " . $epPorVerDaSerie[$i][2] . " Episodio " . $epPorVerDaSerie[$i][3].PHP_EOL;
    }*/
    return $epPorVerDaSerie;
}

function nEpPorVer($pSerie){
    $db = dbConnect();
    $nPorVer = episodiosPorVer($db,$pSerie);
    $db->close();
    return $nPorVer;
}

/*$serie = $argv;
novaSerie($serie[1]);*/
//apagarEpVisto("The Deuce", "S01", "E05");
/*$serie = $argv;
nEpPorVer($serie[1]);*/
//novaSerie($serie[1]);
//apagarSerie($serie[1]);

/*if($_POST['novaSerie'] != "")
{
    novaSerie($_POST['novaSerie']);//echo("Welcome, Spoom.");
}*/

//apagarEpVisto("Bull", "S01", "E05");
