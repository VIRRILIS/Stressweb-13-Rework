<?php
/**
*
* @ IonCube v8.3 Loader By DoraemonPT
* @ PHP 5.3
* @ Decoder version : 1.0.0.7
* @ Author     : DoraemonPT
* @ Release on : 09.05.2014
* @ Website    : http://EasyToYou.eu
*
**/

	if (!defined( 'STRESSWEB' )) {
		exit( 'Access denied...' );
	}

	define( 'DEVELOP', 'STRESSWEB' );
	require( CONFDIR . 'config.iptable.php' );
	include( CONFDIR . 'config.db.php' );
	include( CONFDIR . 'config.l2cfg.php' );
	include( DEVDIR . 'cfg.default.php' );
	require( DEVDIR . 'class.iptable.php' );
	include( DEVDIR . 'class.view.php' );
	include( DEVDIR . 'class.db.php' );
	include( DEVDIR . 'class.la2.php' );
	include( DEVDIR . 'class.functions.php' );
	include( DEVDIR . 'class.admin.php' );
	include( DEVDIR . 'class.init.admin.php' );
?>