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

$tpl->SetResult( 'copyright', $l2cfg["copy"] );
if ( !$l2cfg['nocopy'] ) {
    $tpl->SetResult( 'copyright', "<br /><span class='swc'>2008-2012 © <a href='http://stressweb.ru'>STRESSWEB</a></span>" );
}
?>