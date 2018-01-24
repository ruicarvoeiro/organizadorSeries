<?php
define ("MYSQL_USER", "heimerdinger");
define ("MYSQL_PASS", "#123456A");
define ("MYSQL_HOST", "localhost");
define ("MYSQL_PORT", 3306);
define ("MYSQL_SCHEMA", "schema_organizadorSeries");
define ("MYSQL_CREATE_SCHEMA", "create schema ".MYSQL_SCHEMA.";");
define ("MYSQL_TABLE_SERIE_POR_VER", "por_ver");
define ("MYSQL_TABLE_SERIE_VISTO", "visto");
define ("VERBOSE", 1); //por defeito ativo
define ("MYSQL_CREATE_TABLE_SERIE_POR_VER",
    "CREATE TABLE `".MYSQL_SCHEMA."`.`".MYSQL_TABLE_SERIE_POR_VER."` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(64) NOT NULL,
  `url` VARCHAR(512) NOT NULL,
  `temporada` VARCHAR(10) NOT NULL,
  `episodio` VARCHAR(10) NOT NULL,
  PRIMARY KEY (`id`));
");
define ("MYSQL_CREATE_TABLE_SERIE_VISTO",
    "CREATE TABLE `".MYSQL_SCHEMA."`.`".MYSQL_TABLE_SERIE_VISTO."` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(64) NOT NULL,
  `temporada` VARCHAR(10) NOT NULL,
  `episodio` VARCHAR(10) NOT NULL,
  PRIMARY KEY (`id`));
");
$INSTALL_PROCEDURE_POR_VER = [MYSQL_CREATE_SCHEMA, MYSQL_CREATE_TABLE_SERIE_POR_VER];
$INSTALL_PROCEDURE_VISTO = [MYSQL_CREATE_SCHEMA, MYSQL_CREATE_TABLE_SERIE_VISTO];
//------------------------------------------------------------
function dbConnect ($pHost = MYSQL_HOST, $pUser = MYSQL_USER, $pPwd = MYSQL_PASS, $pPort = MYSQL_PORT){
    $db =  mysqli_connect($pHost, $pUser, $pPwd);
    $e = mysqli_connect_errno();
    //$eM = mysqli_connect_error();
    //fb(__FUNCTION__, $msg = "", $e, $eM);
    return $e===0 ? $db : false;
}//dbConnect
//------------------------------------------------------------
function dbInstaller($pDB, $pInstallProcedure){
    $errosAdmissiveis = [0, 1007, 1050];
    if ($pDB){
        foreach ($pInstallProcedure as $i){
            $result = $pDB->query($i);
            $e = mysqli_errno($pDB);
            //$eM = mysqli_error($pDB);
            $bErroAdmissivel = array_search($e, $errosAdmissiveis) !== false;
            //if (VERBOSE && !$bErroAdmissivel) fb(__FUNCTION__, $i, $e, $eM);
            if (!$bErroAdmissivel) return false;
        }
        return true;
    }//if
    return false;
}//dbInstaller
//------------------------------------------------------------
function fb($pQuemFazChamada, $pMsg, $pErro, $pMsgErro){
    $msg = sprintf("caller: %s\nmsg: %s\ne: %d\neM: %s\n\n", $pQuemFazChamada, $pMsg, $pErro, $pMsgErro);
    @ob_end_flush();
    echo $msg;
    @ob_start();
}//fb
//------------------------------------------------------------
$db = dbConnect();
//$resultadoTrueEmSucessoFalseEmFailure = dbInstaller($db, $INSTALL_PROCEDURE_VISTO);
//$resultadoTrueEmSucessoFalseEmFailure .= dbInstaller($db, $INSTALL_PROCEDURE_POR_VER);
$db->close();