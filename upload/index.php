<?php

ob_start();
session_start();
error_reporting( E_ALL );
define( 'TIMER_START', microtime( true ) );
define( 'STRESSWEB', true );
define( 'DS', DIRECTORY_SEPARATOR );
define( 'DEVELOP', 'STRESSWEB' );
define( 'ROOT_DIR', realpath( dirname( __FILE__ ) ) . DS );
define( 'APPDIR', ROOT_DIR . 'application' . DS );
define( 'CONFDIR', ROOT_DIR . 'config' . DS );
define( 'DEVDIR', ROOT_DIR . 'dev' . DS );
define( 'MODULEDIR', ROOT_DIR . 'module' . DS );
define( 'L2J', ROOT_DIR . 'l2j' . DS );

if ( !file_exists( CONFDIR . 'lock.php' ) ) 
{
	header( 'Location: install.php' );
}

require( DEVDIR . 'boot.php' );

?>