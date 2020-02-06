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

$_static = $controller->SafeData( $app, 3 );
$_lang = (( isset( $_COOKIE['swlang'] ) && $_COOKIE['swlang'] == 'en' ) ? '_en' : '');

if ( empty( $_static ) ) 
{
	if ( $l2cfg['main']['page']['static'] ) 
	{
		$select = $db->query( 'SELECT * FROM `stress_static` WHERE `s_name`=\'' . $db->Safe( $l2cfg['main']['page']['name'] ) . '\'' );

		if ( $db->num_rows( $select )>0 ) 
		{
			$data = $db->fetch( $select );

			if ( empty( $data['s_title_en'] ) ) 
			{
				$data['s_title_en'] = $data['s_title'];
			}

			if ( empty( $data['s_content_en'] ) ) 
			{
				$data['s_content_en'] = $data['s_content'];
			}

			$tpl->LoadView( 'static' );
			$tpl->Set( 'content', $data['s_content' . $_lang] );
			$tpl->Set( 'title', $data['s_title' . $_lang] );
			$tpl->Build( 'content' );
			return 1;
		}

		throw new Exception( 'HTTP/1.0 404 Not Found' );
		return 1;
			 
			echo $e->getMessage();
			exit();
			return 1;		
	}

	include( APPDIR . 'news.php' );
} 
else 
{
	$select = $db->query( 'SELECT * FROM `stress_static` WHERE `s_name`=\'' . $db->Safe( $_static ) . '\'' );

	if ($db->num_rows( $select )>0) 
	{
		$data = $db->fetch( $select );

		if (empty( $data['s_title_en'] )) 
		{
			$data['s_title_en'] = $data['s_title'];
		}


		if (empty( $data['s_content_en'] )) {
			$data['s_content_en'] = $data['s_content'];
		}

		$tpl->LoadView( 'static' );
		$tpl->Set( 'content', $data['s_content' . $_lang] );
		$tpl->Set( 'title', $data['s_title' . $_lang] );
		$tpl->Build( 'content' );
	}
}	

?>