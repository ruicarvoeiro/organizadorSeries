<?php

//"Chama" os ficheiro das base de dados e das querys 
require_once "bd_tabelas.php";
require_once "querys.php";

/*Define o tempo maximo de execução para 5 minutos, tivemos de o fazer devido a alguma lentidão do servidor apache em responder aos pedidos
 e com isto damos tempo suficiente para o mesmo processar os pedidos */
ini_set('max_execution_time', 300);

/*Aqui decicimos esconder os erros de php que estavam a aparecer, erros esses que nao tinham qualquer efeito na execuçao do projeto mas que
 afetavam a interface */
ini_set('display_errors',0);
error_reporting(E_ALL|E_STRICT);

/*Neste conjunto de comandos definimos constantes para as varias partes do url que vamos buscar ao site tugaflix*/
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


define("MARCADOR_DE_HREFS", "<a href=\"");
define("MARCADOR_DE_HREFS_SERIES", "<a class=\"browse-movie-link\" href=\"");
define("FECHO_SERIE","\" class=\"browse-movie-title\" title=\"");

///////////////////////////////////// INICIO PARTE H4 EPISODIOS ///////////////////////////////////////////

define("MARCADOR_DE_NOME_SERIE", "<h4 class=\"gridepisode1\">");
define("FECHO_EPISODIO","</h4>");

///////////////////////////////////// FIM PARTE H4 EPISODIOS ///////////////////////////////////////////

/*Aqui definimos o Url principal, ou seja, a função recebe um url da pesquisa de uma serie e divide-o em partes, fizemos isto com o intuito de
usarmos sempre esta função para definirmos os urls gerados na pesquisa da série em questão */
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


/*Aqui definimos o Url da série em especifico que nos leva para a página da mesma, ou seja, a função tal como a anterir divide o url em partes,
para podermos subsituir certas partes dos urls, com os dados da série em questão */
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
}//definirUrl

/*Função em que utilizamos o CURL para ir buscar os urls ao site */
function get_data($pUrl) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $pUrl);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //inseguro
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}//get_data

/*Esta função serve para comparar os arrays com nomes dos episódios e com os arrays com urls dos episódios de forma a colocar ambos a par
um do outro, ou seja, o episodio fica com o ligado ao seu URL correspondente */
function compararArrays($linksEpisodios, $nomesEpisodios, $pSerie){
    $insertPorVer = [];

    if(count($linksEpisodios) === count($nomesEpisodios)){
        for($i = 0; $i < count($linksEpisodios); $i++){
            $linksEpisodios[$i] = URL_SERIE . $linksEpisodios[$i];
        }
        for($i = 0; $i < count($linksEpisodios); $i++){
            $insertPorVer[$i] = [$pSerie, $linksEpisodios[$i], substr(
                $nomesEpisodios[$i], 0, 3), substr(
                $nomesEpisodios[$i], 3)];
        }
        return $insertPorVer;
    }
}//compararArrays

/*Nesta função fazemos o outro passo da nossa pesquisa, a pesquisa dos episodios da serie, em que utilizamos as constantes e as funçoes para
definir urls criadas acima, juntamente com o comparador de arrays, isto para buscar todas os episodios e os seus respetivos urls */
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
}//pesquisaEpisodiosSerie

/*Esta função é similar ás funçoes acima de definir urls, só que neste caso para definir os urls dos episodios da serie em questao*/
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
}//definirUrlEpisodio

/*Esta funcão serve para adicionar a serie pesquisada na base de dados, funçao esta que é accionada quando fazemos a pesquisa de uma serie
apartir do motor de buscar da nossa interface*/
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

}//novaSerie

/*Função para apagar o episodio visto, ou seja, o episodio é apagado da base de dados juntamente com todos os episodios que estiverem antes
do mesmo, porque decidimos que nao fazia sentido apagar um episodio e deixar os anteriores na base de dados porque partimos do principio
que os mesmos já terão sido vistos pelo utilizador*/
function apagarEpVisto($pSerie, $pTemporada, $pEpisodio){
    $db = dbConnect();
    $episodiosPorVer = dbEpisodiosPorVer($db, $pSerie);
    $db->close();
    compararEpisodiosEApagar($pTemporada, $pEpisodio, $episodiosPorVer, $pSerie);
    $db = dbConnect();
    dbAtualizarEpisodios($db, $pEpisodio, $pTemporada, $pSerie);
    $db->close();
}//apagarEpVisto

/*Nesta função apagamos apagamos todos os episodios anteriores ao episodio visto*/
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

/*Aqui fazemos a sincronização da base de dados com o site de busca, ou seja, se já tiver saido um ou mais episodios novos de uma serie que ja esteja
inserida no nosso site, com esta função vamos buscar esse/es episodio/os ao tugaflix e adicionamos no nosso site*/
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

         //todos os episodios disponiveis no tugaflix para a serie
         $seriesTuga = pesquisaEpisodiosSerie($series[$i][0]);
         //retorna o que tem o Ep mais alto Temp mais alta
         $ultimoSaido = $seriesTuga[count($seriesTuga)-1];

         $igual = compararEpisodiosSincro($ultimoSaido, $maisAltoPorver, $seriesTuga);

         if ($igual != "Funciona"){
             $db = dbConnect();
             for($i = 0; $i < count($igual); $i++) {
                 dbInsertEpisodiosNaoVistos($db, $igual[$i][0], $igual[$i][1], $igual[$i][2], $igual[$i][3]);
             }
             $db->close();
         }

    }
}//sincronizarEpisodios

/*Aqui fazemos uma função para comparar os episodios da base de dados com os do TugaFlix, para ser utilizada na função acima de sincronização*/
function compararEpisodiosSincro($ultimoSaido, $maisAltoPorver, $seriesTuga){
    $col = [];
    if($ultimoSaido[0] === $maisAltoPorver[0] && $ultimoSaido[1] === $maisAltoPorver[1] &&
        $ultimoSaido[2] === $maisAltoPorver[2] && $ultimoSaido[3] === $maisAltoPorver[3]){
        return "Funciona";
    } else {
        for($i = 0; $i < count($seriesTuga); $i++){
            if($seriesTuga[$i][2] >= $maisAltoPorver[2] && $seriesTuga[$i][3] > $maisAltoPorver[3]){
                array_push($col,$seriesTuga[$i]);
            }
        }
        return $col;
    }

}//compararEpisodiosSincro

/*Função que tal como o nome indica, apaga uma serie da nossa base de dados*/
function apagarSerie($pSerie){
    $db = dbConnect();
    removerPorVerPorNome($db, $pSerie);
    removerVistoPorNome($db, $pSerie);
    $db->close();
}//apagarSerie

/*Função para selecionar todas as series vistas da nossa base de dados*/
function selectTodas(){
    $db = dbConnect();
    $todasAsSeries = selectSerieVistos($db);
    $db->close();
    return $todasAsSeries;
}//selectTodas

/*Função para selecionar todos os episodios por ver relacionados a uma serie*/
function selectPorVer($pSerie){
    $db = dbConnect();
    $epPorVerDaSerie = vistoPorNome($db, $pSerie);
    $db->close();
    return $epPorVerDaSerie;
}//selectPorVer

/*Função para selecionar o numero de episodios por ver daquela serie*/
function nEpPorVer($pSerie){
    $db = dbConnect();
    $nPorVer = episodiosPorVer($db,$pSerie);
    $db->close();
    return $nPorVer;
}//nEpPorVer
