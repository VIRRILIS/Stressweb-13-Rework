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

define( 'HTTP_HOME_URL', $l2cfg['siteurl'] );
define( 'TIMEZONE', ($l2cfg['timezone'] * 60) );
define( 'TPLDIR', HTTP_HOME_URL."/templates/{$l2cfg["template"]}" );
/**
 * ==============================
 * Загрузка интерфейса
 * ==============================
 */
if ( isset($_COOKIE['swlang']) and in_array($_COOKIE['swlang'], array('ru', 'en')) ) {
    $l2cfg['lang'] = $_COOKIE['swlang'];
    if ( $_COOKIE['swlang'] == 'en' and file_exists(ROOT_DIR.'templates'.DS.$l2cfg['template'].'_en') ) {
        $l2cfg['template'] .= '_en';
    }
}
require_once ROOT_DIR.'lang'.DS.$l2cfg["lang"].'.php';
/**
 * ==============================
 * Site offline init
 * ==============================
 */
if ( $l2cfg["offline"]["enable"] and !Functions::isAdmin() ) {
    echo ( "<html>\n<head>\n<title>{$l2cfg["title"]}</title>\n<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>\n</head>\n<body style='background:#ddfec6;'>\n<div align='center' style='padding-top: 200px;'>\n\t<div style='width: 500px; padding: 5px; background: #d95757; text-align: justify; border: 1px solid #000;'>{$l2cfg["offline"]["reason"]}</div>\n</div>\n</body>\n</html>\n<!-- 2008-2012 © STRESS WEB, http://stressweb.ru -->\n<!-- {version R13} -->" );
    exit;
}
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
$lsList = array();

for ( $i = 1; $i <= $l2cfg["ls"]["count"]; $i++ ) {
    if ( $l2cfg["ls"][$i]["on"] )
        $lsList[$i] = $i;
}

for ( $i = 1; $i <= $l2cfg["gs"]["count"]; $i++ ) {
    if ( $l2cfg["gs"][$i]["on"] )
        $gsList[$i] = $i;
}

$sid = ( isset($_REQUEST['sid']) and in_array(intval($_REQUEST['sid']), $gsList) ) ? intval( $_REQUEST['sid'] ):reset( $gsList );
$lid = in_array( $l2cfg["gs"][$sid]["ls"], $lsList ) ? intval( $l2cfg["gs"][$sid]["ls"] ):reset( $lsList );

$app = isset( $_REQUEST['f'] ) ? strval( $_REQUEST['f'] ):'';
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