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

require_once str_replace( 'ajax', '', dirname(__file__) )."config" . DIRECTORY_SEPARATOR . "config.db.php";

$is_ajax = ( isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ) ? true:false;

//обработка запроса
if ( isset($_POST) && $is_ajax ) 
{
    $_lang = ( isset($_COOKIE['swlang']) and $_COOKIE['swlang'] == 'en' ) ? '_en':'';
    $pid = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
    $ansval = isset( $_POST['val'] ) ? intval( $_POST['val'] ) : 0;

    $link = @mysqli_connect( DBHOST, DBUSER, DBPASS ) or die( 'No connect' );
    if ( $link )
        $db = @mysqli_select_db( $link, DBNAME ) or die( 'No db' );

    if ( $db ) 
	{
        @mysqli_query( $link, "SET NAMES utf8"  );
        if ( $ansval != 0 ) {
            $check = @mysqli_query( $link, "SELECT `id` FROM `stress_poll_logs` WHERE `ip`='".mysqli_real_escape_string( $link, $_SERVER['REMOTE_ADDR'])."' AND `pid`='{$pid}' " );
            if ( @mysqli_num_rows($check) == 0 ) {
                @mysqli_query( $link, "UPDATE `stress_poll` SET `poll_num`=`poll_num`+1 WHERE `id`='{$pid}'" );
                @mysqli_query( $link, "INSERT INTO `stress_poll_logs` (`pid`,`answ`,`name`,`ip`) VALUES ('{$pid}','{$ansval}','voter','".mysqli_real_escape_string( $link, $_SERVER['REMOTE_ADDR'])."')" );
            }
        }

        $sel = @mysqli_query( $link, "SELECT `id`,`poll_num`,`body`,`body_en` FROM `stress_poll` WHERE `id`='{$pid}'" );
        $data = mysqli_fetch_array( $sel, MYSQLI_ASSOC );
		
		if ( empty($data['body_en']) )
            $data['body_en'] = $data['body'];
        $answers = array_filter( explode("|", $data['body'.$_lang]) );
        $content = "";
        $keys = array_keys( $answers );
        foreach ( $keys as $key ) 
		{
            $ans_num = 0;
            $perc = 0;
            if ( $data['poll_num'] > 0 ) {
                $ans_num = @mysqli_num_rows( @mysqli_query($link, "SELECT `answ` FROM `stress_poll_logs` WHERE `pid`='{$pid}' AND `answ`='{$key}'") );		
				$perc = 100 * round( $ans_num / $data['poll_num'], 2 );
            }
            $p_perc = ( $perc == 0 ) ? 1:$perc;
            $content .= "<div class='panswer' align='left'><i>{$answers[$key]}</i> {$perc}% ({$ans_num})</div>";
            $content .= "<div class='pprogress' align='left'><img src='sysimg/poll.png' height='10' width='{$p_perc}'></div>";
        }
        @mysqli_close( $link );
        echo $content;
    }
}

?>