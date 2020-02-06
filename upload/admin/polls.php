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

	$_action_arr = array( 'edit', 'set' );
	$_action = (( isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], $_action_arr ) ) ? $controller->SafeData( $_REQUEST['action'], 3 ) : '');
	$_act = (isset( $_POST['act'] ) ? $controller->SafeData( $_REQUEST['act'], 3 ) : '');

	if ($_action == '') 
	{
		$polls = '';
		
		$sel = $db->query( 'SELECT id,title,date,poll_num,status FROM `stress_poll` ORDER BY date DESC' );

		if (0 < $db->num_rows( $sel )) 
		{
			$n = 0;
			
			$rows = $db->fetchall( $sel );
			
			if ( is_array( $rows ) )
			{
				foreach ( $rows as $row )
				{
					$n++;
					$pstatus = $row['status'] == 1 ? 'yes.png' : 'no.png';
					$polls .= '
						<tr class=\'jail\'>
							<td><b>' . $n . '</b> - ' . date( 'd/m/Y', $row['date'] ) . '</td>
							<td><i>' . $row['title'] . '</i></td>
							<td>' . $row['poll_num'] . '</td>
							<td><img src=\'' . TPLDIR .  '/' . $pstatus . '\'></td>
							<td align=\'right\'><a href=\'' . $_url . '=polls&action=edit&pid=' . $row['id'] . '\'><img src=\'' . TPLDIR . '/edit.png\' title=\'Редактировать\'></a> <input type=\'checkbox\' name=\'selected_polls[]\' value=\'' . $row['id'] . '\'></td>
						</tr>
					';
				}
			}
		} 
		else {
			$polls = '<tr><td colspan=\'6\'><div class=\'error\'>В базе данных нету новостей</div></td></tr>';
		}

		$polls_content = '<br /><div class=\'swbutton\'><a href="javascript:hide(\'addpoll\');">Добавить опрос</a></div><div class=\'clear\'></div><br />
<div id="addpoll" style="display:none;padding-top:1px;padding-bottom:2px;">
	<table width="100%" class="shadow">
	<tr>
   		<td bgcolor="#EFEFEF" height="29" style="padding-left:10px;">Создать новый опрос</td>
	</tr>
	</table><br />
	<form action="' . $_url . '=polls" method="post" >
		<input type="hidden" name="action" value="set" />
		<input type="hidden" name="act" value="add" />
	<table width="100%" cellpadding=\'0\' cellspacing=\'0\'>
	<tr>
		<td width="150">Вопрос: <a href="#" onMouseover="tooltip_show(\'poll_quest\',\'Укажите вопрос вашего голосования\')" onMouseout="tooltip_hide(\'poll_quest\')">[?]</a><div id="poll_quest" class="tooltip"></div></td>
		<td width="" style="padding:5px;"><input type="text" name="poll_quest" style="width: 350px;"></td>
	</tr>
	<tr>
		<td width="150">Варианты ответов: <a href="#" onMouseover="tooltip_show(\'poll_answ\',\'Перед каждым новым ответом нужно поставить *\')" onMouseout="tooltip_hide(\'poll_answ\')">[?]</a><div id="poll_answ" class="tooltip"></div></td>
		<td width="" style="padding:5px;"><textarea wrap="virtual" name="poll_answ" style="width: 350px; min-height: 100px"></textarea></td>
	</tr>
	<tr>
		<td width="150">Вопрос: <br /><small>(англ., необязательно)</small></td>
		<td width="" style="padding:5px;"><input type="text" name="poll_quest_en" style="width: 350px;"></td>
	</tr>
	<tr>
		<td width="150">Варианты ответов: <br /><small>(англ., необязательно)</small></td>
		<td width="" style="padding:5px;"><textarea wrap="virtual" name="poll_answ_en" style="width: 350px; min-height: 100px"></textarea></td>
	</tr>
	<tr>
		<td width="150">Статус: <a href="#" onMouseover="tooltip_show(\'poll_stat\',\'Если включено, опрос на сайте будет отключен и включен текущий опрос\')" onMouseout="tooltip_hide(\'poll_stat\')">[?]</a><div id="poll_stat" class="tooltip"></div></td>
		<td width="" style="padding:5px;"><input type="checkbox" name="poll_status"></td>
	</tr>
	<tr>
		<td colspan=\'2\'><input type=\'submit\' class=\'swbutton2 aleft\' value=\'Отправить\'></td>
	</tr>
	</table><br>
	</form>
</div>
	<form name=\'polls_list\' method=\'post\' action=\'' . $_url . '=polls\'>
		<input type="hidden" name="action" value="set" />
		<input type="hidden" name="act" value="del" />
	<table id=\'List\'>
	<tr class=\'header bold\'>
		<td width=\'120\'>Дата:</td>
		<td width=\'\'>Заголовок:</td>
		<td width=\'70\'>Голосов:</td>
		<td width=\'70\'>Статус:</td>
		<td width=\'45\'  align=\'right\'><input type=\'checkbox\' name=\'master_box\' title=\'Выбрать все\' onclick="javascript:check_uncheck_all(document.polls_list)"></td>
	</tr>
		' . $polls . '
	</table>
	<div style="text-align: right; padding-top: 5px;"><input type=\'submit\' value=\'Удалить\' class=\'swbutton2 aright\'></div>
	</form><br>';
	}


	if ($_action == 'edit') {	
		$pdata = $db->fetch( $db->query( 'SELECT `id`,`title`,`body`,`title_en`,`body_en`,`status` FROM `stress_poll` WHERE `id`=\'' . intval( $_GET['pid'] ) . '\'' ) );
		
		$pansw = explode( '|', $pdata['body'] );
		$i = 13;

		while ($i < count( $pansw )) {
			$pansw->$body .= '
';
			++$i;
		}

		
		$pansw = implode( '*', $pansw );
		
		$pansw_en = explode( '|', $pdata['body_en'] );
		$i = 13;

		while ($i < count( $pansw_en )) {
			$pansw_en->$body .= '
';
			++$i;
		}

		
		$pansw_en = implode( '*', $pansw_en );
		$pstatus = ($pdata['status'] == 1 ? 'checked=\'checked\'' : '');
		$polls_content = '<br /><table width="100%" border=\'0\' cellpadding=\'0\' cellspacing=\'0\' style="border: 1px solid #AAA;" class="shadow">
<tr>
    <td bgcolor="#EEEFEF" height="29" style="padding-left:10px;">Редактирование опроса</td>
</tr>
</table><br />

<form action="' . $_url . '=polls" method=\'post\' >
<input type="hidden" name="action" value="set" />
<input type="hidden" name="act" value="edit" />
<input type=\'hidden\' name=\'pid\' value=\'' . $pdata['id'] . '\' />
<table width="100%" cellpadding=\'0\' cellspacing=\'0\'>
<tr>
	<td width="150">Вопрос: <a href="#" onMouseover="tooltip_show(\'poll_quest\',\'Укажите вопрос вашего голосования\')" onMouseout="tooltip_hide(\'poll_quest\')">[?]</a><div id="poll_quest" class="tooltip"></div></td>
	<td width="" style="padding:5px;"><input type="text" value="' . $pdata['title'] . '" name="poll_quest" style="width: 350px;"></td>
</tr>
<tr>
	<td width="150">Варианты ответов: <a href="#" onMouseover="tooltip_show(\'poll_answ\',\'Перед каждым новым ответом нужно поставить *\')" onMouseout="tooltip_hide(\'poll_answ\')">[?]</a><div id="poll_answ" class="tooltip"></div></td>
	<td width="" style="padding:5px;"><textarea wrap="virtual" name="poll_answ" style="width: 350px; min-height: 100px;">' . $pansw . '</textarea></td>
</tr>
<tr>
	<td width="150">Вопрос: <br /><small>(англ.)</small></td>
	<td width="" style="padding:5px;"><input type="text" value="' . $pdata['title_en'] . '" name="poll_quest_en" style="width: 350px;"></td>
</tr>
<tr>
	<td width="150">Варианты ответов: <br /><small>(англ.)</small></td>
	<td width="" style="padding:5px;"><textarea wrap="virtual" name="poll_answ_en" style="width: 350px; min-height: 100px">' . $pansw_en . '</textarea></td>
</tr>
<tr>
	<td width="150">Статус: <a href="#" onMouseover="tooltip_show(\'poll_stat\',\'Если включено, опрос на сайте будет отключен и включен текущий опрос\')" onMouseout="tooltip_hide(\'poll_stat\')">[?]</a><div id="poll_stat" class="tooltip"></div></td>
	<td width="" style="padding:5px;"><input type="checkbox" ' . $pstatus . ' name="poll_status"></td>
</tr>
<tr>
	<td colspan=\'2\'><input type=\'submit\' class=\'swbutton2 aleft\' value=\'Сохранить\'></td>
</tr>
</table>
</form>';
	}


	if ($_action == 'set') {
		$polls_title = '';
		$polls_error = '';
		$back_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		if ($_act == 'add') {
			$body = str_replace( array( '', '' ), '', trim( $_POST['poll_answ'] ) );
			
			$body = str_replace( '*', '|', $body );
			
			$body_en = str_replace( array( '', '' ), '', trim( $_POST['poll_answ_en'] ) );
			
			$body_en = str_replace( '*', '|', $body_en );
			$status = (isset( $_POST['poll_status'] ) ? '1' : '0');

			if ($status == 1) {
				$db->query( 'UPDATE `stress_poll` SET `status`=\'0\' WHERE `status`=\'1\'' );
			}

			$db->query( 'INSERT INTO `stress_poll` SET `title`=\'' . $db->safe( $_POST['poll_quest'] ) . '\', `date`=\'' . $db->safe( time(  ) ) . '\', `body`=\'' . $db->safe( $body ) . '\', `status`=\'' . $db->safe( $status ) . '\', `title_en`=\'' . $db->safe( $_POST['poll_quest_en'] ) . '\', `body_en`=\'' . $db->safe( $body_en ) . '\' ' );

			if ($db->affected(  ) == 1) {
				$polls_error = '<div class=\'no_error\'>Опрос успешно добавлен</div>';
			} 
else {
				$polls_error = '<div class=\'error\'>Ошибка</div>';
			}

			$polls_title = 'Добавление опроса';
		}


		if ($_act == 'edit') {
			
			$body = str_replace( array( '', '' ), '', $_POST['poll_answ'] );			
			$body = str_replace( '*', '|', $body );			
			$body_en = str_replace( array( '', '' ), '', $_POST['poll_answ_en'] );			
			$body_en = str_replace( '*', '|', $body_en );
			$status = (isset( $_POST['poll_status'] ) ? '1' : '0');

			if ($status == 1) {
				$db->query( 'UPDATE `stress_poll` SET `status`=\'0\' WHERE `status`=\'1\'' );
			}

			$db->query( 'UPDATE `stress_poll` SET `title`=\'' . $db->safe( $_POST['poll_quest'] ) . '\',`body`=\'' . $db->safe( $body ) . '\',`title_en`=\'' . $db->safe( $_POST['poll_quest_en'] ) . '\',`body_en`=\'' . $db->safe( $body_en ) . '\',`status`=\'' . $db->safe( $status ) . '\' WHERE `id`=\'' . $db->safe( $_POST['pid'] ) . '\'' );

			if ($db->affected(  ) == 1) {
				$polls_error = '<div class=\'no_error\'>Опрос успешно изменен</div>';
			} 
else {
				$polls_error = '<div class=\'error\'>Ошибка</div>';
			}

			$polls_title = 'Редактирование опроса';
		}


		if ($_act == 'del') {
			$deleted = 12;
			$selected_polls = $_POST['selected_polls'];

			if (is_array( $selected_polls )) {
				foreach ($selected_polls as $pollsId) {
					$pollsId = '';
					
					$query = $db->query( ( 'DELETE FROM `stress_poll` WHERE `id`=\'' . $pollsId . '\'' ) );
					$db->affected(  );
					$deleted += $pollsId ;
					
					$query = $db->query( ( 'DELETE FROM `stress_poll_logs` WHERE `pid`=\'' . $pollsId . '\'' ) );
				}
			}

			$polls_error = '<div class=\'no_error\'>Удалено опросов: <b>' . $deleted . '</b></div>';
			$polls_title = 'Удаление опросов';
		}

		$polls_content = '<br /><table width="100%" border=\'0\' cellpadding=\'0\' cellspacing=\'0\' style="border: 1px solid #AAA;" class="shadow">
<tr>
    <td bgcolor="#EEEFEF" height="29" style="padding-left:10px; ">' . $polls_title . '</td>
</tr>
</table><br />
    ' . $polls_error . '
	<center><a href="' . $back_url . '">Назад</a></center>';
	}

	$tpl->SetResult( 'content', $polls_content );
?>