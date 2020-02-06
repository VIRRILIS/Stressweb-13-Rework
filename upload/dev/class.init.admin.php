<?php
/**
 * STRESS WEB
 * @author S.T.R.E.S.S.
 * @copyright 2008 - 2012 STRESS WEB
 * @version 13
 * @web http://stressweb.ru
 */
if ( !defined("STRESSWEB") )
    die( "Access denied..." );

/**
 * ==============================
 * Проверка доступа в Админку по IPtable
 * ==============================
 */
IPtable::Instance();
/**
 * ==============================
 * Constants
 * ==============================
 */
if ( empty($l2cfg['siteurl']) )
    define( 'HTTP_HOME_URL', rtrim('http://'.@getenv("HTTP_HOST").reset(explode(ADMFILE, $_SERVER['PHP_SELF'])), '/') );
else
    define( 'HTTP_HOME_URL', $l2cfg['siteurl'] );
define( 'TIMEZONE', ($l2cfg['timezone'] * 60) );
define( 'TPLDIR', HTTP_HOME_URL.'/'.ADMINDIR.'/skin' );
/**
 * ==============================
 * Загрузка интерфейса
 * ==============================
 */
require_once ROOT_DIR.'lang'.DS.'ru.php';
/**
 * ==============================
 * Site DataBase connect
 * ==============================
 */
$db = new db( DBHOST, DBUSER, DBPASS, DBNAME, true );
/**
 * ==============================
 * Server variables
 * ==============================
 */
$gsList = array();
$gsListTitles = array();
$lsList = array();
for ( $i = 1; $i <= $l2cfg["ls"]["count"]; $i++ ) {
    $lsList[$i] = $i;
}
for ( $i = 1; $i <= $l2cfg["gs"]["count"]; $i++ ) {
    $gsListTitles[$i] = $l2cfg["gs"][$i]["title"];
    $gsList[$i] = $i;
}
$sid = ( isset($_REQUEST["sid"]) and isset($l2cfg["gs"][$_REQUEST["sid"]]) ) ? intval( $_REQUEST["sid"] ):1;
//$lid = in_array($l2cfg["gs"][$sid]["ls"], $lList) ? intval($l2cfg["gs"][$sid]["ls"]) : 0;
$lid = ( isset($_REQUEST["lid"]) and in_array($_REQUEST["lid"], $lsList) ) ? intval( $_REQUEST["lid"] ):1;

$app = isset( $_REQUEST['mod'] ) ? strval( $_REQUEST['mod'] ):'';
$page = isset( $_REQUEST["page"] ) ? abs( intval($_REQUEST["page"]) ):1;
/**
 * ==============================
 * Load servers SQL
 * ==============================
 */
for ( $i = 1; $i <= count($vList); $i++ ) {
    include_once L2J.'l2j_'.strtolower( $vList[$i] ).'.php';
}

$vls = $vList[$l2cfg["ls"][$lid]["version"]];
$vgs = $vList[$l2cfg["gs"][$sid]["version"]];
?>