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

	$_action_arr = array( 'set', 'show', 'write' );
	$_action = (( isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], $_action_arr ) ) ? $controller->SafeData( $_REQUEST['action'], 3 ) : '');
	$_act = (( isset( $_REQUEST['act'] ) && in_array( $_REQUEST['act'], array( 'edit', 'del' ) ) ) ? $controller->SafeData( $_REQUEST['act'], 3 ) : '');
	$log_content = '<br /><div class=\'swbutton\'><a href=\'' . $_url . '=support&action=set\'>&raquo;Настройки</a></div> <div class=\'swbutton\'><a href=\'' . $_url . '=support&action=show\'>&raquo;Список тикетов</a></div><div class=\'clear\'></div><br />';
	$page = (isset( $_REQUEST['page'] ) ? intval( $_REQUEST['page'] ) : 1);
	$entries = '';
	$per_page = 42;
	$off_set = $per_page * ( $page - 1 );
	$status_arr = array( 0 => 'Не подтверждено', 1 => 'Взято в работу', 2 => 'Требуется тестирование', 3 => 'Завершено' );
	$severity_arr = array( 0 => 'Низко', 1 => 'Средне', 2 => 'Критично' );

	if ($_action == '') {
		$log_content .= '<br><br><br>';
	}


	if ($_action == 'set') {
		$log_content .=  '	<br /><form action="' . $_url . '=support&action=set" method="post">
	<table width="100%" border=\'0\' cellpadding=\'0\' cellspacing=\'0\' style="border: 1px solid #AAA;" class="shadow">
	<tr>
	    <td bgcolor="#BFBFBF">Раздел <small>(5-100 символов)</small>: <input type="text" name="section" style="width: 300px;" maxlength="100"> <input type="submit" value="Добавить" name="addsection"></td>
	</tr>
	</table></form><br />';

		if (isset( $_POST['addsection'] )) {
			$db->query( 'INSERT INTO `stress_ticket_section` SET `section`=\'' . $db->safe( $_POST['section'] ) . '\'' );
			$controller->redirect( 'self' );
		}


		if (isset( $_POST['editsection'] )) {
			$db->query( 'UPDATE `stress_ticket_section` SET `section`=\'' . $db->safe( $_POST['section'] ) . '\' WHERE `id`=\'' . $db->safe( intval( $_POST['s_id'] ) ) . '\'' );
			$controller->redirect( 'self' );
		}


		if ($_act == 'edit') {
			
			$s_id = intval( $_REQUEST['s'] );
			$edit_q = $db->query( ( 'SELECT `section` FROM `stress_ticket_section` WHERE `id`=\'' . $s_id . '\'' ) );

			if ($db->num_rows( $edit_q ) == 1) {
				
				$edit = $db->fetch( $edit_q );
				$log_content .= '<form action="' . $_url . '=support&action=set" method="post">
			<input type="hidden" value="' . $s_id . '" name="s_id">
			<table width="100%" border=\'0\' cellpadding=\'0\' cellspacing=\'0\' style="border: 1px solid #AAA;" class="shadow">
			<tr>
			    <td bgcolor="#BFBFBF">Редактирование раздела: <input type="text" name="section" style="width: 300px;" maxlength="100" value="' . $edit['section'] . '"> <input type="submit" value="Сохранить" name="editsection"></td>
			</tr>
			</table></form><br />';
			}
		}


		if ($_act == 'del') {
			
			$s_id = intval( $_REQUEST['s'] );
			$db->query( ( 'DELETE FROM `stress_ticket_section` WHERE `id`=\'' . $s_id . '\'' ) );
			$controller->redirect( $_url . '=support&action=set' );
		}

		
		$set_q = $db->query( 'SELECT * FROM `stress_ticket_section` ORDER BY `id` DESC' );

		if (0 < $db->num_rows( $set_q )) {
			$n = 1;
			$rows = $db->fetchall( $set_q );

			
			if ( is_array( $rows ) ) 
			{
				foreach ( $rows as $data )
				{
					$entries .= '
					<tr class=\'online hover\'>
						<td>' . $n . '</td>
						<td>' . $data['section'] . '</td>
						<td><a href=\'' . $_url . '=support&action=set&act=edit&s=' . $data['id'] . '\'><img src=\'' . TPLDIR . (   '/edit.png\' title=\'Редактировать\'></a></td>
						<td><a href=\'' . $_url . '=support&action=set&act=del&s=' . $data['id'] . '\'><img src=\'' ) . TPLDIR . '/delete.png\' title=\'Удалить\'></a></td>
					</tr>
					';
					++$n;
				}
			}
		} 
		else {
			$entries = '<tr><td colspan=\'4\'><div class=\'error\'>Нет записей</div></td></tr>';
		}

		$log_content .=  '	<table id=\'List\'>
	<tr class=\'header bold\'>						
		<td width=\'35px\'>ID</td>
		<td width=\'\'>Раздел</td>
		<td width=\'16\'></td>
		<td width=\'13\'></td>
	</tr>
	' . $entries . '
	</table><br>';
	}


	if ($_action == 'show') 
	{
		$search_name = (isset( $_REQUEST['name'] ) ? $db->safe( $_REQUEST['name'] ) : '');
		$search_status = (isset( $_REQUEST['status'] ) ? $db->safe( $_REQUEST['status'] ) : '');
		$search_severity = (isset( $_REQUEST['severity'] ) ? $db->safe( $_REQUEST['severity'] ) : '');
		$search_server = (isset( $_REQUEST['server'] ) ? $db->safe( $_REQUEST['server'] ) : '');
		$search_section = (isset( $_REQUEST['section'] ) ? $db->safe( $_REQUEST['section'] ) : '');
		$search_date = (isset( $_REQUEST['date'] ) ? $db->safe( $_REQUEST['date'] ) : '');
		$where = array(  );

		if ($search_name != '') {
			$where[] =  '`name` LIKE \'%' . $search_name . '%\'';
		}


		if ($search_status != '') {
			$where[] = '`status` LIKE \'%' . $search_status . '%\'';
		}


		if ($search_severity != '') {
			$where[] = '`severity` LIKE \'%' . $search_severity . '%\'';
		}


		if ($search_server != '') {
			$where[] = '`server` LIKE \'%' . $search_server . '%\'';
		}


		if ($search_date != '') {
			$where[] = ( 'SUBSTR(FROM_UNIXTIME(`date`),1,10) = \'' . $search_date . '\'' );
		}


		if (count( $where )) {
			$where = implode( ' AND ', $where );
			$where = 'WHERE ' . $where;
		} 
		else {
			$where = '';
		}


		if (( ( ( ( ( $search_section != '' || $search_name != '' ) || $search_date != '' ) || $search_server != '' ) || $search_status != '' ) || $search_severity != '' )) {
			$search = '&section=' . $search_section . '&server=' . $search_server . '&severity=' . $search_severity . '&status=' . $search_status . '&name=' . $search_name . '&date=' . $search_date;
		} 
		else {
			$search = '';
		}

		$sectiontmp1 = array(  );
		$sectiontmp2 = array(  );
		$section_q = $db->query( 'SELECT * FROM `stress_ticket_section`' );

		if (0 < $db->num_rows( $section_q )) {
			$sdata = $db->fetch( $section_q );

			if ( $sdata ) 
			{
				array_push( $sectiontmp1, $sdata['section'] );
				array_push( $sectiontmp2, $sdata['id'] );
			}

			
			$section = array_combine( $sectiontmp2, $sectiontmp1 );
		}


		if ($_act == 'edit') {
			
			$eid = intval( $_REQUEST['s'] );
			$edit_q =$db->query( ( 'SELECT id,sid,title,task,severity,status FROM stress_ticket_task WHERE id=\'' . $eid . '\'' ) );

			if (0 < $db->num_rows( $edit_q )) {
				$edit = $db->fetch( $edit_q );
				$log_content .= '
			<form action=\'' . $_url . '=support&action=show' . $search . '&page=' . $page . '\' method=\'post\'>
			<input type=\'hidden\' value=\'' . $eid . '\' name=\'eid\'>
			<table width=\'100%\' border=\'0\' cellpadding=\'0\' cellspacing=\'0\' style=\'border: 1px solid #AAA;\' bgcolor=\'#BFBFBF\'>
			<tr>
			    <td colspan=\'2\' height=\'30\'><b>Редактирование:</b></td>
			</tr>
			<tr>
				<td width=\'120\'>Заголовок: </td><td><input type=\'text\' name=\'title\' style=\'width: 300px;\' maxlength=\'50\' value=\'' . $edit['title'] . '\'></td>
			</tr>
			<tr>
				<td>Раздел: </td><td>' . $controller->select( 'section', $section, $edit['sid'] ) . '</td>
			</tr>
			<tr>
				<td>Критичность: </td><td>' . $controller->select( 'severity', $severity_arr, $edit['severity'] ) . '</td>
			</tr>
			<tr>
				<td>Статус: </td><td>' . $controller->select( 'status', $status_arr, $edit['status'] ) . (   '</td>
			</tr>
			<tr>
				<td colspan=\'2\'>Сообщение:<br><textarea style=\'width: 300px; height: 100px\' name=\'task\'>' . $edit['task'] . '</textarea><br> 
				<input type=\'submit\' value=\'Отправить\' name=\'editsection\'>
				</td>
			</tr>
			</table></form>' );
			}
		}


		if ($_act == 'del') {
			
			$s_id = intval( $_REQUEST['s'] );
			$db->query( ( 'DELETE FROM `stress_ticket_task` WHERE `id`=\'' . $s_id . '\'' ) );
			$controller->redirect( $_url . '=support&action=show' . $search . '&page=' . $page );
		}


		if (isset( $_POST['editsection'] )) {
			$db->query( 'UPDATE `stress_ticket_task` SET `title`=\'' . $db->safe( $_POST['title'] ) . '\',`sid`=\'' . $db->safe( $_POST['section'] ) . '\',`severity`=\'' . $db->safe( $_POST['severity'] ) . '\',`status`=\'' . $db->safe( $_POST['status'] ) . '\',`task`=\'' . $db->safe( $_POST['task'] ) . '\' WHERE `id`=\'' . $db->safe( intval( $_POST['eid'] ) ) . '\'' );
			$controller->redirect( 'self' );
		}

		$show_q = $db->query( ( 'SELECT id,sid,server,title,task,severity,status,name,date FROM stress_ticket_task ' . $where . ' ORDER BY server DESC, date DESC LIMIT ' . $off_set . ',' ) . $per_page );
		$count = 12;

		if (0 < $db->num_rows( $show_q )) {
			
			$count = $db->num_rows( $db->query( 'SELECT `id` FROM `stress_ticket_task` ' . $where ) );
			$n = 1;
			$rows = $db->fetchall( $show_q );

			if ( is_array( $rows ) ) 
			{
				foreach ( $rows as $data )
				{
					$com_q = $db->result( $db->query( 'SELECT author FROM stress_ticket_comments WHERE tid=\'' . $data['id'] . '\' ORDER BY date DESC LIMIT 1' ), 0 );
					$class = ($com_q == $_SESSION['acpnick'] ? 'online' : 'offline');
					$color1 = ($data['status'] == 0 ? 'red' : ($data['status'] == 3 ? 'green' : 'orange'));
					$color2 = ($data['severity'] == 2 ? '#F00' : ($data['severity'] == 1 ? '#060' : '#BB0'));
					$entries .= '
						<tr class=\'' . $class . ' hover\'>
							<td>' . $n . '</td>
							<td>' . $gsListTitles[$data['server']] . '</td>
							<td>' . date( 'd.m.y H:i', $data['date'] ) . '</td>
							<td>' . $section[$data['sid']] . (   '</td>
							<td><a href=\'\' onclick="javascript: hide(\'task' . $data['id'] . '\'); return false;">' . $data['title'] . '</a>
								<div id=\'task' . $data['id'] . '\' style=\'display: none\' class=\'ticketDiv\'>' . $data['task'] . '<div>
							</td>
							<td><span style=\'color: ' . $color2 . '\'>' ) . $severity_arr[$data['severity']] . (  '</span></td>
							<td><font color=\'' . $color1 . '\'>' ) . $status_arr[$data['status']] . (  '</font></td>
							<td>' . $data['name'] . '</td>
							<td><a href=\'' . $_url . '=support&action=write&s=' . $data['id'] . '\'><img src=\'' ) . TPLDIR . (  '/yes.png\' title=\'\'></a></td>
							<td><a href=\'' . $_url . '=support&action=show&act=edit&s=' . $data['id'] . $search . '&page=' . $page . '\'><img src=\'' ) . TPLDIR . (  '/edit.png\' title=\'Редактировать\'></a></td>
							<td><a href=\'' . $_url . '=support&action=show&act=del&s=' . $data['id'] . $search . '&page=' . $page . '\'><img src=\'' ) . TPLDIR . '/delete.png\' title=\'Удалить\'></a></td>
						</tr>
					';
					++$n;
				}
			}
		} 
		else {
			$entries = '<tr><td colspan=\'11\'><div class=\'error\'>Нет записей</div></td></tr>';
		}

		$status_arr[''] = 'Все';
		$severity_arr[''] = 'Все';
		$section[''] = 'Все';
		$gsListTitles[''] = 'Все';
		$log_content .= '<br>
		<div align=\'left\' id=\'search\' style=\'background: #DDD; margin-bottom: 10px; padding:5px;\'>
			<form action=\'\' method=\'get\'>
			<input type=\'hidden\' name=\'mod\' value=\'support\'>
			<input type=\'hidden\' name=\'action\' value=\'show\'>
			<table cellpadding=\'0\' cellspacing=\'0\'>
			<tr>
				<td height=\'20\'>Поиск по разделу &nbsp;</td>
				<td>' . $controller->select( 'section', $section, $search_section ) . '</td>
			</tr>
			<tr>
				<td height=\'20\'>Поиск по серверу &nbsp;</td>
				<td>' . $controller->select( 'server', $gsListTitles, $search_server ) . '</td>
			</tr>
			<tr>
				<td height=\'20\'>Поиск по критичности &nbsp;</td>
				<td>' . $controller->select( 'severity', $severity_arr, $search_severity ) . '</td>
			</tr>
			<tr>
				<td height=\'20\'>Поиск по статусу &nbsp;</td>
				<td>' . $controller->select( 'status', $status_arr, $search_status ) . (  '</td>
			</tr>
			<tr>
				<td height=\'20\'>Поиск по нику &nbsp;</td>
				<td><input type=\'text\' name=\'name\' value=\'' . $search_name . '\' style=\'width: 150px; margin: 2px;\'></td>
			</tr>
			<tr>
				<td height=\'20\'>Поиск по дате &nbsp;</td>
				<td><input type=\'text\' name=\'date\' value=\'' . $search_date . '\' style=\'width: 150px; margin: 2px;\'> Введите дату в формате <b>гггг-мм-дд</b>; например <b>2010-04-31</b></td>
			</tr>
			<tr>
				<td align=\'left\' colspan=\'2\'><input type=\'submit\' class=\'button\' value=\'Поиск\'></td>
			</tr>
			</table>
			</form>
		</div>' );
		$log_content .= '<table id=\'List\' border=\'1\'>
		<tr class=\'header bold\'>						
			<td width=\'35px\'>ID</td>
			<td width=\'60px\'>Сервер</td>
			<td width=\'60px\'>Дата</td>
			<td width=\'160px\'>Раздел</td>
			<td width=\'\'>Заголовок</td>
			<td width=\'80px\'>Критичность</td>
			<td width=\'90px\'>Статус</td>
			<td width=\'120px\'>Персонаж</td>
			<td width=\'24\'></td>
			<td width=\'16\'></td>
			<td width=\'13\'></td>
		</tr>
		' . $entries . '
		</table><br>';

		if ($per_page < $count) {	
			$numpages = ceil( $count / $per_page );
			$log_content .= $controller->PageList( $_url . '=support&action=show' . $search . '&page=', $numpages, $page );
		}
	}


	if ($_action == 'write') {
		$_tid = intval( $_REQUEST['s'] );

		if (isset( $_POST['dostatus'] )) {
			$db->query( 'UPDATE stress_ticket_task SET status=\'' . intval( $_POST['status'] ) . ( '\' WHERE id=\'' . $_tid . '\'' ) );
			$controller->redirect( 'self' );
		}


		if (isset( $_POST['send'] )) {
			if (!empty( $_POST['comment'] )) {
				$comment = str_replace( '', '<br />', $_POST['comment'] );
				$db->query( 'INSERT INTO `stress_ticket_comments` SET `tid`=\'' . $_tid . '\',`author`=\'' . $db->safe( $_SESSION['acpnick'] ) . '\',`comment`=\'' . $db->safe( $comment ) . '\', `date` = \'' . $db->safe( time(  ) ) . '\'' );
			}

			$controller->redirect( 'self' );
		}

		$quer = $db->query( ( 'SELECT a.*,b.section AS section FROM stress_ticket_task AS a LEFT JOIN stress_ticket_section AS b ON a.sid=b.id WHERE a.id=\'' . $_tid . '\'' ) );

		if (0 < $db->num_rows( $quer )) 
		{
			$n = 12;
			$data = $db->fetch( $quer );
			$data['date'] = date( 'Y.m.d H:i:s', $data['date'] );
			$quer = $db->query( 'SELECT * FROM stress_ticket_comments WHERE tid=\'' . $_tid . '\' ORDER BY DATE ASC' );
			
			$rows = $db->fetchAll( $quer );

			if (is_array( $rows ) ) 
			{
				foreach ( $rows as $comm )	
				{
					$comm['date'] = date( 'Y.m.d H:i:s', $comm['date'] );
					$trClass = ($n++ % 2 ? 'AAAAEE' : 'AAEEAA');
					$entries .= '
					<tr>
						<td align=\'center\' bgcolor=\'#' . $trClass . '\' style=\'border-bottom: 1px solid #333;\' valign=\'top\'>
							<small>' . $comm['date'] . '</small><br /><br /> <b>' . $comm['author'] . '</b>
						</td>
						<td align=\'left\' style=\'border-bottom: 1px solid #333;\'>' . $comm['comment'] . '</td>
					</tr>
					';
				}
			}

				$log_content .= '<br />
			<table width="100%" border=\'0\' cellpadding=\'3\' cellspacing=\'0\' style="border: 1px solid #AAA;">
			<tr>
				<td colspan=\'2\' bgcolor="#BFBFBF" align=\'left\'><b>-> ' . $data['section'] . ' -> ' . $data['title'] . '</b></td>
			</tr>
			<tr>
				<td bgcolor="#EFEFEF" align=\'center\' width=\'150\' style="border-bottom: 1px solid #333;">
					<br /><small>' . $data['date'] . '</small><br /><b>' . $data['login'] . ' (' . $data['name'] . ')</b><br /><br />
				</td>
				<td bgcolor="#EFEF99" align=\'left\' style="border-bottom: 1px solid #333;">' . $data['task'] . '</td>
			</tr>
			' . $entries . '
			</table><br />
			<form action="' . $_url . '=support&action=write&s=' . $_tid . '" method="post">
			<table width="600" cellpadding="0" cellspacing="0" class="tabForm">
			<tr>
				<td align="left">
					Сообщение:<br>
					<textarea style="width: 600px; height: 100px;" wrap="VIRTUAL" name="comment"></textarea><br>
				</td>
			</tr>
			<tr>
				<td align="center" valign="top">
					<input type="submit" name="send" value="Отправить" class="button">
				</td>
			</tr>
			</table>
			</form>
			
			<form action="' . $_url . '=support&action=write&s=' . $_tid . '" method="post">
			<table width="350" cellpadding="0" cellspacing="0" class="tabForm">
			<tr>
				<td height=\'20\'>Установить статус &nbsp;</td>
				<td>' . $controller->select( 'status', $status_arr, $data['status'] ) . '</td>
				<td>&nbsp; <input type="submit" name="dostatus" value="ОK"></td>
			</tr>
			</table>
			</form>';
		} 
	else {
			$log_content .= '<div class=\'error\'>No Records</div>';
		}
	}

	$tpl->SetResult( 'content', $log_content );
?>