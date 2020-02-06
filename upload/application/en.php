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

$lang_tmppos = strrpos( $_SERVER["PHP_SELF"], "/" ) + 1;
$lang_path = substr( $_SERVER["PHP_SELF"], 0, $lang_tmppos );
setcookie( "swlang", 'en', time() + 2592000, $lang_path );
$controller->redirect( $l2cfg['siteurl'] );

?>