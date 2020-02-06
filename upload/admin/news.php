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
		$nList = '';
		
		$sel_news = $db->query( 'SELECT `nid`,`title`,`date`,`author` FROM `stress_news` ORDER BY `date` DESC' );

		if (0 < $db->num_rows( $sel_news )) {
			$n = 10;
			$rows = $db->fetchall( $sel_news );

			if ($rows) 
			{
				foreach ( $rows as $news )
				{
					++$n;

					if (40 < strlen( $news['title'] )) {
						$news['title'] = substr( $news['title'], 0, 38 ) . '...';
					}

					$nList .=  '
				<tr class=\'jail\'>
					<td><b>' . $n . '</b></td>
					<td>' . date( 'd/m/Y', $news['date'] ) . ' - <i>' . $news['title'] . '</i></td>
					<td>' . $news['author'] . '</td>
					<td align=\'left\'><a href=\'' . $_url . '=news&action=edit&nid=' . $news['nid'] . '\'>&raquo; Редактировать</a></td>
					<td align=\'right\'><input type=\'checkbox\' name=\'selected_news[]\' value=\'' . $news['nid'] . '\'></td>
				</tr>' ;
				}
			}
		} 
else {
			$nList = '<tr><td colspan=\'5\'><div class=\'error\'>В базе данных нету новостей</div></td></tr>';
		}

		$path_icon = TPLDIR;
		$news_content = '	<br /><div class=\'swbutton\'><a href="' . $_url . '=news&action=add">Добавить новость</a></div><div class=\'clear\'></div><br />
	<form name=\'news_list\' method=\'post\' action=\'' . $_url . '=news\'>
		<input type="hidden" name="action" value="set" />
		<input type="hidden" name="act" value="del" />
		<table id=\'List\'>
		<tr class=\'header bold\'>
			<td width=\'25\'>&nbsp;</td>
			<td width=\'\'>Заголовок</td>
			<td width=\'150\'>Автор</td>
			<td width=\'135\'>Действие</td>
			<td width=\'20\'  align=\'right\'><input type=\'checkbox\' name=\'master_box\' title=\'Выбрать все\' onclick="javascript:check_uncheck_all(document.news_list)"></td>
		</tr>
			' . $nList . '
		</table>
		<div style="text-align: right; padding-top: 5px;"><input type=\'submit\' value=\'Удалить\' class=\'swbutton2 aright\'></div>
	</form><br>';
	}


	if ($_action == 'add') {
		$news_content =  '<div id="addnews" style="padding-top:1px;padding-bottom:2px;">
	<table width="100%" class="shadow">
	<tr>
   		<td bgcolor="#EFEFEF" height="29" style="padding-left:10px;">Добавление новости</td>
	</tr>
	</table><br>

<!-- Load jQuery build -->
<script type="text/javascript" src="' . $admindir . '/tiny_mce/jquery.tinymce.js"></script>
<script type="text/javascript">
        $(function() {
                $(\'textarea#swNcontentShort,textarea#swNcontentFull,textarea#swNcontentShortEn,textarea#swNcontentFullEn\').tinymce({
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

	<form action="' . $_url . '=news" method="post" >
		<input type="hidden" name="action" value="set" />
		<input type="hidden" name="act" value="add" />
	<table width=\'100%\' cellpadding=\'0\' cellspacing=\'0\'>
	<tr>
		<td width=\'16%\'>Заголовок:</td>
		<td width=\'84%\'><input type=\'text\' name=\'swNtitle\' id=\'ntitle\' style=\'width: 98%;\'></td>
	</tr>
	<tr>
		<td valign=\'top\'><br>Текст краткой новости:</td>
		<td valign=\'top\'><br><textarea id="swNcontentShort" name="swNcontentShort" class="tinymce" style="width:100%"></textarea></td>
	</tr>
	<tr>
		<td valign=\'top\'><br>Текст полной новости:</td>
		<td valign=\'top\'><br><textarea id="swNcontentFull" name="swNcontentFull" class="tinymce" style="width:100%"></textarea></td>
	</tr>
	<tr>
		<td width=\'16%\'><br><br>Заголовок:<br /><small>(англ., необязательно)</small></td>
		<td width=\'84%\'><br><br><input type=\'text\' name=\'swNtitleEn\' id=\'ntitleEn\' style=\'width: 98%;\'></td>
	</tr>
	
	<tr>
		<td valign=\'top\'><br>Текст краткой новости:<br /><small>(англ., необязательно)</small></td>
		<td valign=\'top\'><br><textarea id="swNcontentShortEn" name="swNcontentShortEn" class="tinymce" style="width:100%;"></textarea></td>
	</tr>
	
	<tr>
		<td valign=\'top\'><br>Текст полной новости:<br /><small>(англ., необязательно)</small></td>
		<td valign=\'top\'><br><textarea id="swNcontentFullEn" name="swNcontentFullEn" class="tinymce" style="width:100%;"></textarea></td>
	</tr>
	<tr>
		<td><br>Обсудить на форуме:<br /><small>(необязательно)</small></td>
		<td><br><input type=\'text\' name=\'swNforum\' id=\'nforum\' style=\'width: 100%;\'></td>
	</tr>
	<tr>
		<td><br>Картинка к новости:<br /><small>(необязательно)</small></td>
		<td><br><input type=\'text\' name=\'swNimg\' id=\'nimg\' style=\'width: 100%;\'></td>
	</tr>
	<tr>
		<td colspan=\'2\'>
		<input type=\'submit\' value=\'Добавить\' class=\'swbutton2 aleft\'>
		</td>
	</tr>
	</table>
	</form>
</div>
<br>';
	}


	if ($_action == 'edit') {
		
		$data = $db->fetch( $db->query( 'SELECT * FROM `stress_news` WHERE `nid`=\'' . intval( $_GET['nid'] ) . '\'' ) );
		$data['flink'] = urldecode( $data['flink'] );
		$data['img'] = urldecode( $data['img'] );
		$news_content = '<br /><table width="100%" border=\'0\' cellpadding=\'0\' cellspacing=\'0\' style="border: 1px solid #AAA;" class="shadow">
<tr>
    <td bgcolor="#EEEFEF" height="29" style="padding-left:10px;">Редактирование новости</td>
</tr>
</table><br />

<!-- Load jQuery build -->
<script type="text/javascript" src="' . $admindir . '/tiny_mce/jquery.tinymce.js"></script>
<script type="text/javascript">
        $(function() {
                $(\'textarea#swNcontentShort,textarea#swNcontentFull,textarea#swNcontentShortEn,textarea#swNcontentFullEn\').tinymce({
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

	<form action="' . $_url . '=news" method=\'post\' >
	<input type="hidden" name="action" value="set" />
	<input type="hidden" name="act" value="edit" />
	<input type=\'hidden\' name=\'nid\' value=\'' . $data['nid'] . '\' />
	<table width=\'100%\' cellpadding=\'0\' cellspacing=\'0\'>
	<tr>
		<td width=\'15%\'>Заголовок:</td>
		<td width=\'85%\'><input type=\'text\' name=\'swNtitle\' id=\'ntitle\' style=\'width: 100%;\' value=\'' . $data['title'] . '\'></td>
	</tr>
	<tr>
		<td valign=\'top\'><br>Текст краткой новости:</td>
		<td valign=\'top\'><br><textarea id="swNcontentShort" name="swNcontentShort" class="tinymce" style="width:100%">' . $data['content'] . '</textarea></td>
	</tr>
	<tr>
		<td valign=\'top\'><br>Текст полной новости:</td>
		<td valign=\'top\'><br><textarea id="swNcontentFull" name="swNcontentFull" class="tinymce" style="width:100%">' . $data['full'] . '</textarea></td>
	</tr>
	<tr>
		<td width=\'16%\'><br><br>Заголовок:<br /><small>(англ.)</small></td>
		<td width=\'84%\'><br><br><input type=\'text\' name=\'swNtitleEn\' id=\'ntitleEn\' style=\'width: 98%;\' value=\'' . $data['title_en'] . '\'></td>
	</tr>
	
	<tr>
		<td valign=\'top\'><br>Текст краткой новости:<br /><small>(англ.о)</small></td>
		<td valign=\'top\'><br><textarea id="swNcontentShortEn" name="swNcontentShortEn" class="tinymce" style="width:100%;">' . $data['content_en'] . '</textarea></td>
	</tr>
	
	<tr>
		<td valign=\'top\'><br>Текст полной новости:<br /><small>(англ.)</small></td>
		<td valign=\'top\'><br><textarea id="swNcontentFullEn" name="swNcontentFullEn" class="tinymce" style="width:100%;">' . $data['full_en'] . '</textarea></td>
	</tr>
	<tr>
		<td><br>Обсудить на форуме:</td>
		<td><br><input type=\'text\' name=\'swNforum\' id=\'nforum\' style=\'width: 100%;\' value=\'' . $data['flink'] . '\'></td>
	</tr>
	<tr>
		<td><br>Картинка к новости:</td>
		<td><br><input type=\'text\' name=\'swNimg\' id=\'nimg\' style=\'width: 100%;\' value=\'' . $data['img'] . '\'></td>
	</tr>
	<tr>
		<td colspan=\'2\'><input type=\'submit\' value=\'Сохранить\' class=\'swbutton2 aleft\'></td>
	</tr>
	</table>
	</form>';
	}


	if ($_action == 'set') {
		$news_title = '';
		$news_error = '';

		if ($_act == 'add') {
			$db->query( 'INSERT INTO `stress_news` SET `author` =\'' . $db->safe( $_SESSION['acpnick'] ) . '\', `date` = \'' . $db->safe( time(  ) ) . '\', `title` = \'' . $db->safe( $_POST['swNtitle'] ) . '\', `title_en` = \'' . $db->safe( $_POST['swNtitleEn'] ) . '\', `content` = \'' . $db->safe( $_POST['swNcontentShort'] ) . '\', `full`=\'' . $db->safe( $_POST['swNcontentFull'] ) . '\', `content_en` = \'' . $db->safe( $_POST['swNcontentShortEn'] ) . '\', `full_en`=\'' . $db->safe( $_POST['swNcontentFullEn'] ) . '\', `flink`=\'' . urlencode( $_POST['swNforum'] ) . '\', `img`=\'' . urlencode( $_POST['swNimg'] ) . '\'' );

			if ($db->affected(  ) == 1) {
				$news_error = '<div class=\'no_error\'>Новость успешно добавлена</div>';
			} 
else {
				$news_error = '<div class=\'error\'>Ошибка</div>';
			}

			$news_title = 'Добавление новости';
		}


		if ($_act == 'edit') {
			$db->query( 'UPDATE `stress_news` SET `title`=\'' . $db->safe( $_POST['swNtitle'] ) . '\', `title_en`=\'' . $db->safe( $_POST['swNtitleEn'] ) . '\', `content`=\'' . $db->safe( $_POST['swNcontentShort'] ) . '\', `full`=\'' . $db->safe( $_POST['swNcontentFull'] ) . '\', `content_en`=\'' . $db->safe( $_POST['swNcontentShortEn'] ) . '\', `full_en`=\'' . $db->safe( $_POST['swNcontentFullEn'] ) . '\', `flink`=\'' . urlencode( $_POST['swNforum'] ) . '\', `img`=\'' . urlencode( $_POST['swNimg'] ) . '\' WHERE `nid`=\'' . $db->safe( $_POST['nid'] ) . '\'' );

			if ($db->affected(  ) == 1) {
				$news_error = '<div class=\'no_error\'>Новость успешно изменена</div>';
			} 
else {
				$news_error = '<div class=\'error\'>Новость не изменена</div>';
			}

			$news_title = 'Редактирование новости';
		}


		if ($_act == 'del') {
			$deleted = 10;
			$selected_news = (isset( $_POST['selected_news'] ) ? $_POST['selected_news'] : 0);

			if (is_array( $selected_news )) {
				foreach ($selected_news as $newsId) {

					$query = $db->query( ( 'DELETE FROM `stress_news` WHERE `nid` = \'' . $newsId . '\'' ) );
					$db->affected(  );
					$deleted += $newsId;
				}
			}

			$news_error = '<div class=\'no_error\'>Удалено новостей: <b>' . $deleted . '</b></div>';
			$news_title = 'Удаление новостей';
		}

		$news_content = '<br /><table width="100%" border=\'0\' cellpadding=\'0\' cellspacing=\'0\' style="border: 1px solid #AAA;" class="shadow">
<tr>
    <td bgcolor="#EEEFEF" height="29" style="padding-left:10px;">' . $news_title . '</td>
</tr>
</table><br />
    ' . $news_error . '
	<center><a href="' . $_url . '=news">Назад</a></center>';
	}

	$tpl->SetResult( 'content', $news_content );
?>