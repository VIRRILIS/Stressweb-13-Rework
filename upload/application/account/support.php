<?php
/**
 * STRESS WEB
 * @author S.T.R.E.S.S.
 * @copyright 2008 - 2012 STRESS WEB
 * @version 13
 * @web http://stressweb.ru
 */
if ( !defined("STRESSWEB") )
    die( "Access denied..." );

if ( $controller->isLogged() ) 
{
    if ( !$l2cfg["support"]["enable"] ) 
	{
        $profile = "<div class='error'>{$lang["ticket_23"]}</div>";
    } 
	else 
	{
        $_act = ( isset($_REQUEST["act"]) ) ? $controller->SafeData( $_REQUEST["act"], 3 ):"";
        $tickets = "";
        $entries = "";
        $section = array();
        $servers = array();
        $serversIN = array();
        $status_arr = array( 0 => "{$lang["ticket_1"]}", 1 => "{$lang["ticket_2"]}", 2 => "{$lang["ticket_3"]}", 3 => "{$lang["ticket_4"]}" );
        $severity_arr = array( 0 => "{$lang["ticket_5"]}", 1 => "{$lang["ticket_6"]}", 2 => "{$lang["ticket_7"]}" );

        foreach ( $gsList as $sServ ) 
		{
            if ( $l2cfg["gs"][$sServ]["ls"] == $_lid )
                $serversIN[] = $sServ;
        }
		
        $serversIN = implode( ",", $serversIN );

		if ( $_act == "" ) 
		{
            if ( isset($_POST["send"]) ) 
			{
                $t_title = $db->safe( strip_tags($_POST["title"]) );
                $t_task = $db->safe( strip_tags($_POST["task"]) );
                $t_name = $db->safe( strip_tags($_POST["name"]) );
                $t_captcha = strtoupper( $db->safe($_POST["captcha"]) );

                if ( empty($t_task) or empty($t_title) or empty($t_name) or empty($t_captcha) )
                    $tpl->ShowError( $lang['error'], $lang["ticket_8"] );
                elseif ( $t_captcha != $_SESSION["seccode"] or !isset($_SESSION["seccode"]) )
                    $tpl->ShowError( $lang['error'], $lang["err_code"] );
                elseif ( strlen($t_title) < 10 )
                    $tpl->ShowError( $lang['error'], $lang["ticket_9"] );
                elseif ( strlen($t_task) > 1000 )
                    $tpl->ShowError( $lang['error'], $lang["ticket_10"] );
                else {
                    //$t_task = str_replace(array("\\n", "\\r"), "<br>", $t_task);
                    $db->query( "INSERT INTO `stress_ticket_task` SET `sid`='".intval($_POST["section"])."',`server`='".intval($_POST["server"])."',`title`='{$t_title}',`task`='{$t_task}',`severity`='".intval($_POST["severity"])."',`status`='0',`name`='{$t_name}',`login`='{$controller->GetName()}',`date`='".time()."'" );

					if ( $db->affected() > 0 )
					{
                        $controller->redirect();
                    } else
                        $tpl->ShowError( $lang['error'], $lang["err_db"] );
                }
            }
			
            $t_query = $db->query( "SELECT id,date,title,status FROM `stress_ticket_task` WHERE `login`='{$controller->GetName()}' AND `server` IN ({$serversIN}) ORDER BY `date` DESC" );
            if ( $db->num_rows($t_query) > 0 ) {
                while ( $data = $db->fetch($t_query) ) {
                    $color = ( $data["status"] == 0 ) ? "#F00":( ($data["status"] == 3) ? "#070":"#DD0" );
                    $data["date"] = date( "d.m.Y", $data["date"] );
                    $data["status"] = $status_arr[$data["status"]];
                    $_link = HTTP_HOME_URL.( $l2cfg['mod_rewrite'] ? '/cp/support/':'/index.php?f=cp&opt=support&act=write&s=' ).$data['id'];
                    $entries .= "
					<tr class='online hover'>
						<td>{$data['id']}</td>
						<td align='left'>{$data["date"]}</td>
						<td align='left'><a href='{$_link}'>{$data["title"]}</a></td>
						<td><span style='color: {$color};'>{$data["status"]}</span></td>
					</tr>
					";
                }
            } else
                $entries = "<tr><td colspan='4'><div class='error'>{$lang["ticket_11"]}</div></td></tr>";
            $profile = "
			<div align='center'>
			<h4>{$lang["ticket_12"]}</h4>
			<table id='swsupport'>
			<tr class='header bold'>						
				<th width='35px'>ID</th>
				<th width='70px'>{$lang["ticket_13"]}</th>
				<th width=''>{$lang["ticket_14"]}</th>
				<th width='100px'>{$lang["ticket_15"]}</th>
			</tr>
			{$entries}
			</table><br>";
            $sectiontmp1 = array();
            $sectiontmp2 = array();
            $section_q = $db->query( "SELECT * FROM `stress_ticket_section`" );
            if ( $db->num_rows($section_q) > 0 ) {
                while ( $sdata = $db->fetch($section_q) ) {
                    array_push( $sectiontmp1, $sdata["section"] );
                    array_push( $sectiontmp2, $sdata["id"] );
                }
                $section = array_combine( $sectiontmp2, $sectiontmp1 );
            }
            foreach ( $gsList as $sListTmp ) {
                if ( $l2cfg["gs"][$sListTmp]["ls"] == $_lid )
                    $servers[$sListTmp] = $l2cfg["gs"][$sListTmp]["title"];
            }
            $profile .= '
            <script type="text/javascript">//<![CDATA[
function reload () {
	var rndval = new Date().getTime(); 
	document.getElementById(\'sw-captcha\').innerHTML = \'<a onclick="reload(); return false;" href="#"><img src="'.HTTP_HOME_URL.'/module/antibot.php?rndval=\' + rndval + \'" border="0"></a>\';
};
//]]></script>
			<form name="form" action="" method="post">
			<table cellpadding="0" cellspacing="0" class="supForm">
			<tr>
				<td colspan="2" align="center"><p>'.$lang["ticket_16"].'</p></td>
			</tr>
			<tr>
			  	<td class="tdLeft">'.$lang["ticket_17"].':</td>
			  	<td class="tdRight">'.$controller->select( "section", $section, "", "style='width: 250px;'" ).'</td>
			</tr>
			<tr>
			  	<td class="tdLeft">'.$lang["ticket_18"].':</td>
			  	<td class="tdRight">'.$controller->select( "severity", $severity_arr, "", "style='width: 150px;'" ).'</td>
			</tr>
			<tr>
			  	<td class="tdLeft">'.$lang["ticket_19"].':</td>
			  	<td class="tdRight">'.$controller->select( "server", $servers, "", "style='width: 150px;'" ).'</td>
			</tr>
			<tr>
			  	<td class="tdLeft">'.$lang["ticket_20"].':</td>
			  	<td class="tdRight"><input type="text" name="name" maxlength="50" style="width: 250px"></td>
			</tr>
			<tr>
			  	<td class="tdLeft">'.$lang["ticket_21"].':</td>
			  	<td class="tdRight"><input type="text" name="title" maxlength="50" style="width: 250px"></td>
			</tr>
			<tr>
			  	<td align="center" colspan="2">
			  		'.$lang["ticket_22"].'<br>
			  		<textarea style="width: 300px; height: 100px;" wrap="VIRTUAL" name="task"></textarea><br>
				</td>
			</tr>
			<tr>
			  	<td align="center" colspan="2" valign="top">
			  		<div id="sw-captcha" class="captcha"><a onclick="reload(); return false;" href="#"><img src="'.HTTP_HOME_URL.'/module/antibot.php" alt="Код безопасности" border="0" /></a></div>
					<input type="text" name="captcha" maxlength="10" style="width: 50px"><br>
					<input type="submit" name="send" value="'.$lang["send"].'" class="button">
				</td>
			</tr>
			</table>
			</form>
			</div>';
        }
		
        if ( $_act == "write" ) {
            $_tid = intval( $_REQUEST["s"] );
            if ( isset($_POST["send"]) ) {
                $t_comment = strip_tags( $_POST["comment"], "\n" );
                $t_captcha = strtoupper( $db->safe($_POST["captcha"]) );
                if ( !isset($t_comment) or empty($t_comment) or !isset($t_captcha) or empty($t_captcha) )
                    $tpl->ShowError( $lang['error'], $lang["ticket_8"] );
                elseif ( $t_captcha != $_SESSION["seccode"] or !isset($_SESSION["seccode"]) )
                    $tpl->ShowError( $lang['error'], $lang["err_code"] );
                elseif ( strlen($t_comment) < 5 )
                    $tpl->ShowError( $lang['error'], "Слишком короткое сообщение" );
                elseif ( strlen($t_comment) > 1000 )
                    $tpl->ShowError( $lang['error'], $lang["ticket_10"] );
                else {
                    $t_comment = $db->safe( str_replace("\n", "<br />", $t_comment) );
                    $db->query( "INSERT INTO `stress_ticket_comments` SET `tid`='{$_tid}',`author`='".$controller->GetName()."',`comment`='{$t_comment}', `date` = '".$db->safe(time())."'" );
                    
					$controller->redirect();
                }
            }
            $quer = $db->query( "SELECT a.*,b.section AS section FROM stress_ticket_task AS a LEFT JOIN stress_ticket_section AS b ON a.sid=b.id WHERE a.id='{$_tid}' AND a.login='{$controller->GetName()}' AND server IN ({$serversIN})" );
            if ( $db->num_rows($quer) > 0 ) {
                $n = 0;
                $data = $db->fetch( $quer );
                $data["date"] = date( "Y.m.d H:i:s", $data["date"] );
                $quer = $db->query( "SELECT * FROM stress_ticket_comments WHERE tid='{$_tid}' ORDER BY date ASC" );
                while ( $comm = $db->fetch($quer) ) {
                    $comm["date"] = date( "Y.m.d H:i:s", $comm["date"] );
                    $trClass = $n++ % 2 ? "":"trRowA";
                    $entries .= "
					<tr>
						<td align='center' class='td1 {$trClass}' valign='top'>
							<small>{$comm["date"]}</small><br /> <b>{$comm["author"]}</b>
						</td>
						<td align='left' class='td2 trRowA2'>{$comm["comment"]}</td>
					</tr>
					";
                }
                $profile = <<< HTML
				<br />
				<table width="100%" border='0' cellpadding='3' cellspacing='0' id="swsupport2">
				<tr>
				    <th colspan='2' align='left'><b>{$data["section"]} -> {$data["title"]}</b><hr></th>
				</tr>
				<tr>
					<th class='td1' align='center' width='120' valign="top">
						<br /><small>{$data["date"]}</small><br /><b>{$data["login"]} ({$data["name"]})</b><br /><br />
					</th>
					<th class='td2' align='left'>{$data["task"]}</th>
				</tr>
				{$entries}
				</table><br />
HTML;
                if ( $data["status"] != 3 ) {
                    $SUP_ADDR = HTTP_HOME_URL;
                    $profile .= <<< HTML
					<script type="text/javascript">//<![CDATA[
function reload () {
	var rndval = new Date().getTime(); 
	document.getElementById('sw-captcha').innerHTML = '<a onclick="reload(); return false;" href="#"><img src="{$SUP_ADDR}/module/antibot.php?rndval=' + rndval + '" border="0"></a>';
};
//]]></script>
					<form action="" method="post">
					<input type="hidden" name="s" value="{$_tid}" />
					<table width="100%" cellpadding="3" cellspacing="3" class="supForm">
					<tr>
					  	<td align="center">
					  		Сообщение:<br>
					  		<textarea style="width: 80%; height: 100px;" wrap="VIRTUAL" name="comment"></textarea><br>
						</td>
					</tr>
					<tr>
					  	<td align="center" valign="top">
					  		<div id="sw-captcha" class="captcha"><a onclick="reload(); return false;" href="#"><img src="{$SUP_ADDR}/module/antibot.php" alt="Код безопасности" border="0" /></a></div>
							<input type="text" name="captcha" maxlength="10" style="width: 50px; height: 20px;">
						</td>
					</tr>
					<tr>
					  	<td align="center" valign="top">
					  		<input type="submit" name="send" value="{$lang["send"]}" class="button">
						</td>
					</tr>
					</table>
					</form>
HTML;
                }
            } else {
                $profile = "<div class='error'>No Records</div>";
            }
        }
    }
} else
    exit;
?>