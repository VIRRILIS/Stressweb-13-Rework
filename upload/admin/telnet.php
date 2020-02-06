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

	function Telnet($sendmsg) {
		global $l2cfg;
		global $sid;

		@fsockopen( $l2cfg['gs'][$sid]['host'], $l2cfg['gs'][$sid]['telnet']['port'], $errno, $errstr, $l2cfg['gs'][$sid]['telnet']['timeout'] );

		if ($telnet = ) {
			fputs( $telnet, $l2cfg['gs'][$sid]['telnet']['pass'] );
			fputs( $telnet, '' );

			if (!empty( $l2cfg['gs'][$sid]['telnet']['gmname'] )) {
				fputs( $telnet, $l2cfg['gs'][$sid]['telnet']['gmname'] );
				fputs( $telnet, '' );
			}

			fputs( $telnet, $sendmsg );
			fputs( $telnet, 'exit' );

			if ($telnet) {
				fclose( $telnet );
				$echo = '<fieldset><legend>OK</legend>';
				$echo .= '<div class=\'warning\'> &nbsp; Комманда успешно отправлена.</div>';
				$echo .= '</fieldset><br>';
			} 
			else {
				$echo = '<fieldset><legend>Error</legend>';
				$echo .= '<div class=\'warning\'> &nbsp; Ошибка при отправке команды (возможно неверный пароль).</div>';
				$echo .= '</fieldset><br>';
			}
		} 
		else {
			$echo = '<fieldset><legend>Error</legend>';
			$echo .= (  . '<div class=\'warning\'> Невозможно подключиться к серверу через Telnet ' . $l2cfg['gs'][$sid]['host'] . ':' ) . $l2cfg['gs'][$sid]['telnet']['port'] . '</div>';
			$echo .= '</fieldset><br>';
		}

		return $echo;
	}


	if (!defined( 'STRESSWEB' )) {
		exit( 'Access denied...' );
	}


	if (( !$controller->isAdmin(  ) || !defined( 'DEVELOP' ) )) {
		$controller->redirect( 'index.php' );
	}

	$_action = (isset( $_REQUEST['action'] ) ? $controller->SafeData( $_REQUEST['action'], 3 ) : '');
	$telnet_msg = '';

	if (empty( $l2cfg['gs'][$sid]['host'] )) {
		$telnet_msg = '<fieldset><legend>Ошибка</legend>';
		$telnet_msg .= '<div class=\'warning\'> &nbsp; Не задан адрес telnet сервера</div>';
		$telnet_msg .= '</fieldset>';
	} 
	else {
		if (empty( $$_action )) {
			if ($_action == 'announce') {
				Telnet( 'announce ' . $_REQUEST['msg'] );
				$telnet_msg = ;
			} 
			else {
				if ($_action == 'msg') {
					Telnet( 'msg ' . $_REQUEST['nick'] . ' ' . iconv( 'UTF8', 'UTF-8', $_REQUEST['msg'] ) );
					$telnet_msg = ;
				} 
				else {
					if ($_action == 'kick') {
						Telnet( 'kick ' . $_REQUEST['nick'] );
						$telnet_msg = ;
					} 
					else {
						if ($_action == 'restart') {
							Telnet( 'restart ' . $_REQUEST['time'] );
							$telnet_msg = ;
						} 

						else {
							if ($_action == 'shutdown') {
								Telnet( 'shutdown ' . $_REQUEST['time'] );
								$telnet_msg = ;
							} 
							else {
								if (!empty( $$_action )) {
									$telnet_msg .= '<fieldset><legend>Error</legend>';
									$telnet_msg .= '<div class=\'warning\'> &nbsp; Неизвестная комманда</div>';
									$telnet_msg .= '</fieldset><br>';
								}
							}
						}
					}
				}
			}
		}
	}

	$controller->select( 'sid', $gsListTitles, $sid, 'style="width: 100px;" onchange="javascript: document.sid.submit(); return false;"' );
	$select_server = ;
	$telnet_content =  . '<br /><table width="100%" border=\'0\' cellpadding=\'0\' cellspacing=\'0\' style="border: 1px solid #AAA;" class=\'shadow\'>
<tr>
    <td bgcolor="#EEEFEF" height="29" style="padding-right:10px; color: #888;" align="right" valign="middle">
		<form action="" method="GET" id="sid" name="sid">
		<input type="hidden" name="mod" value="telnet">
		Сервер: ' . $select_server . '
		</form>
	</td>
</tr>
</table><br />
<table width="100%" cellpadding=\'0\' cellspacing=\'0\' class=\'shadow\'>
<tr>
	<td bgcolor="#DDEFEF" height="29" style="padding-left:10px; color: #888; border: 1px solid #AAA;">
		<a href="javascript:ChangeOption(\'divAnnounce\');"> &raquo;Announce</a> &nbsp;
		<a href="javascript:ChangeOption(\'divMsg\');"> &raquo;ПМ</a> &nbsp;
		<a href="javascript:ChangeOption(\'divKick\');"> &raquo;Kick</a> &nbsp;
		<a href="javascript:ChangeOption(\'divRestart\');"> &raquo;Рестарт сервера</a> &nbsp;
		<a href="javascript:ChangeOption(\'divShutdown\');"> &raquo;Выключение сервера</a> &nbsp;
	</td>
</tr>	
<tr>
    <td style="padding:5px;" bgcolor="#FFFFFF">
		' . $telnet_msg . '
		<div id="dle_tabView1">
			<div id="divAnnounce" style="" >
			<form action=\'' . $_url . '=telnet&action=announce&sid=' . $sid . '\' method=\'POST\'>
				<b>Сообщение</b> <input type=\'text\' name=\'msg\'> <br />
				<input type=\'submit\' value=\'Отправить\' class=\'swbutton2 aleft\'>							
			</form>
			</div>
					
			<div id="divMsg" style="display:none" >
			<form action=\'' . $_url . '=telnet&action=msg&sid=' . $sid . '\' method=\'POST\'>
				<b>Ник игрока</b> <input type=\'text\' name=\'nick\'> <br /><br />
				<b>Сообщение</b> <input type=\'text\' name=\'msg\'> <br />
				<input type=\'submit\' value=\'Отправить\' class=\'swbutton2 aleft\'>
			</form>
			</div>
					
			<div id="divKick" style="display:none" >
			<form action=\'' . $_url . '=telnet&action=kick&sid=' . $sid . '\' method=\'POST\'>
				<b>Ник игрока</b> <input type=\'text\' name=\'nick\'> <br />
				<input type=\'submit\' value=\'Отправить\' class=\'swbutton2 aleft\'>
			</form>
			</div>
				
			<div id="divRestart" style="display:none" >
				<form action=\'' . $_url . '=telnet&action=restart&sid=' . $sid . '\' method=\'POST\'>
				<b>Время до рестарта</b> (сек) <input type=\'text\' name=\'time\' size=\'16\'> <br />
				<input type=\'submit\' value=\'Отправить\' class=\'swbutton2 aleft\'>
			</form>
			</div>
			
			<div id="divShutdown" style="display:none" >
			<form action=\'' . $_url . '=telnet&action=shutdown&sid=' . $sid . '\' method=\'POST\'>
				<b>Время до выключения</b> (сек) <input type=\'text\' name=\'time\' size=\'16\'> <br />
				<input type=\'submit\' value=\'Отправить\' class=\'swbutton2 aleft\'>
			</form>
			</div>
		</div>
	</td>
</tr>
</table>
<script type="text/javascript">
function ChangeOption(selectedOption) 
{
	document.getElementById(\'divAnnounce\').style.display = "none";
	document.getElementById(\'divMsg\').style.display = "none";
	document.getElementById(\'divKick\').style.display = "none";
	document.getElementById(\'divRestart\').style.display = "none";
	document.getElementById(\'divShutdown\').style.display = "none";
	if(selectedOption == \'divAnnounce\') {document.getElementById(\'divAnnounce\').style.display = "";}
	if(selectedOption == \'divMsg\') {document.getElementById(\'divMsg\').style.display = "";}
	if(selectedOption == \'divKick\') {document.getElementById(\'divKick\').style.display = "";}
	if(selectedOption == \'divRestart\') {document.getElementById(\'divRestart\').style.display = "";}
	if(selectedOption == \'divShutdown\') {document.getElementById(\'divShutdown\').style.display = "";}	
}
</script>';
	$tpl->SetResult( 'content', $telnet_content );
?>