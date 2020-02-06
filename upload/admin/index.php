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


	if (( !$controller->isAdmin() || !defined( 'DEVELOP' ) )) {
		$controller->redirect( 'index.php' );
		exit(  );
	}


	if (( isset( $_REQUEST['act'] ) && $_REQUEST['act'] == 'clear' )) {
		$controller->ClearCache(  );
		$controller->redirect( ADMFILE );
	}

	$res = '';
	if (( isset( $_GET['get'] ) && $_GET['get'] == 'license' )) {
		require_once( ROOT_DIR . 'build.php' );
		//$LicInfo = $controller->LicInfo();
		$res .= '<br />
<table width=\'100%\' border=\'0\' cellpadding=\'0\' cellspacing=\'0\' style=\'border: 1px solid #AAA;\' class=\'shadow\'>
<tr>
    <td bgcolor=\'#EEEFEF\' height=\'29\' style=\'padding-left:10px;\' >Лицензионная информация</td>
</tr>
</table><br />
<table cellpadding=\'4\' cellspacing=\'4\' width=\'100%\' id=\'List\'>
<tr>
	<td><b>Установленная версия: <span style=\'color:#009;\'>' . ${@revision} . '</span></b></td>
</tr>
<tr>
	<td><b>Актуальная версия:</b> Stress Web 13</td>
</tr>
<tr>
	<td><b>Доменное имя:</b> <b><span style=\'color:red;\'>НЕ ПРИВЯЗАН</span></b><u></u></td>
</tr>
<tr>
	<td><b>Срок действия до:</b> <b><span style=\'color:green;\'>ПОЖИЗНЕННО</span></b><u></u></td>
</tr>
</table>';
	} 
else {
		$offline = ($l2cfg['offline']['enable'] ? '<font color=\'#f00\'>Выключен</font>' : '<font color=\'#55f\'>Включен</font>');
		$php =  phpversion(  );
		$dbversion = $db->fetch( $db->query( 'SELECT VERSION()' ) )[0];
		
		if (function_exists( 'apache_get_modules' )) {
			if (array_search( 'mod_rewrite', apache_get_modules(  ) )) {
				$mod_rewrite = '<font color=\'#55f\'>Включен</font>';
			} 
else {
				$mod_rewrite = '<font color=\'#f00\'>Выключен</font>';
			}
		} 
else {
			$mod_rewrite = '<font color=\'#f00\'>Неопределено</font>';
		}

		$reg_globals = (@ini_get( 'register_globals' ) == 1 ? '<font color=\'red\'>Включено</font>' : '<font color=\'green\'>Выключено</font>');
		$memory = (@ini_get( 'memory_limit' ) != '' ? '<font color=\'#55f\'>' . @ini_get( 'memory_limit' ) . '</font>' : '<font color=\'red\'>Неопределено</font>');

		if (( extension_loaded( 'gd' ) && function_exists( 'gd_info' ) )) {
			$gdinfo = gd_info(  );
			$gd = '<font color=\'green\'>Активна</font> ' . $gdinfo['GD Version'];
		} 
else {
			$gd = '<font color=\'#f00\'>Неопределено</font>';
		}

		$cache_size = $controller->FormatSize( $controller->DirSize( ROOT_DIR . 'cache' ) );
		$res .= '<br />
<table cellpadding=\'4\' cellspacing=\'4\' width=\'100%\' id=\'List\'>
<tr>
	<td width=\'150\'>Режим работы сайта:</td>
	<td><i>' . $offline . '</i></td>
</tr>
<tr>
	<td width=\'150\'>Версия PHP:</td>
	<td><i><font color=\'#55f\'>' . $php . '</font></i></td>
</tr>
<tr>
	<td>Версия MySQL:</td>
	<td><i><font color=\'#55f\'>' . $dbversion . '</font></i></td>
</tr>
<tr>
	<td>mod_rewrite:</td>
	<td><i>' . $mod_rewrite . '</i></td>
</tr>
<tr>
	<td>register_globals:</td>
	<td><i>' . $reg_globals . '</i></td>
</tr>
<tr>
	<td>memory_limit:</td>
	<td><i>' . $memory . '</i></td>
</tr>
<tr>
	<td>Библиотека GD:</td>
	<td><i>' . $gd . '</i></td>
</tr>
<tr>
	<td>Общий размер кеша:</td>
	<td>' . $cache_size . ' <input type=\'button\' onclick="javascript: location.href=\'' . ADMFILE . '?act=clear\'" class=\'inbutton\' value=\'Очистить кеш\'></td>
</tr>
</table>';
	}

	$tpl->SetResult( 'content', $res );
?>