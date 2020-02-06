<?php
/**
 * STRESS WEB
 * @author S.T.R.E.S.S.
 * @copyright 2008 - 2012 STRESS WEB
 * @version 13
 * @web http://stressweb.ru
 */
header( "Content-type: text/html; charset=utf-8" );
header( "Cache-Control: no-cache" );
header( "Pragma: nocache" );
define( 'STRESSWEB', true );
require_once str_replace( 'ajax', '', dirname(__file__) )."config".DIRECTORY_SEPARATOR."config.l2cfg.php";
function report( $text )
{
    echo json_encode( $text );
    exit;
}
$is_ajax = ( isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ) ? true:false;
if ( isset($_POST["order"]) and $l2cfg["rb"]["enable"] and $is_ajax) {
    $id = intval( $_POST["order"] );
    $invid = intval( $_POST["inv"] );
    $sid = intval( $_POST["shpa"] );
    $rnd = preg_match( "/^[0-9A-Z]{8}+$/", $_POST["shpb"] ) ? $_POST["shpb"]:report( array('code' => 1, 'msg' => 'Error: Ошибка данных') );
    $col = intval( $_POST["col"] );
    $OutSumm = $col * $l2cfg["gs"][$sid]["rb"]["sum"];

    if ( !$link = mysql_connect($l2cfg["gs"][$sid]["dbhost"], $l2cfg["gs"][$sid]["dbuser"], $l2cfg["gs"][$sid]["dbpass"]) )
        report( array('code' => 2, 'msg' => 'Error: No connect') );
    if ( !$db = mysql_select_db($l2cfg["gs"][$sid]["dbname"], $link) )
        report( array('code' => 3, 'msg' => 'Error: No db') );
    mysql_query( "UPDATE stress_auto_rb SET OutSum='{$OutSumm}',OutCount='{$col}',stage='P',comment='To Pay' WHERE id='{$id}' AND InvId='{$invid}' AND RND='{$rnd}'", $link );
    $aff = mysql_affected_rows( $link );
    @mysql_close( $link );
    if ( $aff != -1) {
        $sign = md5( "{$l2cfg["rb"]["mrhlogin"]}:$OutSumm:$invid:{$l2cfg["rb"]["mrhpass1"]}:shpa={$sid}:shpb=$rnd" );

        report( array('code' => 10, 'login' => $l2cfg["rb"]["mrhlogin"], 'outsum' => $OutSumm, 'desc' => $l2cfg["rb"]["invdesc"], 'sign' => $sign) );
    } else
        report( array('code' => 4, 'msg' => 'Error: Ошибка базы данных') );
}
?>