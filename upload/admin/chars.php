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

	$debug = false;
	$select_server = $controller->select( 'sid', $gsListTitles, $sid, 'style="width: 100px;" onchange="javascript: document.serv.submit(); return false;"' );
	
	$l2_content = '<br /><table width="100%" border=\'0\' cellpadding=\'0\' cellspacing=\'0\' style="border: 1px solid #AAA;" class=\'shadow\'>
<tr>
    <td bgcolor="#EEEFEF" height="29" style="padding-left:10px;">Управление персонажами</td>
    <td bgcolor="#EEEFEF" height="29" style="padding-right:10px;" align="right" valign="middle">
		<form action="" method="GET" id="serv" name="serv">
		<input type="hidden" name="mod" value="chars">
		Сервер: ' . $select_server . '
		</form>
	</td>
</tr>
</table><br />';
	$server = (0 < $sid ? '&sid=' . $sid : '');
	$_action = (isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '');
	$_act = (isset( $_REQUEST['act'] ) ? $_REQUEST['act'] : '');
	$db->ldb( $lid );
	$db->gdb( $sid );

	if ($_action == 'account') {
		$account = $ldb[$lid]->safe( $_REQUEST['acc'] );
		$l2_content .= '<table width="100%" border=\'0\' cellpadding=\'0\' cellspacing=\'0\' style="border: 1px solid #AAA;">
<tr>
    <td colspan="2" bgcolor="#DDEFEF" height="29" style="padding-left:10px; color: #888; border: 1px solid #AAA;">Просмотр аккаунта <b>' . $account . '</b></td>
</tr>
</table>';
		$i = 16;

		while ($i <= $l2cfg['gs']['count']) {
			if ($l2cfg['gs'][$i]['ls'] == $lid) {
				$db->gdb( $i );
				$vgs_temp = $vList[$l2cfg['gs'][$i]['version']];
				$sel_characters = $gdb[$i]->SuperQuery( $qList[$vgs_temp]['getAccountCharacters'], array( 'account' => $account ) );
				$l2_content .= '
			<div align=\'center\'><br>
			<table width=\'100%\' cellpadding=\'0\' cellspacing=\'0\' class=\'shadow\'>
			<tr>
				<td class=\'tabTitle\' colspan=\'5\'>Сервер: ' . $l2cfg['gs'][$i]['title'] . '</td>
			</tr>
			<tr>
				<td class=\'tdTitle\' width=\'\'>Nick</td>
				<td class=\'tdTitle\' width=\'150px\'>Level</td>
				<td class=\'tdTitle\' width=\'150px\'>Class</td>
				<td class=\'tdTitle\' width=\'150px\'>Last Visit</td>
				<td class=\'tdTitle\' width=\'150px\'>Access</td>
			</tr>';

				if ($char_data = $gdb[$i]->fetch( $sel_characters )) {
					if (0 < $char_data['accesslevel']) {
						$char_name = '<font color=\'green\'><b>' . $char_data['char_name'] . '</b></font>';
						$trClass = 'trRowA';
					} 
else {
						if ($char_data['accesslevel'] < 0) {
							$char_name = '<font color=\'red\'><b>' . $char_data['char_name'] . '</b></font>';
							$trClass = 'trRowB';
						} 
else {
							$char_name = $char_data['char_name'];
							$trClass = 'trRowC';
						}
					}

					$lastaccess = ($char_data['lastAccess'] == 0 ? 'n/a' : date( 'H:i d.m.y', intval( substr( $char_data['lastAccess'], 0, 10 ) + $l2cfg['timezone'] * 60 ) ));
					$l2_content .= '
				<tr class=\'' . $trClass . '\' onmouseover="this.className=\'trRowOn\'" onmouseout="this.className=\'' . $trClass . '\'">
					<td class=\'tdRow\'><a href=\'' . $_url . '=chars&action=character&lid=' . $lid . '&sid=' . $i . '&id=' . $char_data['charID'] . '\'>' . $char_name . '</a></td>
					<td class=\'tdRow\'>' . $char_data['level'] . '</td>
					<td class=\'tdRow\'>' . $char_data['ClassName'] . '</td>
					<td class=\'tdRow\'>' . $lastaccess . '</td>
					<td class=\'tdRow\'>' . $char_data['accesslevel'] . '</td>
				</tr>
';
				}


				if ($gdb[$i]->num_rows( $sel_characters ) == 0) {
					$l2_content .= '
				<tr class=\'trRowC\'>
					<td class=\'error\' colspan=\'5\'>На этом сервере нет персонажей</td>
				</tr>
				';
				}

				$l2_content .= '</table></div><br>';
			}

			++$i;
		}
	}


	if ($_action == 'character') {
		$l2_content .= '<div class=\'title\'>Сервер ..::' . $l2cfg['gs'][$sid]['title'] . '::..</div>';
		
		$charID = intval( $_GET['id'] );

		if (( isset( $_REQUEST['bann_char'] ) || isset( $_REQUEST['unbann_char'] ) )) {
			$access = (isset( $_REQUEST['bann_char'] ) ? -100 : 0);
			$gdb[$sid]->SuperQuery( $qList[$vgs]['setAccessLevelCharacter'], array( 'level' => $access, 'charID' => $charID ) );
			$controller->redirect(  $_url . '=chars&action=character&lid=' . $lid . '&sid=' . $sid . '&id=' . $charID );
		}


		if (( isset( $_REQUEST['bann_acc'] ) || isset( $_REQUEST['unbann_acc'] ) )) {
			$bann_login = $_REQUEST['bann_login'];
			$access = (isset( $_REQUEST['bann_acc'] ) ? -100 : 0);
			$ldb[$lid]->SuperQuery( $qList[$vls]['setAccessLevelAccount'], array( 'level' => $access, 'login' => $bann_login ) );
			$controller->redirect( $_url . '=chars&action=character&lid=' . $lid . '&sid=' . $sid . '&id=' . $charID );
		}

		
		$res = $gdb[$sid]->SuperQuery( $qList[$vgs]['getCharacter'], array( 'charID' => $charID ) );

		if ($gdb[$sid]->num_rows( $res ) == 0) {
			$l2_content .= '<div class=\'error\'>Персонаж не существует</div>';
		} 
else {
			
			$char_data = $gdb[$sid]->fetch( $res );
			$account_data = $ldb[$lid]->SuperFetchArray( $qList[$vls]['getAccount'], array( 'login' => $char_data['account_name'], 'where' => '' ) );

			if ($char_data['online'] == 1) {
				$l2_content .= '<div class=\'error\'>Для корректного изменения данных, персонаж должен находится в офф-лайне!</div>';
			}

			$char_data['sex'] = ($char_data['sex'] == 0 ? 'Man' : 'Woman');
			$char_data['online'] = ($char_data['online'] == 1 ? '<font color=\'green\'>Online</font>' : '<font color=\'red\'>Offline</font>');

			if (0 < $char_data['accesslevel']) {
				$accesslevel = '<font color=\'green\'>' . $char_data['accesslevel'] . '</font>';
				$char_name = '<font color=\'green\'>' . $char_data['char_name'] . '</font>';
			} 
else {
				if ($char_data['accesslevel'] < 0) {
					$accesslevel = '<font color=\'red\'>' . $char_data['accesslevel'] . '</font>';
					$char_name = '<font color=\'red\'>' . $char_data['char_name'] . '</font>';
				} 
else {
					
					$accesslevel = $char_data['accesslevel'];
					
					$char_name = $char_data['char_name'];
				}
			}


			if (0 < $account_data['accessLevel']) {
				$accountname = '<font color=\'green\'>' . $char_data['account_name'] . '</font>';
			} 
else {
				if ($account_data['accessLevel'] < 0) {
					$accountname = '<font color=\'red\'>' . $char_data['account_name'] . '</font>';
				} 
else {
					
					$accountname = $char_data['account_name'];
				}
			}

			$char_data['onlinetime'] = $controller->OnlineTime( $char_data['onlinetime'] );
			$char_data['lastAccess'] = ($char_data['lastAccess'] != '' ? date( 'H\:i d.m.y', intval( substr( $char_data['lastAccess'], 0, 10 ) + $l2cfg['timezone'] * 60 ) ) : 'n/a');
			$l2_content .= '
		<table cellpadding=\'0\' cellspacing=\'0\' width=\'100%\' class=\'shadow\'>
		<tr>
			<td class=\'tdTitle\'>Аккаунт</td>
			<td class=\'tdTitle\'>Персонаж</td>
			<td class=\'tdTitle\'>Уровень</td>
			<td class=\'tdTitle\'>Пол</td>
			<td class=\'tdTitle\'>Класс</td>
			<td class=\'tdTitle\'>Клан</td>
			<td class=\'tdTitle\'>Последний вход</td>
		</tr>
		<tr>
			<td class=\'tdRow\'>' . $accountname . '</td>
			<td class=\'tdRow\'>' . $char_name . '</td>
			<td class=\'tdRow\'>' . $char_data['level'] . '</td>
			<td class=\'tdRow\'>' . $char_data['sex'] . '</td>
			<td class=\'tdRow\'>' . $char_data['ClassName'] . '</td>
			<td class=\'tdRow\'>' . $char_data['clan_name'] . '</td>
			<td class=\'tdRow\'>' . $char_data['lastAccess'] . '</td>
		</tr>
		<tr>
			<td class=\'tdTitle\'>Exp</td>
			<td class=\'tdTitle\'>Sp</td>
			<td class=\'tdTitle\'>Karma</td>
			<td class=\'tdTitle\'>PvP / PK</td>
			<td class=\'tdTitle\'>Время в игре</td>
			<td class=\'tdTitle\'>Access Level</td>
			<td class=\'tdTitle\'>Статус</td>
		</tr>
		<tr>
			<td class=\'tdRow\'>' . $char_data['exp'] . '</td>
			<td class=\'tdRow\'>' . $char_data['sp'] . '</td>
			<td class=\'tdRow\'>' . $char_data['karma'] . '</td>
			<td class=\'tdRow\'>' . $char_data['pvpkills'] . ' / ' . $char_data['pkkills'] . '</td>
			<td class=\'tdRow\'>' . $char_data['onlinetime'] . '</td>
			<td class=\'tdRow\'>' . $accesslevel . '</td>
			<td class=\'tdRow\'>' . $char_data['online'] . '</td>
		</tr>
		</table><hr>';
			$form_url .= '<form action=\'' . $_url . '=chars&action=character&lid=' . $lid . '&sid=' . $sid . '&id=' . $charID . '\' method=\'post\'>';
			$l2_content .= '
		<table cellpadding=\'0\' cellspacing=\'0\' width=\'100%\'>
		<tr>
			<td>
				' . $form_url . '
				<input type=\'submit\' value=\'Заблокировать персонажа\' name=\'bann_char\' class=\'button\'>
				</form>
			</td>
			<td>
				' . $form_url . '
				<input type=\'submit\' value=\'Разблокировать персонажа\' name=\'unbann_char\' class=\'button\'>
				</form>
			</td>
			<td>
				' . $form_url . '
				<input type=\'hidden\' value=\'' . $char_data['account_name'] . '\' name=\'bann_login\'>
				<input type=\'submit\' value=\'Заблокировать аккаунт\' name=\'bann_acc\' class=\'button\'>
				</form>
			</td>
			<td>
				' . $form_url . '
				<input type=\'hidden\' value=\'' . $char_data['account_name'] . '\' name=\'bann_login\'>
				<input type=\'submit\' value=\'Разблокировать аккаунт\' name=\'unbann_acc\' class=\'button\'>
				</form>
			</td>
			<td>
				' . $form_url . '
				<input type=\'submit\' value=\'Удалить все предметы\' name=\'delete_items\' class=\'button\'>
				</form>
			</td>
		</tr>
		</table><hr>';
			$l2_content .= '
			<div align=\'left\'>
			<form action=\'' . $_url . '=chars&action=character&lid=' . $lid . '&sid=' . $sid . '&id=' . $charID . '\' method=\'post\'>
			Удалить у чара вещи c <b>ID</b>: <input type=\'text\' name=\'delItemId\' style=\'width: 70px;\'> <input type=\'submit\' value=\'Удалить\' name=\'deleteItemsIdOwner\' class=\'button\'></form></div>';

			if (isset( $_REQUEST['deleteItemsIdOwner'] )) {
				
				$del_item_id = $_POST['delItemId'];
				$gdb[$sid]->SuperQuery( $qList[$vgs]['delItemByIDOwner'], array( 'item' => $del_item_id, 'charID' => $charID ) );
				$l2_content .= '<div class=\'error\'>Удалено предметов ' . $gdb[$sid]->affected(  ) . '</div><hr>';
			}

			$l2_content .= '
			<div align=\'left\'>
			<form action=\'' . $_url . '=chars&action=character&lid=' . $lid . '&sid=' . $sid . '&id=' . $charID . '\' method=\'post\'>
			Добавить персонажу предмет &nbsp; <b>Item ID</b>: <input type=\'text\' name=\'AddItemId\' style=\'width: 70px;\'> &nbsp; <b>Количество:</b> <input type=\'text\' name=\'AddItemCount\' style=\'width: 70px;\'>  &nbsp; <b>Заточка:</b> <input type=\'text\' name=\'AddItemEnchant\' value=\'0\' style=\'width: 70px;\'> <b><i>одиночный</i></b> <input type=\'checkbox\' value=\'1\' name=\'AddItemSingle\' title=\'Не складывается в кучу, а добавляется отдельно\'> <input type=\'submit\' value=\'Добавить\' name=\'AddItem\' class=\'button\'></form>
			</div>';

			if (isset( $_POST['AddItem'] )) {
				
				$AddItemId = intval( $_POST['AddItemId'] );
				
				$AddItemCount = intval( $_POST['AddItemCount'] );
				$AddItemEnchant = (!empty( $_POST['AddItemEnchant'] ) ? intval( $_POST['AddItemEnchant'] ) : 0);
				
				$AddItemSingle = intval( $controller->set( 'AddItemSingle' ) );

				if (( empty( $$AddItemId ) || $AddItemId == 0 )) {
					$l2_content .= '<div class=\'error\'>Введите ID предмета</div>';
				} 
else {
					if (( empty( $$AddItemCount ) || $AddItemCount == 0 )) {
						$l2_content .= '<div class=\'error\'>Введите количество предметов</div>';
					} 
else {
						if ($char_data['online'] == 1) {
							$l2_content .= '<div class=\'error\'>Персонаж находится в игре</div>';
						} 
else {
							if ($AddItemSingle == '0') {
								
								$add_query = $gdb[$sid]->query(   'SELECT `object_id`,`count` FROM `items` WHERE `owner_id`=\'' . $charID . '\' AND `item_id`=\'' . $AddItemId . '\' AND `loc`=\'INVENTORY\'' );

								if (0 < $gdb[$sid]->num_rows( $add_query )) {
									
									$add_data = $gdb[$sid]->fetch( $add_query );
									$gdb[$sid]->query( ( 'UPDATE `items` SET `count`=`count`+' . $AddItemCount . ' WHERE `owner_id`=\'' . $charID . '\' AND `object_id`=\'' . $add_data['object_id'] . '\'' ) );
								} 
else {
									
									$max_obj = $gdb[$sid]->fetch( $gdb[$sid]->query( 'SELECT MAX(`object_id`)+1 FROM `items`' ) )[0];
									$gdb[$sid]->query(  'INSERT INTO `items` SET `owner_id`=\'' . $charID . '\',`object_id`=\'' . $max_obj . '\',`item_id`=\'' . $AddItemId . '\',`count`=' . $AddItemCount . ',`enchant_level`=\'' . $AddItemEnchant . '\',`loc`=\'INVENTORY\'' );
								}
							} 
else {
								$i = 15;

								while ($i < $AddItemCount) {
									
									$max_obj = $gdb[$sid]->fetch( $gdb[$sid]->query( 'SELECT MAX(`object_id`)+1 FROM `items`' ) )[0];
									$gdb[$sid]->query(   'INSERT INTO `items` SET `owner_id`=\'' . $charID . '\',`object_id`=\'' . $max_obj . '\',`item_id`=\'' . $AddItemId . '\',`count`=\'1\',`enchant_level`=\'' . $AddItemEnchant . '\',`loc`=\'INVENTORY\'' );
									++$i;
								}
							}


							if (0 < $gdb[$sid]->affected(  )) {
								$l2_content .= '<div class=\'no_error\'>Персонажу был добавлен предмет <b>ID: ' . $AddItemId . ' (' . $AddItemCount . 'шт.)[заточка: +' . $AddItemEnchant . ']</b></div>';
							} 
else {
								$l2_content .= '<div class=\'error\'>Ошибка базы данных</div>';
							}
						}
					}
				}

				$l2_content .= '<center><a href=\'' . $_url . '=chars&action=character&lid=' . $lid . '&sid=' . $sid . '&id=' . $charID . '\'><b>Назад</b></a></center><br>';
			}


			if (isset( $_REQUEST['delete_items'] )) {
				$gdb[$sid]->SuperQuery( $qList[$vgs]['delItemByOwner'], array( 'charID' => $charID ) );
				$l2_content .= '<div class=\'error\'> Удалено записей: ' . $gdb[$sid]->affected(  ) . '</div><br>
';
			}


			if (( isset( $_REQUEST['do'] ) && $_REQUEST['do'] == 'del_item' )) {
				
				$object_id = intval( $_REQUEST['object_id'] );
				$gdb[$sid]->SuperQuery( $qList[$vgs]['delItemByObjectID'], array( 'objectID' => $object_id ) );
				$l2_content .= '<div class=\'error\'> Удалено записей: ' . $gdb[$sid]->affected(  ) . '</div><br>
';
			}


			if (( isset( $_REQUEST['do'] ) && $_REQUEST['do'] == 'edit_item' )) {
				
				$edit_owner_id = intval( $_REQUEST['id'] );				
				$edit_object_id = intval( $_REQUEST['object_id'] );
				$item_data = $gdb[$sid]->SuperFetchArray( $qList[$vgs]['getItemByObjectID'], array( 'objectID' => $edit_object_id ) );
				$l2_content .= '
			<form method=\'post\' action=\'' . $_url . '=chars&action=character&lid=' . $lid . '&sid=' . $sid . '&do=update_item&id=' . $edit_owner_id . '&object_id=' . $edit_object_id . '\'>
			<b>Edit Item</b> - ID ' . $item_data['item_id'] . '<br>
			<b>Count</b>: <input type=\'text\' value=\'' . $item_data['count'] . '\' name=\'item_count\' style=\'width: 50px;\'> <b>Enchant</b>: <input type=\'text\' value=\'' . $item_data['enchant_level'] . '\' name=\'item_enchant\' style=\'width: 50px;\'> <input type=\'submit\' value=\'Edit\'><hr>';
			}


			if (( isset( $_REQUEST['do'] ) && $_REQUEST['do'] == 'update_item' )) {
				
				$update_owner_id = intval( $_REQUEST['id'] );
				
				$update_object_id = intval( $_REQUEST['object_id'] );
				
				$update_count = intval( $_REQUEST['item_count'] );
				
				$update_enchant = intval( $_REQUEST['item_enchant'] );
				$update_count = (0 < $update_count ? $update_count : 1);
				$gdb[$sid]->SuperQuery( $qList[$vgs]['setItem'], array( 'count' => $update_count, 'enchant' => $update_enchant, 'objectID' => $update_object_id ) );

				if ($gdb[$sid]->affected( )) {
					$l2_content .= '<div class=\'no_error\'>Предмет успешно обновлен</div>';
				} 
else {
					$l2_content .= '<div class=\'error\'>Ошибка при обновлении</div>
';
				}
			}

			
			$sel = $gdb[$sid]->SuperQuery( $qList[$vgs]['getInventory'], array( 'charID' => $charID, 'order' => 'name' ) );

			if ($gdb[$sid]->num_rows( $sel ) == 0) {
				$l2_content .= '<div class=\'error\'>У персонажа нет вещей</div>';
			} 
else {
				$l2_content .= '<div>
				<table cellpadding=\'0\' cellspacing=\'0\' width=\'100%\' class=\'shadow\'>
				<tr>
					<td class=\'tdTitle\'>ID</td>
					<td class=\'tdTitle\'>Item</td>
					<td class=\'tdTitle\'>Type</td>
					<td class=\'tdTitle\'>Count</td>
					<td class=\'tdTitle\'>Enchant</td>
					<td class=\'tdTitle\'>Loc</td>
					<td class=\'tdTitle\'>&nbsp</td>
				</tr>
';
				
				$item_type = $gdb[$sid]->fetch( $sel )[6];
				//print_r( $item_type );
				$item_name = [5];
				
				$item_loc = [4];
				
				$item_enchant = [3];
				
				$item_count = [2];
				
				$item_id = [1];
				
				$object_id = [0];

				//if () 
				{
					$l2_content .= '
				<tr class=\'trRow\' onmouseover="this.className=\'trRowOn\'" onmouseout="this.className=\'trRow\'">
					<td>' . $item_id . '</td>
					<td>' . $item_name . '</td>
					<td>' . $item_type . '</td>
					<td>' . $controller->CountFormat( $item_count ) . '</td>
					<td>' . $item_enchant . '</td>
					<td>' . $item_loc  . '</td>
					<td><a href=\'' . $_url . '=chars&action=character&lid=' . $lid . '&sid=' . $sid . '&do=edit_item&id=' . $charID . '&object_id=' . $object_id . '\'><img src=\'' . TPLDIR . '/edit.png\' title=\'Edit\'></a> <a href="javascript: v=\'' . $item_id . '\'; url=\'' . $_url . '=chars&action=character&lid=' . $lid . '&sid=' . $sid . '&do=del_item&id=' . $charID . '&object_id=' . $object_id . '\'; confirmDelete(v,url);"><img src=\'' . TPLDIR . '/delete.png\' title=\'Delete\'></a></td>
				</tr>
';
				}

				$l2_content .= '</table></div><br>';
			}
		}
	}


	if ($_action == '') {
		$lid = (in_array( $l2cfg['gs'][$sid]['ls'], $lsList ) ? intval( $l2cfg['gs'][$sid]['ls'] ) : 1);
		$l2_content .= '<div class=\'title\'>Сервер ..:: ' . $l2cfg['gs'][$sid]['title'] . ' ::..</div>';

		if (isset( $_REQUEST['delete'] )) {
			
			$list = $_REQUEST['delete'];
			$deleted_characters = 15;
			$deleted_other = 15;
			foreach ($list as $charid_del) {
				//$charid_del = ;
				$gdb[$sid]->SuperQuery( $qList[$vgs]['delCharByID'], array( 'charID' => $charid_del ) );
				++$deleted_characters;
				//print_r( $qList[$vgs]['other'] );
				foreach ($qList[$vgs]['other'] as $it) {
					//$sql = ;
					$gdb[$sid]->SuperQuery( $sql, array( 'charID' => $charid_del ) );
					$gdb[$sid]->affected(  );
					++$deleted_other;
				}
			}

			$l2_content .= '<div class=\'error\'> Удалено персонажей <b>' . $deleted_characters . '</b>, остальных записей <b>' . $deleted_other . '</b></div><br>
';
		}

		$search_account = (isset( $_REQUEST['search_account'] ) ? $gdb[$sid]->safe( $_REQUEST['search_account'] ) : '');
		$search_name = (isset( $_REQUEST['search_name'] ) ? $gdb[$sid]->safe( $_REQUEST['search_name'] ) : '');
		$search_access = (isset( $_REQUEST['search_access'] ) ? $gdb[$sid]->safe( $_REQUEST['search_access'] ) : '');
		$where = array(  );

		if ($search_name != '') {
			$where[] =  '`char_name` LIKE \'%' . $search_name . '%\'';
		}


		if ($search_account != '') {
			$where[] =   '`account_name` LIKE \'%' . $search_account . '%\'';
		}


		if ($search_access != '') {
			if ($search_access == 'less') {
				$where[] = '`accesslevel`<\'0\'';
			} 
else {
				if ($search_access == 'more') {
					$where[] = '`accesslevel`>\'0\'';
				}
			}
		}


		if (count( $where )) {
			$where = implode( ' AND ', $where );
			$where = 'WHERE ' . $where;
		} 
else {
			$where = '';
		}

		$off_set = 100 * ( $page - 1 );
		$sel_characters = $gdb[$sid]->SuperQuery( $qList[$vgs]['getCharactersList'], array( 'where' => $where, 'limit' =>  $off_set . ',100' ) );
		 //print_r( $qList[$vgs]['getCharactersList'] );
		if ($gdb[$sid]->num_rows( $sel_characters ) == 0) {
			$l2_content .= '<div class=\'error\'>В базе данных нет персонажей</div>';
		} 
else {
			$count = $gdb[$sid]->SuperFetchArray( $qList[$vgs]['getCountCharacters'], array( 'where' => $where ) )[0];
			
			$l2_content .= '<center><b>Всего ' . $count . ' персонажей</b></center><br><a href="javascript:hide(\'search\')">Поиск</a><br><br>
		<div align=\'left\' id=\'search\' style=\'display:none; margin-bottom: 10px; padding:5px;\' >
			<form action=\'\' method=\'get\'>
			<input type=\'hidden\' name=\'mod\' value=\'chars\'>
			<input type=\'hidden\' name=\'lid\' value=\'' . $lid . '\'>
			<input type=\'hidden\' name=\'sid\' value=\'' . $sid . '\'>
			<input type=\'hidden\' name=\'act\' value=\'search\'>
			<table cellpadding=\'0\' cellspacing=\'0\' class=\'shadow\'>
			<tr>
				<td>Поиск по аккаунтам</td>
				<td><input type=\'text\' name=\'search_account\' style=\'width: 150px; margin: 2px;\'></td>
			</tr>
			<tr>
				<td>Поиск по нику</td>
				<td><input type=\'text\' name=\'search_name\' style=\'width: 150px; margin: 2px;\'></td>
			</tr>
			<tr>
				<td>Поиск по уровню доступа</td>
				<td>
					<select name=\'search_access\' style=\'width: 50px; margin: 2px;\'>
						<option value=\'null\' selected> &nbsp;&nbsp; = &nbsp;&nbsp;</option>
						<option value=\'more\'> &nbsp;&nbsp; > &nbsp;&nbsp; </option>
						<option value=\'less\'> &nbsp;&nbsp; < &nbsp;&nbsp; </option>
					</select> 0
				</td>
			</tr>
			<tr>
				<td align=\'right\' colspan=\'2\'><input type=\'submit\' class=\'button\' value=\'Поиск\'></td>
			</tr>
			</table>
			</form>
		</div>';
			$l2_content .= '
			<div align=\'left\'>
			<form action=\'' . $_url . '=chars&sid=' . $sid . '\' method=\'post\'>
			Удалить у всех чаров предмет c <b>ID</b>: <input type=\'text\' name=\'del_item_id\' style=\'width: 70px;\'> <input type=\'submit\' value=\'Удалить\' name=\'delete_items\' class=\'button\'></form></div>';

			if (isset( $_REQUEST['delete_items'] )) {
				$del_item_id = $_POST['del_item_id'];
				$gdb[$sid]->SuperQuery( $qList[$vgs]['delItemByID'], array( 'item' => $del_item_id ) );
				$l2_content .= '<div class=\'error\'>Удалено предметов ' . $gdb[$sid]->affected(  ) . '</div><hr>';
			}

			$l2_content .= '
			<center><form method=\'post\' action=\'\'>
			<table cellpadding=\'0\' cellspacing=\'0\' width=\'100%\' id=\'List\'>
			<tr>
				<td class=\'tdTitle\'>Account</td>
				<td class=\'tdTitle\'>Nick</td>
				<td class=\'tdTitle\'>Level</td>
				<td class=\'tdTitle\'>Class</td>
				<td class=\'tdTitle\'>Last Visit</td>
				<td class=\'tdTitle\'>Access Level</td>
				<td class=\'tdTitle\' align=\'center\'><input type=\'submit\' value=\'Delete\' name=\'DeleteSubmit\' style=\'background: transparent; border: 1px solid #FFF; color: #FFF\'></td>
			</tr>';
			$account = $gdb[$sid]->fetch( $sel_characters )[6];
			$base_class = [5];
			
			$lastaccess = [4];
			
			$accesslevel = [3];
			
			$level = [2];
			
			$char_name = [1];
			
			$objId = [0];
			
			
			/**БЫЛО //if() сделал так как в старой*/
			while(list($account, $objId, $char_name, $level, $accesslevel, $lastaccess, $base_class) = $gdb[$sid]->fetch($sel_characters))
			{
				if ($accesslevel > 0)
				{
					$char_name = "<font color='green'><b>" . $char_name . "</b></font>";
					$trClass = "trRowA";
				} 
				elseif ($accesslevel < 0)
				{
					$char_name = "<font color='red'><b>" . $char_name . "</b></font>";
					$trClass = "trRowB";
				}
				else
				{
					$char_name = $char_name;
					$trClass = "trRowC";
				}
				$timezone = $l2cfg['timezone'];
				$lastaccess = ($lastaccess == 0 ? 'n/a' : date( 'H:i d.m.y', intval( substr( $lastaccess, 0, 10 ) + $timezone * 60 ) ));
				$l2_content .= '
				<tr class=\'' . $trClass . '\' onmouseover="this.className=\'trRowOn\'" onmouseout="this.className=\'' . $trClass . '\'">
					<td><a href=\'' .$_url . '=chars&action=account&acc=' . $account . '&lid=' . $lid . '\'>' . $account . '</a></td>
					<td><a href=\'' . $_url . '=chars&action=character&lid=' . $lid . '&sid=' . $sid . '&id=' . $objId . '\'>' . $char_name . '</a></td>
					<td>' . $level . '</td>
					<td>' . $base_class . '</td>
					<td>' . $lastaccess . '</td>
					<td>' . $accesslevel . '</td>
					<td align=\'center\'><input type=\'checkbox\' name=\'delete[]\' value=\'' . $objId . '\'></td>
				</tr>';
			}

			$l2_content .= '</table></form></center><br>';

			if (100 < $count) 
			{
				$numpages = ceil( $count / 100 );

				if (!empty( $$_act )) 
				{
					$string = '&search_account=' . $search_account . '&search_name=' . $search_name . '&search_access=' . $search_access;
				} 
				else 
				{
					$string = '';
				}

				$controller->PageList(  $_url . '=chars&lid=' . $lid . '&sid=' . $sid . $string . '&page=', $numpages, $page );
				$l2_content .= '';
			}
		}
	}

	$tpl->SetResult( 'content', $l2_content );
?>