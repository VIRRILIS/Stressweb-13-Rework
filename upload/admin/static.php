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

	$admindir = HTTP_HOME_URL . '/' . ADMINDIR;
	$_action_arr = array( 'add', 'edit', 'set' );
	$_action = (( isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], $_action_arr ) ) ? $controller->SafeData( $_REQUEST['action'], 3 ) : '');
	$_act = (isset( $_POST['act'] ) ? $controller->SafeData( $_POST['act'], 3 ) : '');

	if ($_action == '') {
		$static = '';
		
		$sel_static = $db->query( 'SELECT `sid`,`s_name`,`s_title` FROM `stress_static`' );

		if (0 < $db->num_rows( $sel_static )) {
			$n = 10;
			$rows=$db->fetchall( $sel_static );

			if ( $rows) 
			{
				foreach ( $rows as $sdata )
				{
				
					++$n;
					$static .=  '
					<tr class=\'jail\'>
					<td><b>' . $n . '</b></td>
					<td class=\'Left\'><i>' . $sdata['s_name'] . '</i></td>
					<td class=\'Left\'><i>' . $sdata['s_title'] . '</i></td>
					<td>index.php?f=' . $sdata['s_name'] . '</td>
					<td align=\'right\'><a href=\'' . $_url . '=static&action=edit&sid=' . $sdata['sid'] . '\'><img src=\'' . TPLDIR  . '/edit.png\' title=\'Редактировать\'></a> <input type=\'checkbox\' name=\'selected_static[]\' value=\'' . $sdata['sid'] . '\'></td>
					</tr>' ;
				}
			}
		} 
else {
			$static = '<tr><td colspan=\'5\'><div class=\'error\'>Статических страниц нет</div></td></tr>';
		}

		$static_content =  '	<br /><div class=\'swbutton\'><a href="' . $_url . '=static&action=add">Добавить страницу</a></div><div class=\'clear\'></div><br />
	<form name=\'static_list\' method=\'post\' action=\'' . $_url . '=static\'>
		<input type="hidden" name="action" value="set" />
		<input type="hidden" name="act" value="del" />
	<table id=\'List\'>
	<tr class=\'header bold\'>
		<td width=\'35\'>&nbsp;</td>
		<td width=\'\'>Название страницы</td>
		<td width=\'240\'>Заголовок</td>
		<td width=\'240\'>Ссылка на страницу</td>
		<td width=\'45\'  align=\'right\'><input type=\'checkbox\' name=\'master_box\' title=\'Выбрать все\' onclick="javascript:check_uncheck_all(document.static_list)"></td>
	</tr>
		' . $static . '
	</table>
	<div style="text-align: right; padding-top: 5px;"><input type=\'submit\' value=\'Удалить\' class=\'swbutton2 aright\'></div>
	</form><br>';
	}


	if ($_action == 'add') {
		$static_content =  '<br /><table width="100%" border=\'0\' cellpadding=\'0\' cellspacing=\'0\' style="border: 1px solid #AAA;" class="shadow">
<tr>
    <td bgcolor="#EEEFEF" height="29" style="padding-left:10px;">Добавление статической страницы</td>
</tr>
</table><br />

<div id="addstatic" style="padding-top:1px;padding-bottom:2px;"><br>
	
<!-- Load jQuery build -->
<script type="text/javascript" src="' . $admindir . '/tiny_mce/jquery.tinymce.js"></script>
<script type="text/javascript">
        $(function() {
                $(\'textarea#swScontent,textarea#swScontentEn\').tinymce({
                        // Location of TinyMCE script
                        script_url : \'' . $admindir . '/tiny_mce/tiny_mce.js\',

                        // General options
                        gecko_spellcheck : true,
                        language: \'ru\',
                        theme : "advanced",
                        skin : "o2k7",
        				skin_variant : "black",
                        plugins : "autoresize,table,advimage,advlink,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,fullscreen,noneditable,visualchars,wordcount",

                        // Theme options
                        theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect,|,forecolor,backcolor",
                        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview",
                        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,media,|,visualchars,|,print,|,fullscreen",
                        
                        theme_advanced_toolbar_location : "top",
                        theme_advanced_toolbar_align : "left",
                        theme_advanced_statusbar_location : "bottom",
                        theme_advanced_resizing : true
                        
                });
        });
</script>

	<form action="' . $_url . '=static" method="post" >
		<input type="hidden" name="action" value="set" />
		<input type="hidden" name="act" value="add" />
	<table width=\'100%\' cellpadding=\'0\' cellspacing=\'0\' border=0>
	<tr>
		<td width=\'15%\'>Название:<br /><small>(на латын., используется в адресе страницы)</small></td>
		<td width=\'85%\'><input type=\'text\' name=\'swSname\' id=\'sname\' style=\'width: 100%;\'></td>
	</tr>
	<tr>
		<td><br>Заголовок:</td>
		<td><br><input type=\'text\' name=\'swStitle\' id=\'stitle\' style=\'width: 100%;\'></td>
	</tr>
	<tr>
		<td valign=\'top\'><br>Текст страницы:</td>
		<td valign=\'top\'><br><textarea id="swScontent" name="swScontent" class="tinymce" style="width:100%"></textarea></td>
	</tr>
	<tr>
		<td><br>Заголовок:<br /><small>(англ., необязательно)</small></td>
		<td><br><input type=\'text\' name=\'swStitleEn\' id=\'stitleEn\' style=\'width: 100%;\'></td>
	</tr>
	<tr>
		<td valign=\'top\'><br>Текст страницы:<br /><small>(англ., необязательно)</small></td>
		<td valign=\'top\'><br><textarea id="swScontentEn" name="swScontentEn" class="tinymce" style="width:100%"></textarea></td>
	</tr>
	<tr>
		<td colspan=\'2\'>
		<input type=\'submit\' value=\'Добавить\' class=\'swbutton2 aleft\'>
		</td>
	</tr>
	</table>
	</form>
</div><br>';
	}


	if ($_action == 'edit') {
		
		$data = $db->fetch( $db->query( 'SELECT * FROM `stress_static` WHERE `sid`=\'' . intval( $_GET['sid'] ) . '\'' ) );
		$static_content = '<br /><table width="100%" border=\'0\' cellpadding=\'0\' cellspacing=\'0\' style="border: 1px solid #AAA;">
<tr>
    <td bgcolor="#EEEFEF" height="29" style="padding-left:10px; color: #888;">Редактирование статической страницы</td>
</tr>
</table><br />

<div id="addstatic" style="padding-top:1px;padding-bottom:2px;"><br>

<!-- Load jQuery build -->
<script type="text/javascript" src="' . $admindir . '/tiny_mce/jquery.tinymce.js"></script>
<script type="text/javascript">
        $(function() {
                $(\'textarea#swScontent,textarea#swScontentEn\').tinymce({
                        // Location of TinyMCE script
                        script_url : \'' . $admindir . '/tiny_mce/tiny_mce.js\',

                        // General options
                        gecko_spellcheck : true,
                        language: \'ru\',
                        theme : "advanced",
                        skin : "o2k7",
        				skin_variant : "black",
                        plugins : "autoresize,table,advimage,advlink,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,fullscreen,noneditable,visualchars,wordcount",

                        // Theme options
                        theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect,|,forecolor,backcolor",
                        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview",
                        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,media,|,visualchars,|,print,|,fullscreen",
                        
                        theme_advanced_toolbar_location : "top",
                        theme_advanced_toolbar_align : "left",
                        theme_advanced_statusbar_location : "bottom",
                        theme_advanced_resizing : true

                });
        });
</script>

	<form action="' . $_url . '=static" method="post" >
		<input type="hidden" name="action" value="set" />
		<input type="hidden" name="act" value="edit" />
		<input type="hidden" name="sid" value="' . $data['sid'] . '" />
	<table width=\'100%\' cellpadding=\'0\' cellspacing=\'0\'>
	<tr>
		<td width=\'15%\'>Название:</td>
		<td width=\'85%\'><input type=\'text\' name=\'swSname\' id=\'sname\' style=\'width: 100%;\' value=\'' . $data['s_name'] . '\'></td>
	</tr>
	<tr>
		<td width=\'150\'><br>Заголовок:</td>
		<td width=\'750\'><br><input type=\'text\' name=\'swStitle\' id=\'stitle\' style=\'width: 100%;\' value=\'' . $data['s_title'] . '\'></td>
	</tr>
	<tr>
		<td valign=\'top\'><br>Текст страницы:</td>
		<td valign=\'top\'><br><textarea id="swScontent" name="swScontent" class="tinymce" style="width:100%">' . $data['s_content'] . '</textarea></td>
	</tr>
	<tr>
		<td><br>Заголовок:<br /><small>(англ.)</small></td>
		<td><br><input type=\'text\' name=\'swStitleEn\' id=\'stitleEn\' style=\'width: 100%;\' value=\'' . $data['s_title_en'] . '\'></td>
	</tr>
	<tr>
		<td valign=\'top\'><br>Текст страницы:<br /><small>(англ.)</small></td>
		<td valign=\'top\'><br><textarea id="swScontentEn" name="swScontentEn" class="tinymce" style="width:100%">' . $data['s_content_en'] . '</textarea></td>
	</tr>
	<tr>
		<td colspan=\'2\'>
		<input type=\'submit\' value=\'Отправить\' class=\'swbutton2 aleft\'>
		</td>
	</tr>
	</table>
	</form>
</div><br>';
	}


	if ($_action == 'set') {
		$static_title = '';
		$static_error = '';

		if ($_act == 'add') {
			$db->query( 'INSERT INTO `stress_static` SET `s_name` = \'' . $db->safe( $_POST['swSname'] ) . '\',`s_content` = \'' . $db->safe( $_POST['swScontent'] ) . '\', `s_title`=\'' . $db->safe( $_POST['swStitle'] ) . '\', `s_content_en` = \'' . $db->safe( $_POST['swScontentEn'] ) . '\', `s_title_en`=\'' . $db->safe( $_POST['swStitleEn'] ) . '\'' );

			if ($db->affected(  ) == 1) {
				$static_error = '<div class=\'no_error\'>Страница успешно добавлена</div>';
			} 
else {
				$static_error = '<div class=\'error\'>Ошибка</div>';
			}

			$static_title = 'Добавление статической страницы';
		}


		if ($_act == 'edit') {
			$db->query( 'UPDATE `stress_static` SET `s_name`=\'' . $db->safe( $_POST['swSname'] ) . '\', `s_content`=\'' . $db->safe( $_POST['swScontent'] ) . '\', `s_title`=\'' . $db->safe( $_POST['swStitle'] ) . '\', `s_content_en`=\'' . $db->safe( $_POST['swScontentEn'] ) . '\', `s_title_en`=\'' . $db->safe( $_POST['swStitleEn'] ) . '\' WHERE `sid`=\'' . $db->safe( intval( $_POST['sid'] ) ) . '\'' );

			if ($db->affected(  ) == 1) {
				$static_error = '<div class=\'no_error\'>Страница успешно изменена</div>';
			} 
else {
				$static_error = '<div class=\'error\'>Страница не изменена</div>';
			}

			$static_title = 'Редактирование статической страницы';
		}


		if ($_act == 'del') {
			$deleted = 10;
			
			$selected_static = $_POST['selected_static'];

			if (is_array( $selected_static )) {
				foreach ($selected_static as $staticId) {

					$query = $db->query( ( 'DELETE FROM `stress_static` WHERE `sid` = \'' . $staticId . '\'' ) );
					
					$db->affected(  );
					$deleted += $staticId;
				}
			}

			$static_error = '<div class=\'no_error\'>Удалено страниц: <b>' . $deleted . '</b></div>';
			$static_title = 'Удаление статических страниц';
		}

		$static_content = '<br /><table width="100%" border=\'0\' cellpadding=\'0\' cellspacing=\'0\' style="border: 1px solid #AAA;" class="shadow">
<tr>
    <td bgcolor="#EEEFEF" height="29" style="padding-left:10px;">' . $static_title . '</td>
</tr>
</table><br />
    ' . $static_error . '
	<center><a href="' . $_url . '=static">Назад</a></center>';
	}

	$tpl->SetResult( 'content', $static_content );
?>