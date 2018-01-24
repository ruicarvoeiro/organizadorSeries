<?php
require_once "bd_tabelas.php";
function dbInsertNovaSerie($pDB, $pNome){
    $q = "insert into ".MYSQL_SCHEMA.".".MYSQL_TABLE_SERIE_VISTO." values(null, '$pNome', 'S01', 'E00')";
    if ($pDB){
        $result = $pDB->query($q);
        //$e = mysqli_errno($pDB);
        //$eM = mysqli_error($pDB);
        //if (VERBOSE) fb(__FUNCTION__, $q, $e, $eM);
        return $result;
    }
    return false;
}
function dbInsertEpisodiosNaoVistos($pDB, $pNome, $pLink, $pTemporada, $pEpisodio){
    $q = "insert into ".MYSQL_SCHEMA.".".MYSQL_TABLE_SERIE_POR_VER." values(null, '$pNome', '$pLink', '$pTemporada', '$pEpisodio')";
    if ($pDB){
        $result = $pDB->query($q);
        //$e = mysqli_errno($pDB);
        //$eM = mysqli_error($pDB);
        //if (VERBOSE) fb(__FUNCTION__, $q, $e, $eM);
        return $result;
    }
    return false;
}
function dbAtualizarEpisodios($pDB, $pEpisodio, $pTemporada, $pNome){
    $q = "update ".MYSQL_SCHEMA.".".MYSQL_TABLE_SERIE_VISTO." set episodio='$pEpisodio', temporada='$pTemporada' where nome = '$pNome'";
    if ($pDB){
        $result = $pDB->query($q);
        //$e = mysqli_errno($pDB);
        //$eM = mysqli_error($pDB);
        //if (VERBOSE) fb(__FUNCTION__, $q, $e, $eM);
        return $result;
    }
    return false;
}
function dbRemove($pDB, $pEpisodio, $pTemporada, $pNome){
    //apagar anteriores
    $q = "delete from ".MYSQL_SCHEMA.".".MYSQL_TABLE_SERIE_POR_VER. " where episodio ='$pEpisodio' and temporada = '$pTemporada' and nome = '$pNome'";
    //dbAtualizarEpisodios($pDB, $pEpisodio, $pTemporada, $pNome);
    if ($pDB){
        $result = $pDB->query($q);
        //$e = mysqli_errno($pDB);
        //$eM = mysqli_error($pDB);
        //if (VERBOSE) fb(__FUNCTION__, $q, $e, $eM);
        return $result;
    }
    return false;
}

function dbEpisodiosPorVer($pDB, $pNome){
    $ret = array();
    $q= "select nome, url, temporada, episodio from ".MYSQL_SCHEMA.".".MYSQL_TABLE_SERIE_POR_VER. " where nome = '$pNome'";
    if ($pDB){
        $resultado = $pDB->query($q);
        $e = mysqli_errno($pDB);
        //$eM = mysqli_error($pDB);
        //if (VERBOSE) fb(__FUNCTION__, $q, $e, $eM);
        if ($resultado!==false && $e===0){
            $quantos = mysqli_num_rows($resultado);
            for ($idx = 0; $idx<$quantos ; $idx++){
                $registo = $resultado->fetch_assoc();
                $x[0] = $registo["nome"];
                $x[1] = $registo["url"];
                $x[2] = $registo["temporada"];
                $x[3] = $registo["episodio"];
                $ret[$idx] = $x;
            }//for

        }//if
    }
    return $ret;
}
/*
function removeSerie($pDB, $pNome){
    $q = "delete from".MYSQL_SCHEMA.".".MYSQL_TABLE_SERIE_POR_VER."where nome='$pNome'";
    if ($pDB){
        $result = $pDB->query($q);
        $e = mysqli_errno($pDB);
        $eM = mysqli_error($pDB);
        if (VERBOSE) fb(__FUNCTION__, $q, $e, $eM);
        return $result;
    }
    return false;
}*/

function serieExistente($pDB, $pNome){
    $q = "select nome from ".MYSQL_SCHEMA.".".MYSQL_TABLE_SERIE_POR_VER." where nome='$pNome'";
    if ($pDB){
        $result = $pDB->query($q);
        //$e = mysqli_errno($pDB);
        //$eM = mysqli_error($pDB);
        //if (VERBOSE) fb(__FUNCTION__, $q, $e, $eM);
        $quantos = mysqli_num_rows($result);
        if($quantos > 0){
            //echo $result;
            return "true";
        }
        else {
            return "false";
        }

    }
    //return $resultado;
}

function dbEpisodiosTodosPorVer($pDB){
    $ret = array();
    $q= "select nome, url, temporada, episodio from ".MYSQL_SCHEMA.".".MYSQL_TABLE_SERIE_POR_VER."";
    if ($pDB){
        $resultado = $pDB->query($q);
        $e = mysqli_errno($pDB);
        //$eM = mysqli_error($pDB);
        //if (VERBOSE) fb(__FUNCTION__, $q, $e, $eM);
        if ($resultado!==false && $e===0){
            $quantos = mysqli_num_rows($resultado);
            for ($idx = 0; $idx<$quantos ; $idx++){
                $registo = $resultado->fetch_assoc();
                $x[0] = $registo["nome"];
                $x[1] = $registo["url"];
                $x[2] = $registo["temporada"];
                $x[3] = $registo["episodio"];
                $ret[$idx] = $x;
            }//for

        }//if
    }
    return $ret;
}

function selectSerieVistos($pDb){
    $q = "select nome, temporada, episodio from " .MYSQL_SCHEMA. "." .MYSQL_TABLE_SERIE_VISTO. "";
    $ret = array();
    if($pDb){
        $result = $pDb->query($q);
        $e = mysqli_errno($pDb);
        //$eM = mysqli_error($pDb);
        //if (VERBOSE) fb(__FUNCTION__, $q, $e, $eM);
        if ($result!==false && $e===0){
            $quantos = mysqli_num_rows($result);
            for ($idx = 0; $idx<$quantos ; $idx++){
                $registo = $result->fetch_assoc();
                $x[0] = $registo["nome"];
                $x[2] = $registo["temporada"];
                $x[3] = $registo["episodio"];
                $ret[$idx] = $x;
            }//for

        }//if
    }
    return $ret;
}

function vistoPorNome($pDb, $pNome){
    $q = "select nome, url, temporada, episodio from " .MYSQL_SCHEMA. "." .MYSQL_TABLE_SERIE_POR_VER. " where nome='$pNome'";
    $ret = array();
    if($pDb){
        $result = $pDb->query($q);
        $e = mysqli_errno($pDb);
        //$eM = mysqli_error($pDb);
        //if (VERBOSE) fb(__FUNCTION__, $q, $e, $eM);
        if ($result!==false && $e===0){
            $quantos = mysqli_num_rows($result);
            for ($idx = 0; $idx<$quantos ; $idx++){
                $registo = $result->fetch_assoc();
                $x[0] = $registo["nome"];
                $x[1] = $registo["url"];
                $x[2] = $registo["temporada"];
                $x[3] = $registo["episodio"];
                $ret[$idx] = $x;
            }//for

        }//if
    }
    return $ret;

}

function removerPorVerPorNome($pDb, $pNome){
    $q = "delete from " .MYSQL_SCHEMA.".".MYSQL_TABLE_SERIE_POR_VER." where nome='$pNome';";
    if($pDb){
        $result = $pDb->query($q);
        //$e = mysqli_errno($pDb);
        //$eM = mysqli_error($pDb);
        //if (VERBOSE) fb(__FUNCTION__, $q, $e, $eM);

        return $result;
    }
    return false;

}

function removerVistoPorNome($pDb, $pNome){
    $q = "delete from " .MYSQL_SCHEMA.".".MYSQL_TABLE_SERIE_VISTO." where nome='$pNome';";
    if($pDb){
        $result = $pDb->query($q);
        //$e = mysqli_errno($pDb);
        //$eM = mysqli_error($pDb);
        //if (VERBOSE) fb(__FUNCTION__, $q, $e, $eM);

        return $result;
    }
    return false;

}

function episodiosPorVer($pDB, $pNome){
    $q = "select nome from ".MYSQL_SCHEMA.".".MYSQL_TABLE_SERIE_POR_VER." where nome='$pNome'";
    if ($pDB){
        $result = $pDB->query($q);
        //$e = mysqli_errno($pDB);
        //$eM = mysqli_error($pDB);
        //if (VERBOSE) fb(__FUNCTION__, $q, $e, $eM);
        $quantos = mysqli_num_rows($result);
        return $quantos;

    }
    return false;
}
