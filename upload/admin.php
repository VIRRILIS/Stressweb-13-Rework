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

	session_start(  );
	error_reporting( 0 );
	define( 'STRESSWEB', true );
	define( 'ADMINDIR', 'admin' );
	define( 'ADMFILE', 'admin.php' );
	define( 'DS', DIRECTORY_SEPARATOR );
	define( 'ROOT_DIR', realpath( dirname( __FILE__ ) ) . DS );
	define( 'PATHDIR', ROOT_DIR . ADMINDIR . DS );
	define( 'CONFDIR', ROOT_DIR . 'config' . DS );
	define( 'DEVDIR', ROOT_DIR . 'dev' . DS );
	define( 'SCRIPT', 'admin' );
	define( 'L2J', ROOT_DIR . 'l2j' . DS );
	require( DEVDIR . 'boot.admin.php' );
	@header( 'Content-type: text/html; charset=utf-8' );
	@header( 'Last-Modified: ' . @gmdate( 'D, d M Y H:i:s', @strtotime( '-1 day' ) ) . ' GMT' );
	@header( 'Cache-Control: no-store, no-cache, must-revalidate' );
	@header( 'Expires: 0' );
	@header( 'Pragma: no-cache' );
	$tpl = View::getInstance();
	$tpl->SetViewPathAdmin();
	$controller = new Admin();
	
	@ini_set( 'display_errors', '1' );
@error_reporting( E_ALL );

if ($controller->isAdmin()) { 
		$_url = ADMFILE . '?mod';
		$tpl->LoadView( 'index' );
		$tpl->Set( 'index', ADMFILE );
		$tpl->Set( 'login', $controller->sess_get( 'acplogin' ) );
		
		switch ($app) {
			case 'settings': {
				$module = 'settings.php';
				$title = 'Настройки';
				break;
			}

			case 'polls': {
				$module = 'polls.php';
				$title = 'Опросы';
				break;
			}

			case 'static': {
				$module = 'static.php';
				$title = 'Статические страницы';
				break;
			}

			case 'news': {
				$module = 'news.php';
				$title = 'Управление новостями';
				break;
			}

			case 'admins': {
				$module = 'admins.php';
				$title = 'Администраторы';
				break;
			}

			case 'accounts': {
				$module = 'accounts.php';
				$title = 'Аккаунты';
				break;
			}

			case 'chars': {
				$module = 'chars.php';
				$title = 'Персонажи';
				break;
			}

			case 'support': {
				$module = 'support.php';
				$title = 'Обратная связь';
				break;
			}

			case 'telnet': {
				$module = 'telnet.php';
				$title = 'Telnet';
				break;
				break;
			}
			default:
				$module = 'index.php';
				$title = 'Главная';
		}


		/*while (true) {
			$module = 'index.php';
			$title = 'Главная';
			break;
		}*/

		$tpl->Set( 'title', $title );

		if (file_exists( PATHDIR . $module )) {
			include( PATHDIR . $module );
		} 
	else {
			$tpl->SetResult( 'content', '<div class=\'error\'>Ошибка 404. Страница не найдена</div>' );
		}

		$tpl->Set( 'content', $tpl->GetResult( 'content' ) );
		$tpl->Set( 'copyright', '2008-2012 © <a href="http://stressweb.ru">STRESS WEB</a>' );
		$tpl->Build( 'admin' );
} else { 
		$tpl->LoadView( 'login' );

		if ($l2cfg['captcha']['admin_type'] == 'sw') {
			$tpl->Block( 'code' );
			$tpl->Block( 'recode', false );
		}


		if ($l2cfg['captcha']['admin_type'] == 'recaptcha') {
			$tpl->Block( 'code', false );
			$tpl->Block( 'recode' );
			$tpl->Set( 'recaptcha', '
            <script type="text/javascript">
 				var RecaptchaOptions = {
    				theme : \'white\'
 				};
 			</script>
			<script type="text/javascript"
		       src="http://www.google.com/recaptcha/api/challenge?k=' . $l2cfg['captcha']['publickey'] . '">
		    </script>
		    <noscript>
		       <iframe src="http://www.google.com/recaptcha/api/noscript?k=' . $l2cfg['captcha']['publickey'] . '"
		           height="300" width="500" frameborder="0"></iframe><br>
		       <textarea name="recaptcha_challenge_field" rows="3" cols="40">
		       </textarea>
		       <input type="hidden" name="recaptcha_response_field"
		           value="manual_challenge">
		    </noscript>' );
		}

		$tpl->Build( 'admin' );
	}

	$tpl->Display( 'admin' );

	if (is_array( $db )) {
		$db->close(  );
	}


	if (isset( $ldb ) && is_array( $ldb )) {
		foreach ($ldb as $ldb_close) {
			$ldb_close->close(  );
		}
	}


	if ( isset( $gdb ) && is_array( $gdb )) {
		foreach ($gdb as $gdb_close) {
			$gdb_close->close(  );
		}
	}

?>