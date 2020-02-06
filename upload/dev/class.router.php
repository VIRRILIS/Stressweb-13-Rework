<?php

function router_clean($string) 
{	
	$string = str_replace( 'http://', '', strtolower( $string ) );
	$string = str_replace( 'https://', '', $string );
	if ( substr( $string, 0, 4 ) == 'www.' ) 
	{
		$string = substr( $string, 4 );
	}

	return $string;
}

while ( true ) 
{
	if (!defined( 'STRESSWEB' )) 
	{
		exit( 'Access denied...' );
	}


	if ( !is_dir( APPDIR ) )
	{
		throw new Exception( 'Incorrect application directory' );
	}

	$router_controller = router_clean( $app );
	$router_controller = APPDIR . $router_controller . '.php';

	if ( !file_exists( $router_controller ) ) 
	{
		$router_controller = APPDIR . 'main.php';
	}

	if ( file_exists( $router_controller ) ) 
	{
		include( $router_controller );		
		break;
	}

	throw new Exception( 'HTTP/1.0 404 Not Found' );
	break;
}

foreach ( $SWMODULES as $MODFILE ) 
{
	if ( file_exists( $module = MODULEDIR . $MODFILE . '.php' ) ) 
	{		
		include_once( $module );
		continue;
	}
}

?>