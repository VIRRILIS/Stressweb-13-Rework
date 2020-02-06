<?php

if (!defined( 'STRESSWEB' )) 
{
	exit( 'Access denied...' );
}

include( CONFDIR . 'config.db.php' );
include( CONFDIR . 'config.l2cfg.php' );
include( DEVDIR . 'cfg.default.php' );
include( DEVDIR . 'class.view.php' );
include( DEVDIR . 'class.db.php' );
include( DEVDIR . 'class.mail.php' );
include( DEVDIR . 'class.la2.php' );
include( DEVDIR . 'class.functions.php' );
include( DEVDIR . 'class.controller.php' );
include( DEVDIR . 'class.init.php' );

define( 'SCRIPT', 'index' );

@header( 'Content-type: text/html; charset=utf-8' );
@header( 'Last-Modified: ' . @gmdate( 'D, d M Y H:i:s', @strtotime( '-1 day' ) ) . ' GMT' );
@header( 'Cache-Control: no-store, no-cache, must-revalidate' );
@header( 'Expires: 0' );
@header( 'Pragma: no-cache' );

$_SW_TEMPLATE = 'index';
$tpl = View::getInstance();
//$tpl = new View();
$tpl->SetViewPath( $l2cfg['template'] );
$controller = new Controller();
include( DEVDIR . 'class.router.php' );
$tpl->LoadView( $_SW_TEMPLATE );
$tpl->Set( 'title', $l2cfg['title'] );
$tpl->Set( 'headers', 
	'<script type=\'text/javascript\' src=\'' . TPLDIR . '/js/jquery.js\'></script><script type=\'text/javascript\' src=\'' . TPLDIR . '/js/stressweb.js\'></script>' 
);
$tpl->Set( 'info', $tpl->GetResult( 'info' ) );
$tpl->Set( 'content', $tpl->GetResult( 'content' ) );
foreach ( $SWMODULES as $MODTAG ) 
{
	$tpl->Set( $MODTAG, $tpl->GetResult( $MODTAG ) );
}

$tpl->Set( 'timer', round( microtime( true ) - TIMER_START, 5 ) );
$tpl->Build( 'index' );
$tpl->Display( 'index' );

if ( $db ) 
{
	$db->close();
}

if ( isset( $ldb ) && is_array( $ldb ) && count( $ldb ) ) 
{
	foreach ( $ldb as $db ) 
	{
		$db->close();
	}
}

if ( isset( $gdb ) && is_array( $gdb ) && count( $gdb ) ) 
{
	foreach ( $gdb as $db ) 
	{
		$db->close();
	}
}

?>