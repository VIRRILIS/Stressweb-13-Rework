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


	if (( !$controller->isAdmin(  ) || !defined( 'DEVELOP' ) )) {
		$controller->redirect( 'index.php' );
	}

	$admins_content = '<br /><table width="100%" border=\'0\' cellpadding=\'0\' cellspacing=\'0\' style="border: 1px solid #AAA;" class=\'shadow\'>
<tr>
    <td bgcolor="#EEEFEF" height="29" style="padding-left:10px;">Список Администраторов</td>
</tr>
</table><br />';

	if (isset( $_POST['aid'] )) {
		$db->query( 'DELETE FROM `stress_admin` WHERE `id`=\'' . $db->safe( intval( $_POST['aid'] ) ) . '\'' );
		$controller->redirect( $_url . '=admins' );
	}


	if (isset( $_POST['save_my'] )) {
		$old_pass = $_POST['a_old_pass'];
		$new_pass1 = $_POST['a_new_pass1'];
		$new_pass2 = $_POST['a_new_pass2'];

		if (( ( $old_pass == '' || $new_pass1 == '' ) || $new_pass2 == '' )) {
			$admins_content .= '<div class=\'error\'>Заполните все поля</div>';
		} 
else {
			if ($new_pass1 != $new_pass2) {
				$admins_content .= '<div class=\'error\'>Новые пароли не совпадают</div>';
			} 
else {
				
				$old_pass_enc = $controller->PassEncode( $db->safe( $old_pass ) );
				$db_pass = $db->fetch( $db->query( 'SELECT `login`,`password` FROM `stress_admin` WHERE `login`=\'' . $_SESSION['acplogin'] . '\'' ) )[1];
				
				$db_login = [0];

				if ($db_pass != $old_pass_enc) {
					$admins_content .= '<div class=\'error\'>Старый пароль введен неверно</div>';
				} 
else {
					
					$new_pass_enc = $controller->PassEncode( $db->safe( $new_pass1 ) );
					$db->query( 'UPDATE `stress_admin` SET `password`=\'' . $new_pass_enc . '\' WHERE `login`=\'' . $_SESSION['acplogin'] . '\'' );
					$admins_content .= '<div class=\'no_error\'>Старый пароль \'' . $old_pass . '\' удачно изменен на новый \'' . $new_pass1 . '\'</div>';
				}
			}
		}
	}


	if (isset( $_POST['save_new'] )) {
		$new_adm_login = $db->safe( $_POST['anew_login'] );
		$new_adm_nick = $db->safe( $_POST['anew_nick'] );
		$new_adm_pass1 = $controller->PassEncode( $db->safe( $_POST['anew_pass1'] ) );
		$new_adm_pass2 = $controller->PassEncode( $db->safe( $_POST['anew_pass2'] ) );

		if (( ( ( $new_adm_login == '' || $new_adm_nick == '' ) || $new_adm_pass1 == '' ) || $new_adm_pass2 == '' )) {
			$admins_content .= '<div class=\'error\'>Заполните все поля формы</div>';
		} 
else {
			if ($new_adm_pass1 != $new_adm_pass2) {
				$admins_content .= '<div class=\'error\'>Пароли не совпадают</div>';
			} 
else {
				
				$db_adm_login = $db->query( 'SELECT `login` FROM `stress_admin` WHERE `login`=\'' . $new_adm_login . '\'' );

				if (0 < $db->num_rows( $db_adm_login )) {
					$admins_content .= '<div class=\'error\'>Логин уже существует.</div>';
				} 
else {
					$db->query( ( 'INSERT INTO `stress_admin` SET `login` = \'' . $new_adm_login . '\', `password` = \'' . $new_adm_pass1 . '\', `nick` = \'' . $new_adm_nick . '\'' ) );
					$admins_content .= '<div class=\'no_error\'>Новый администратор \'' . $new_adm_login . '\' (' . $new_adm_nick . ') с паролем \'' . $_POST['anew_pass1'] . '\' удачно добавлен</div>';
				}
			}
		}
	}

	
	$sel_admins = $db->query( 'SELECT `id`,`login`,`nick` FROM `stress_admin`' );

	if ($db->num_rows( $sel_admins ) == 0) {
		$adm_table = '<div class=\'error\'>В базе данных нет администраторов</div>';
	} 
	else {
		$adm_table = '<table cellpading=\'0\' cellspacing=\'0\' width=\'250\' class=\'shadow\'>
		<tr>
			<td></td>
			<td><b>login</b></td>
			<td><b>nick</b></td>
			<td></td>
		</tr>';
		
		$n = 1;
		$adms = $db->fetchall( $sel_admins );
	
		if ( is_array( $adms ) ) 
		{
			foreach ( $adms as $adm )
			{
				$adm_table .= '
				<tr>
					<td>' . $n . '</td>
					<td>' . $adm['login'] . '</td>
					<td>' . $adm['nick'] . ('</td>
					<td><form action=\'' . $_url . '=admins\' method=\'post\'><input type=\'hidden\' name=\'aid\' value=\'' . $adm['id'] . '\'><input type=\'image\' src=\'' ) . TPLDIR . '/delete.png\' title=\'Удалить\'></form></td>
				</tr>';
				++$n;
			}
		}

		$adm_table .= '</table>';
	}

	$admins_content .= '
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td width="100%" valign="top" colspan="2">
		' . $adm_table . '
	</td>
</tr>
<tr>
	<td width="50%"><br /><br />
		<form action="' . $_url . '=admins&act=save" method="post">
			<table width="300" cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="2" align="center"><b>Изменить свой пароль</b></td>
			</tr>
			<tr>
				<td width="150">Старый пароль</td><td><input type="password" name="a_old_pass" style="width: 150px;"></td>
			</tr>
			<tr>
				<td>Новый пароль</td><td><input type="password" name="a_new_pass1" style="width: 150px;"></td>
			</tr>
			<tr>
				<td>Новый пароль</td><td><input type="password" name="a_new_pass2" style="width: 150px;"></td>
			</tr>
			<tr>
				<td colspan="2" align="center"><input type="hidden" value="1" name="save_my"><input type="submit" value="Сохранить" class="button"></td>
			</tr>
			</table>
		</form>
	</td>
	<td width="50%"><br /><br />
		<form action="' . $_url . '=admins&act=savenew" method="post">
			<table width="300" cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="2" align="center"><b>Добавить нового Администратора</b></td>
			</tr>
			<tr>
				<td width="150">Логин</td><td><input type="text" name="anew_login" style="width: 150px;"></td>
			</tr>
			<tr>
				<td width="150">Ник</td><td><input type="text" name="anew_nick" style="width: 150px;"></td>
			</tr>
			<tr>
				<td>Пароль</td><td><input type="password" name="anew_pass1" style="width: 150px;"></td>
			</tr>
			<tr>
				<td>Пароль</td><td><input type="password" name="anew_pass2" style="width: 150px;"></td>
			</tr>
			<tr>
				<td colspan="2" align="center"><input type="hidden" value="1" name="save_new"><input type="submit" value="Добавить" class="button"></td>
			</tr>
			</table>
		</form>
	</td>
</tr>
</table><br>';
	$tpl->SetResult( 'content', $admins_content );
?>